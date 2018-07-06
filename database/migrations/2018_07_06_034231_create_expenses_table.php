<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('user_displayName');
            $table->integer('family_id')->unsigned();
            $table->float('expense_amount');
            $table->integer('expense_category');
            $table->string('expense_category_name');
            $table->mediumText('expense_description');
            $table->timestamp('expense_date');
            $table->timestamps();

            // Key Constratins
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('family_id')->references('id')->on('families');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
