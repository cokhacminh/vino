<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('algorithm_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
        });

        // Insert default settings
        DB::table('algorithm_settings')->insert([
            'key' => 'transfer_order_algo',
            'value' => json_encode([
                'markup' => 20,
                'tiers' => [
                    ['from' => 0,       'to' => 1000000,   'maxQty' => 999, 'minSP' => 1, 'priority' => 'random'],
                    ['from' => 1000000,  'to' => 4000000,   'maxQty' => 4,   'minSP' => 2, 'priority' => 'high'],
                    ['from' => 4000000,  'to' => 8000000,   'maxQty' => 5,   'minSP' => 3, 'priority' => 'high'],
                    ['from' => 8000000,  'to' => 999999999, 'maxQty' => 10,  'minSP' => 3, 'priority' => 'high'],
                ],
            ]),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('algorithm_settings');
    }
};
