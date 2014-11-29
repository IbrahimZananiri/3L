<?php

class BookManager {

    // Singleton
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance)
        {
            $instance = new static();
        }
        return $instance;
    }

    // Cache TTL in minutes
    protected $_cacheMinutes = 10;

    public function getCacheMinutes()
    {
        return $this->_cacheMinutes;
    }

    public function setCacheMinutes($minutes)
    {
        $this->_cacheMinutes = $minutes;
    }

    public function getCacheKeyForId($id)
    {
        return 'object:'.(new Book)->getTable().':'.$id;
    }

    protected $_lastSource;

    public function getLastSource()
    {
        return $this->_lastSource;
    }

    public function setLastSource($name)
    {
        $this->_lastSource = $name;
    }



    /**
     * Find a Book by ID.
     * Attempts to locate in cache, document store, then relational database.
     * Stores book data in different layers (document store and/or cache) as needed.
     *
     * @param  int  $id
     * @return array|null
     */
    public function findBookDataById($id)
    {
        $cacheKey = $this->getCacheKeyForId($id);
        $cachedBookData = Cache::get($cacheKey);
        if ($cachedBookData)
        {
            Log::info('Book Manager: Retrieved Book '.$id.' from Cache');
            $this->setLastSource('cache');
            return $cachedBookData;
        }
        elseif ($bookDocument = BookDocument::find($cacheKey))
        {
            Log::info('Book Manager: Retrieved Book '.$id.' from Document Store');
            $bookDocumentData = $bookDocument->toArray();
            // for consistency, clear mongodb's _id, it is only used for retrieval from the books collection
            unset($bookDocumentData[$bookDocument->getKeyName()]);
            Log::info('Book Manager: Storing Book '.$id.' in Cache');
            Cache::put($cacheKey, $bookDocumentData, $this->getCacheMinutes());
            $this->setLastSource('document');
            return $bookDocumentData;
        }
        else
        {
            Log::info('Book Manager: Retrieving Book '.$id.' from Relational Database');
            $book = Book::with(array('attributeValues', 'attributeValues.attribute'))->findOrFail($id);

            Log::info('Book Manager: Storing Book '.$id.' in Document Store');
            $bookData = $book->toArray();
            $bookDocument = new BookDocument($book->toArray());
            // Use cache key for book document primary key in books collection
            $bookDocument->setAttribute($bookDocument->getKeyName(), $cacheKey);
            $bookDocument->save();

            Log::info('Book Manager: Storing Book '.$id.' in Cache');
            Cache::put($cacheKey, $bookData, $this->getCacheMinutes());
            $this->setLastSource('database');
            return $bookData;
        }
    }


    public function invalidateCacheForId($id)
    {
        Log::info('Book Manager: Invalidated Book '.$id.' from Cache');
        Cache::forget(static::getCacheKeyForId($id));
    }

    public function invalidateIndexDocumentForId($id)
    {
        Log::info('Book Manager: Invalidated Book '.$id.' from Document Store');
        BookDocument::where((new BookDocument)->getKeyName(), '=', static::getCacheKeyForId($id))->delete();
    }

    public function invalidateAllForId($id)
    {
        $this->invalidateCacheForId($id);
        $this->invalidateIndexDocumentForId($id);
    }


}