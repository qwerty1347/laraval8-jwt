<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNgmApiKeyManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ngm_apiKeyManagement', function (Blueprint $table) {
            $table->increments('no');
            $table->string('id', 40);
            $table->text('apiKey');
            $table->dateTime('dateExpire')->nullable();
            $table->dateTime('dateReg')->nullable();
            $table->dateTime('dateUpdate')->nullable();
            $table->text('memo')->nullable();

            $table->index('id');
        });

        DB::statement("ALTER TABLE ngm_apiKeyManagement COMMENT 'API 키 관리'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ngm_apiKeyManagement');
    }
}
