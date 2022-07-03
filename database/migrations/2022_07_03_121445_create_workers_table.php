<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id');
            $table->string('worker_id');
            $table->string('worker_name');
            $table->integer('date');
            $table->float('hashrate');
            $table->float('reject');
            $table->primary(['worker_id', 'date']);
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
        Schema::dropIfExists('workers');
    }
};
