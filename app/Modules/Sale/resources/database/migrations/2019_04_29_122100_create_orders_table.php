<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ref')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('salesperson_id')->nullable();
            $table->bigInteger('pricelist_id')->nullable();
            $table->boolean('gst_included');
            $table->string('status')->default('processing');
            $table->bigInteger('customer_id')->nullable();
            $table->decimal('discount',8,2)->default(0.00);
            $table->decimal('total',8,2)->default(0.00);
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
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
