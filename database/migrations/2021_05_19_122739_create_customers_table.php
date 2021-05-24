<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigInteger('customer_id')->unique();
            $table->text('display_name');
            $table->string('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->double('points', 8, 2)->nullable();
            $table->double('discount_rate', 5, 2)->nullable();
            $table->double('cashback_rate', 5, 2)->nullable();
            $table->string('membership_tier_name')->nullable();
            $table->dateTime('date_created')->nullable();
            $table->dateTime('last_transaction_time')->nullable();
            $table->text('uid')->unique();
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
        Schema::dropIfExists('customers');
    }
}
