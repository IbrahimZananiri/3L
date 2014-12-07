<?php

abstract class DataStoreBase {

    public function getKeyForId($id)
    {
        return 'object:'.$id;
    }


}