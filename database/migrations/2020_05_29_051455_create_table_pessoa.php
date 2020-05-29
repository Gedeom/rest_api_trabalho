<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePessoa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pessoa', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->string('cpf',11);
            $table->date('dt_nascimento');
            $table->string('email');
            $table->timestamps();

            $table->unique('cpf','pessoa_cpf_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pessoa');
    }
}
