<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInteractionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('interactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();

			// the interactor, i.e. the user
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			// polymorphic, object that was interacted with
			$table->integer('interactable_id')->unsigned();
			$table->string('interactable_type');

			$table->string('action');

			// for extra relation ids, used to store attribute_id when AttributeValue is touched
			$table->integer('relatable_id')->unsigned()->nullable();
			$table->string('relatable_type')->nullable();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('interactions');
	}

}
