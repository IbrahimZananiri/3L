<?php
use Jenssegers\Mongodb\Model as Eloquent;

class BookDocument extends Eloquent {

    protected $connection = 'mongodb';
    protected $collection = 'books';

	protected $guarded = array();

}