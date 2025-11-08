<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons');
            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->string('user_email')->index();
            $table->integer('discount_amount_in_cents');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('coupon_usages');
    }
};