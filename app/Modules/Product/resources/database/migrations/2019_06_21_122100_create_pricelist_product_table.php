<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricelistProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricelist_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pricelist_id');
            $table->bigInteger('product_id');
            $table->decimal('value',8,2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pricelist_product', function (Blueprint $table) {
            //
        });
    }
}
