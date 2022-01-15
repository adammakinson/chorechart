<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChoreAndPointsToUserchores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_chores', function (Blueprint $table) {
            $table->string('chore');
            $table->integer('pointvalue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('userchores', function (Blueprint $table) {
            $table->dropColumn('chore');
            $table->dropColumn('pointvalue');
        });
    }
}
