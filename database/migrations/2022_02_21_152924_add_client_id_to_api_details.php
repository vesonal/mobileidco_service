<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToApiDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_details', function (Blueprint $table) {
             $table->string('client_id', 80)->after('api_url')
                        ->nullable()
                        ->default(null);
             $table->string('payload', 2555)->after('api_url')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_details', function (Blueprint $table) {
            //
        });
    }
}
