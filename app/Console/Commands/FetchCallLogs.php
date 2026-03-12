<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchCallLogs extends Command
{
    protected $signature = 'call:fetch-today';
    protected $description = 'Lấy toàn bộ lịch sử cuộc gọi hôm nay từ eTelecom API và lưu vào database';

    private $token = 'shop1415713146516250402:99jzsRpRvE92w0et2KThHscAZeSL4Mg1PMzeT11h74dectynmD4rvPmvVJjwGCRJ';

    public function handle()
    {
        $today = now()->format('Y-m-d');
        $todayDisplay = now()->format('d/m/Y');

        $this->info("========================================");
        $this->info("  LỊCH SỬ CUỘC GỌI NGÀY {$todayDisplay}");
        $this->info("========================================");

        // Load extension → MaNV map từ database
        $extensionMap = DB::table('dienthoai')
            ->whereNotNull('MaNV')
            ->pluck('MaNV', 'extension')
            ->toArray();

        $this->info("  Loaded " . count($extensionMap) . " extension(s) từ database");

        $dateFrom = $today . 'T00:00:00.000Z';
        $dateTo = $today . 'T23:59:59.000Z';

        $allLogs = [];
        $newCount = 0;
        $skipCount = 0;
        $after = '';
        $page = 1;

        while (true) {
            $this->info("\n→ Đang tải trang {$page}...");

            $postData = json_encode([
                'filter' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ],
                'paging' => [
                    'limit' => 100,
                    'after' => $after,
                ],
            ]);

            $response = $this->callApi($postData);

            if ($response === null) {
                $this->error('✗ Lỗi kết nối API!');
                break;
            }

            $logs = $response['call_logs'] ?? [];
            $paging = $response['paging'] ?? [];

            if (empty($logs)) {
                $this->info('  Không có thêm dữ liệu.');
                break;
            }

            // Kiểm tra add_time của từng record
            $stopped = false;
            foreach ($logs as $log) {
                // Lấy add_time từ call_targets
                $addTime = '';
                if (!empty($log['call_targets']) && isset($log['call_targets'][0]['add_time'])) {
                    $addTime = $log['call_targets'][0]['add_time'];
                }

                // Kiểm tra add_time có phải hôm nay không
                if ($addTime) {
                    try {
                        $addDate = (new \DateTime($addTime))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $addDate = substr($addTime, 0, 10);
                    }

                    if ($addDate !== $today) {
                        $this->warn("  ⚠ Phát hiện add_time = {$addTime} (khác ngày hôm nay). Dừng lại!");
                        $stopped = true;
                        break;
                    }
                }

                $id = $log['id'] ?? '';
                $caller = $log['caller'] ?? '';
                $callee = $log['callee'] ?? '';
                $direction = $log['direction'] ?? '';
                $startedAt = $this->parseDateTime($log['started_at'] ?? '');

                // Xác định extension dựa vào direction
                // in: callee là extension | out: caller là extension
                $ext = ($direction === 'in') ? $callee : $caller;
                $maNV = $extensionMap[$ext] ?? null;

                $record = [
                    'id' => $id,
                    'MaNV' => $maNV,
                    'started_at' => $startedAt,
                    'caller' => $caller,
                    'callee' => $callee,
                    'direction' => $direction,
                    'call_state' => $log['call_state'] ?? '',
                    'duration' => $log['duration'] ?? 0,
                ];

                $allLogs[] = $record;

                // Kiểm tra và lưu vào database
                if ($id && !DB::table('etelecom_call_logs')->where('id', $id)->exists()) {
                    DB::table('etelecom_call_logs')->insert($record);
                    $newCount++;
                } else {
                    $skipCount++;
                }
            }

            $this->info("  ✓ Đã lấy " . count($logs) . " bản ghi (tổng: " . count($allLogs) . ")");

            if ($stopped) {
                break;
            }

            // Kiểm tra paging.next
            $next = $paging['next'] ?? '';
            if (empty($next)) {
                $this->info('  Hết dữ liệu (không còn trang tiếp).');
                break;
            }

            $after = $next;
            $page++;
        }

        // Hiển thị kết quả
        $this->newLine();
        $this->info("========================================");
        $this->info("  TỔNG: " . count($allLogs) . " cuộc gọi");
        $this->info("  MỚI LƯU: {$newCount} | ĐÃ TỒN TẠI: {$skipCount}");
        $this->info("========================================");
        $this->newLine();

        if (!empty($allLogs)) {
            // Lấy tên nhân viên để hiển thị
            $userNames = DB::table('users')->pluck('name', 'id')->toArray();

            $displayData = array_map(function ($r) use ($userNames) {
                return [
                    $r['id'],
                    $r['MaNV'] ? ($userNames[$r['MaNV']] ?? $r['MaNV']) : '—',
                    $this->formatTime($r['started_at']),
                    $r['caller'],
                    $r['callee'],
                    $r['direction'],
                    $r['call_state'],
                ];
            }, $allLogs);

            $this->table(
                ['ID', 'Nhân Viên', 'Gọi Lúc', 'Caller', 'Callee', 'Direction', 'Call State'],
                $displayData
            );
        }

        // Thống kê
        $collection = collect($allLogs);
        $answered = $collection->where('call_state', 'answered')->count();
        $notAnswered = $collection->where('call_state', 'not_answered')->count();
        $unknown = $collection->where('call_state', 'unknown')->count();
        $outbound = $collection->where('direction', 'out')->count();
        $inbound = $collection->where('direction', 'in')->count();

        $this->newLine();
        $this->info("--- THỐNG KÊ ---");
        $this->info("  Tổng cuộc gọi: " . count($allLogs));
        $this->info("  Gọi đi: {$outbound} | Gọi nhận: {$inbound}");
        $this->info("  Nghe máy: {$answered} | Không nghe: {$notAnswered} | Unknown: {$unknown}");

        return Command::SUCCESS;
    }

    private function callApi(string $postData): ?array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.etelecom.vn/v1/partner.Etelecom/ListCallLogs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $httpCode >= 400) {
            return null;
        }

        return json_decode($response, true);
    }

    private function parseDateTime(string $t): ?string
    {
        if (!$t) return null;
        try {
            $d = new \DateTime($t);
            $d->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'));
            return $d->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $t;
        }
    }

    private function formatTime(?string $t): string
    {
        if (!$t) return '';
        try {
            $d = new \DateTime($t);
            return $d->format('H:i:s - d/m/Y');
        } catch (\Exception $e) {
            return $t;
        }
    }
}
