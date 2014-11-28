<?php

class BookObserver {
	
	public function saved($model)
	{
		Log::info('BookObserver: Book '.$model->id.' was saved.');
	}

	public function created($model)
	{
		Log::info('BookObserver: Book '.$model->id.' was created.');
	}

	public function updated($model)
	{
		Log::info('BookObserver: Book '.$model->id.' was updated.');
		BookManager::getInstance()->invalidateAllForId($model->id);
	}

	public function deleted($model)
	{
		Log::info('BookObserver: Book '.$model->id.' was deleted.');
		BookManager::getInstance()->invalidateAllForId($model->id);
	}

}