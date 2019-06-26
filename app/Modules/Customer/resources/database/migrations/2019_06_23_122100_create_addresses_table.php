<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('line1')->nullable();
            $table->string('line2')->nullable();
            $table->string('tel')->nullable();
            $table->string('pin')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->bigInteger('customer_id');
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
        Schema::table('addresses', function (Blueprint $table) {
            //
        });
    }
}
