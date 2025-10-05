<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique();
            $table->unsignedBigInteger('vendor_id');
            $table->date('purchase_date');
            $table->decimal('total_amount', 12, 2);
            $table->text('notes')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        // Add foreign key constraint after table creation
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};