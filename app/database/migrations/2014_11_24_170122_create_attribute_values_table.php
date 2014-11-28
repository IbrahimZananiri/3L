<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attribute_values', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->timestamps();
			
			// Consider polymorphic relationship? No...
			$table->integer('book_id')->unsigned();
			$table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');

			$table->integer('attribute_id')->unsigned();
			$table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');

			$table->string('value');

			// creator
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attribute_values');
	}

}
