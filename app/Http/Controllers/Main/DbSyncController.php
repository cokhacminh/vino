<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DbSyncController extends Controller
{
    /**
     * Kiểm tra có phải localhost không
     */
    private function isLocalhost()
    {
        return app()->environment('local');
    }

    /**
     * Trang chính Đồng Bộ DB
     */
    public function index()
    {
        if (!$this->isLocalhost()) {
            abort(403, 'Tính năng này chỉ hoạt động trên localhost.');
        }

        $remoteConfig = [
            'host'     => env('REMOTE_DB_HOST', ''),
            'port'     => env('REMOTE_DB_PORT', '3306'),
            'database' => env('REMOTE_DB_DATABASE', ''),
            'username' => env('REMOTE_DB_USERNAME', ''),
            'password' => env('REMOTE_DB_PASSWORD', ''),
        ];

        return view('main.db-sync.index', compact('remoteConfig'));
    }

    /**
     * Test kết nối remote
     */
    public function testConnection(Request $request)
    {
        if (!$this->isLocalhost()) {
            return response()->json(['success' => false, 'message' => 'Chỉ hoạt động trên localhost.'], 403);
        }

        $host     = $request->input('host');
        $port     = $request->input('port', 3306);
        $database = $request->input('database');
        $username = $request->input('username');
        $password = $request->input('password');

        // Cập nhật config động
        Config::set('database.connections.mysql_remote', [
            'driver'      => 'mysql',
            'host'        => $host,
            'port'        => $port,
            'database'    => $database,
            'username'    => $username,
            'password'    => $password,
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => false,
            'engine'      => null,
            'options'     => [],
        ]);

        // Xóa connection cache cũ
        DB::purge('mysql_remote');

        try {
            DB::connection('mysql_remote')->getPdo();
            $tables = DB::connection('mysql_remote')
                ->select('SHOW TABLES');

            $tableKey = 'Tables_in_' . $database;
            $tableList = array_map(fn($t) => (array)$t, $tables);
            $tableNames = array_map(fn($t) => $t[$tableKey] ?? array_values($t)[0], $tableList);

            // Lưu config vào env session để dùng cho sync
            session([
                'remote_db' => [
                    'host'     => $host,
                    'port'     => $port,
                    'database' => $database,
                    'username' => $username,
                    'password' => $password,
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kết nối thành công!',
                'tables'  => $tableNames,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kết nối thất bại: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Đồng bộ các bảng được chọn từ remote về local
     */
    public function sync(Request $request)
    {
        if (!$this->isLocalhost()) {
            return response()->json(['success' => false, 'message' => 'Chỉ hoạt động trên localhost.'], 403);
        }

        $tables = $request->input('tables', []);
        if (empty($tables)) {
            return response()->json(['success' => false, 'message' => 'Không có bảng nào được chọn.']);
        }

        $remoteDb = session('remote_db');
        if (!$remoteDb) {
            return response()->json(['success' => false, 'message' => 'Chưa kết nối remote DB. Hãy test kết nối trước.']);
        }

        // Thiết lập lại connection
        Config::set('database.connections.mysql_remote', [
            'driver'    => 'mysql',
            'host'      => $remoteDb['host'],
            'port'      => $remoteDb['port'],
            'database'  => $remoteDb['database'],
            'username'  => $remoteDb['username'],
            'password'  => $remoteDb['password'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
            'options'   => [],
        ]);
        DB::purge('mysql_remote');

        $results = [];

        foreach ($tables as $table) {
            try {
                // Lấy dữ liệu từ remote
                $rows = DB::connection('mysql_remote')->table($table)->get()->toArray();
                $rows = array_map(fn($r) => (array)$r, $rows);

                // Xóa dữ liệu cũ ở local và insert mới
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table($table)->truncate();

                if (!empty($rows)) {
                    // Chunk để tránh quá tải
                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                }

                DB::statement('SET FOREIGN_KEY_CHECKS=1');

                $results[] = [
                    'table'   => $table,
                    'success' => true,
                    'rows'    => count($rows),
                    'message' => 'Đã đồng bộ ' . count($rows) . ' bản ghi.',
                ];
            } catch (\Exception $e) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                $results[] = [
                    'table'   => $table,
                    'success' => false,
                    'rows'    => 0,
                    'message' => 'Lỗi: ' . $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}
