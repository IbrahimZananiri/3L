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

    protected $_primaryStore = 'BookDatabaseStore'; // default primary store, persistent, the database

    public function getPrimaryStore()
    {
        return $this->_primaryStore;
    }

    public function setPrimaryStore($store)
    {
        $this->_primaryStore = $store;
    }

    // Ordered array of stores class names, in sequenece of required retrieval and cacheing
    protected $_secondaryStores = array(
        'BookCacheStore',
        'BookDocumentStore',
    );

    // using store class names
    public function setSecondaryStores($stores)
    {
        $this->_secondaryStores = $stores;
    }

    public function getSecondaryStores()
    {
        return $this->_secondaryStores;
    }

    // Factory for Store
    public function createStoreInstance($storeName, $options = array())
    {
        return new $storeName($options);
    }

    // for Tests
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
     * Attempts to locate in secondary stores by sequence, and relational database other.
     * Stores book data in secondary stores (document store and/or cache) as needed.
     *
     * @param  int  $id
     * @return array|null
     */
    public function findBookDataById($id)
    {

        $secondaryStores = $this->getSecondaryStores();
        $cachedData = null;
        $previousStores = array();

        foreach ($secondaryStores as $storeName) {

            $store = $this->createStoreInstance($storeName);
            $cachedData = $store->get($id);
            if ($cachedData)
            {
                Log::info('BookManager: Retrieved Book '.$id.' through: '.$storeName);
                $this->setLastSource($storeName);
                foreach ($previousStores as $previousStore) {
                    $previousStore->put($id, $cachedData);
                }
                return $cachedData;
            } else {
                $previousStores[] = $store;
            }

        }

        $primaryStore = $this->createStoreInstance($this->getPrimaryStore());

        $data = $primaryStore->get($id);
        if ($data) {
            foreach ($secondaryStores as $storeName) {
                $store = $this->createStoreInstance($storeName);
                $store->put($id, $data);
            }
            $this->setLastSource($this->getPrimaryStore());
            Log::info('BookManager: Retrieved Book '.$id.' through: '.$this->getPrimaryStore());
            return $data;
        }

        $this->setLastSource(null);
        return null;

    }



    public function invalidateAllForId($id)
    {
        $stores = $this->getSecondaryStores();
        foreach ($stores as $storeName) {
            $store = $this->createStoreInstance($storeName);
            $store->delete($id);
        }
    }


}