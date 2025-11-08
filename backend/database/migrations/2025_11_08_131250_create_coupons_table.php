<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->integer('discount_value');
            $table->timestamp('valid_until')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->string('compatible_periodicity')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('plans');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('coupons');
    }
};