<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserChoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_chores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chore_id')
                ->references('id')->on('chores')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable()
                ->referneces('id')->on('users')->onDelete('cascade');
            $table->timestamp('inspection_ready')->nullable();
            $table->timestamp('inspected_on')->nullable();
            $table->boolean('inspection_passed')->nullable();
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
        Schema::dropIfExists('user_chores');
    }
}
