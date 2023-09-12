<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('expiration_time', 255)->default(null);
            $table->string('usage', 255)->default(null);
            $table->string('authorization', 255)->default(null);
            $table->string('payment', 255)->default(null);
            $table->string('consent', 255)->default(null);
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
        Schema::dropIfExists('billing_settings');
    }
}
