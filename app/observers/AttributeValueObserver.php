<?php

class AttributeValueObserver {

    public function updated($model)
    {
        Log::info('AttributeValueObserver: Attribute Value '.$model->id.' for Book '.$model->book_id.' was updated.');
        BookManager::getInstance()->invalidateAllForId($model->book_id);
    }

    public function deleted($model)
    {
        Log::info('AttributeValueObserver: Attribute Value '.$model->id.' for Book '.$model->book_id.' was deleted.');
        BookManager::getInstance()->invalidateAllForId($model->book_id);
    }


}