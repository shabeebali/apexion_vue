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
            $table->string('slug')->unique();
            $table->decimal('gst');
            $table->decimal('weight',8,3)->default(0.000);
            $table->string('hsn')->nullable();
            $table->decimal('mrp',8,2)->default(0.00);
            $table->decimal('landing_price')->default(0.00);
            $table->decimal('general_selling_price')->default(0.00);
            $table->decimal('general_selling_dealer')->default(0.00);
            $table->text('remarks')->nullable();
            $table->text('description')->nullable();
            $table->boolean('tally')->default(0);
            $table->boolean('publish')->default(0);
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
