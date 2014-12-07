<?php

class BookCacheStore extends DataStoreBase implements DataStoreInterface {

    public function get($id)
    {
        $cacheKey = $this->getKeyForId($id);
        $cachedBookData = Cache::get($cacheKey);
        return $cachedBookData ? $cachedBookData : null;
    }

    public function put($id, $data)
    {
        $cacheKey = $this->getKeyForId($id);
        Cache::put($cacheKey, $data, $this->getCacheMinutes());
    }

    public function delete($id)
    {
        Cache::forget($this->getKeyForId($id));
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

}