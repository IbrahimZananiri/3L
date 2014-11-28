<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	$latestBook = Book::orderBy('id', 'desc')->first();
	return View::make('hello')->with('book', $latestBook);
});


Route::group(array('prefix' => 'api'), function()
{
	Route::get('books/{id}', function($id)
	{
		try {
			$bookData = BookManager::getInstance()->findBookDataById($id);
			$responseCode = 200;
			$responseData = $bookData;
		} catch (Exception $e) {
			$responseData = array('error' => 'Book not found');
			$responseCode = 404;
		} finally {
			return Response::json($responseData, $responseCode);
		}
	});

	Route::get('analytics/{type}', function($type)
	{
		try {
			$options = array(
				'type'			=> $type,
				'action' 		=> Input::get('action', 'created'),
				'groupBy' 		=> Input::get('groupBy', 'user_id'),
				'orderBy'	 	=> Input::get('orderBy', 'count'),
				'orderDirection'=> Input::get('orderDirection', 'desc'),
				'dateStart' 	=> Input::get('dateStart'),
				'dateEnd' 		=> Input::get('dateEnd'),
				'with' 			=> Input::get('with', str_replace('_id', '', Input::get('groupBy', 'user_id'))),
			);

			$collection = Interaction::analytics($options);

			$interactions = $collection->get();

			$responseCode = 200;
			$responseData = $interactions->toArray();

		} catch (Exception $e) {
			$responseData = array('error' => 'Object not found');
			$responseCode = 404;
		} finally {
			return Response::json($responseData, $responseCode);
		}
	});

});