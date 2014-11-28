<?php

class Attribute extends Eloquent {

	use InteractableTrait;

	public $timestamps = false;

	public static function findByNameOrFail($name)
	{
		return Attribute::where('name', '=', $name)->firstOrFail();
	}

	public function attributeValues()
	{
		return $this->hasMany('AttributeValue');
	}

}