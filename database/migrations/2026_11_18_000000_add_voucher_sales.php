<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_view_sales')->default(false);
        });

        Schema::create('voucher_sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 80)->unique();
            $table->string('voucher_code');
            $table->string('voucher_name');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total');
            $table->date('paid_on')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_sales');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_view_sales');
        });
    }
};
