<?php
// Bootstrap Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check if already seeded
$exists = DB::table('report_templates')->count();
if ($exists > 0) {
    echo "Templates already exist ({$exists}). Skipping.\n";
    exit(0);
}

// Template 1: Kinh Doanh
$t1 = DB::table('report_templates')->insertGetId([
    'name' => 'Báo Cáo Ngày - Kinh Doanh',
    'MaPB' => 1,
    'type' => 'daily',
    'is_active' => true,
    'created_by' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

$fields1 = [
    ['label' => 'Số cuộc gọi hôm nay', 'field_type' => 'number', 'is_required' => true, 'options' => null],
    ['label' => 'Số cuộc gọi thành công', 'field_type' => 'number', 'is_required' => true, 'options' => null],
    ['label' => 'Số đơn hàng tạo mới', 'field_type' => 'number', 'is_required' => true, 'options' => null],
    ['label' => 'Doanh thu dự kiến (VNĐ)', 'field_type' => 'number', 'is_required' => false, 'options' => null],
    ['label' => 'Khách hàng tiềm năng mới', 'field_type' => 'number', 'is_required' => false, 'options' => null],
    ['label' => 'Khó khăn / Vấn đề gặp phải', 'field_type' => 'textarea', 'is_required' => false, 'options' => null],
    ['label' => 'Kế hoạch ngày mai', 'field_type' => 'textarea', 'is_required' => true, 'options' => null],
    ['label' => 'Tự đánh giá', 'field_type' => 'select', 'is_required' => false, 'options' => json_encode(['Tốt', 'Khá', 'Trung bình', 'Yếu'])],
];

foreach ($fields1 as $i => $f) {
    DB::table('report_template_fields')->insert([
        'template_id' => $t1,
        'label' => $f['label'],
        'field_type' => $f['field_type'],
        'options' => $f['options'],
        'is_required' => $f['is_required'],
        'sort_order' => $i,
    ]);
}
echo "Created template: Báo Cáo Ngày - Kinh Doanh (ID: {$t1})\n";

// Template 2: Marketing
$t2 = DB::table('report_templates')->insertGetId([
    'name' => 'Báo Cáo Ngày - Marketing',
    'MaPB' => 5,
    'type' => 'daily',
    'is_active' => true,
    'created_by' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);

$fields2 = [
    ['label' => 'Bài viết đã đăng', 'field_type' => 'number', 'is_required' => true, 'options' => null],
    ['label' => 'Nền tảng', 'field_type' => 'select', 'is_required' => true, 'options' => json_encode(['Facebook', 'Zalo', 'TikTok', 'Website', 'Khác'])],
    ['label' => 'Lượt tương tác (like, share, comment)', 'field_type' => 'number', 'is_required' => false, 'options' => null],
    ['label' => 'Số lead / tin nhắn mới', 'field_type' => 'number', 'is_required' => true, 'options' => null],
    ['label' => 'Chi phí quảng cáo (VNĐ)', 'field_type' => 'number', 'is_required' => false, 'options' => null],
    ['label' => 'Nội dung chính đã làm', 'field_type' => 'textarea', 'is_required' => true, 'options' => null],
    ['label' => 'Kế hoạch ngày mai', 'field_type' => 'textarea', 'is_required' => true, 'options' => null],
];

foreach ($fields2 as $i => $f) {
    DB::table('report_template_fields')->insert([
        'template_id' => $t2,
        'label' => $f['label'],
        'field_type' => $f['field_type'],
        'options' => $f['options'],
        'is_required' => $f['is_required'],
        'sort_order' => $i,
    ]);
}
echo "Created template: Báo Cáo Ngày - Marketing (ID: {$t2})\n";
echo "Done!\n";
