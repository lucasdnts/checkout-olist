<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('cupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); 
        $table->boolean('is_active')->default(true);
        $table->enum('discount_type', ['percentage', 'fixed']); 
        $table->integer('discount_value');
        $table->timestamp('valid_until')->nullable();
        $table->integer('usage_limit')->nullable(); 
        $table->integer('current_usage')->default(0); //Substituir por relação com subCupons
        $table->string('compatible_periodicity')->nullable();
        $table->foreignId('plan_id')->nullable()->constrained('planos'); // null = todos os planos
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupons');
    }
};
