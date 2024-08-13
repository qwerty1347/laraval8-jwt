<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNgmApiKeyErrorLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ngm_apiKeyErrorLog', function (Blueprint $table) {
            $table->increments('no');
            $table->string('id')->comment('회원아이디');
            $table->text('request')->nullable()->comment('요청데이터');
            $table->text('response')->nullable()->comment('응답데이터');
            $table->dateTime('dateReg')->nullable();
            $table->text('memo')->nullable()->comment('기타');

            $table->index('id');
        });

        DB::statement("ALTER TABLE ngm_apiKeyErrorLog COMMENT 'API 키 관리 오류 로그'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ngm_apiKeyErrorLog');
    }
}
