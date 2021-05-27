<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigInteger('id')->unique();
            $table->dateTime('date_created');
            $table->double('points', 9, 2)->nullable();
            $table->double('certificate_points', 9, 2)->nullable();
            $table->double('cash', 9, 2)->nullable();
            $table->double('total', 9, 2)->nullable();
            $table->string('comment')->nullable();

            $table->bigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->string('delivery_type')->nullable();
            $table->bigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->string('delivery_address')->nullable();
            $table->string('delivery_receiver_name')->nullable();
            $table->string('delivery_receiver_phone')->nullable();
            $table->string('delivery_user_comment')->nullable();
            
            $table->boolean('uploaded_to_bitrix')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
