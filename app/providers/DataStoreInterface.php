<?php

interface DataStoreInterface {

    public function get($id);

    public function put($id, $data);

    public function delete($id);

}