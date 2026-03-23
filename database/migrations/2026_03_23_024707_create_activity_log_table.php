<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('action')->default('delete'); // delete
            $table->string('MaDH')->nullable();
            $table->integer('MaNV')->nullable();
            $table->string('TenNV')->nullable();
            $table->bigInteger('TongTien')->default(0);
            $table->bigInteger('GiamGia')->default(0);
            $table->string('Ngay')->nullable(); // ngay don hang
            $table->string('DonHang')->nullable();
            $table->text('khach_hang_data')->nullable(); // JSON khach hang
            $table->text('chi_tiet_data')->nullable(); // JSON chi tiet don hang
            $table->string('deleted_by')->nullable(); // nguoi xoa
            $table->boolean('restored')->default(false);
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
