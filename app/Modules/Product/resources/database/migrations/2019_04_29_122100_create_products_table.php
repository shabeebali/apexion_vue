<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->integer('stock')->default(0)->nullable();
            $table->decimal('weight',8,3)->default(0.000)->nullable();
            $table->string('hsn')->nullable();
            $table->decimal('mrp',8,2)->default(0.00)->nullable();
            $table->decimal('landing_price')->default(0.00)->nullable();
            $table->decimal('general_selling_price')->default(0.00)->nullable();
            $table->decimal('general_selling_dealer')->default(0.00)->nullable();
            $table->text('remarks')->nullable();
            $table->text('description')->nullable();
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
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
