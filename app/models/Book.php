<?php

class Book extends Eloquent {

	use InteractableTrait;
	protected $guarded = array();

	public function attributeValues()
	{
		return $this->hasMany('AttributeValue');
	}
	public function createAttributeValue($attributeName, $value)
	{
	    	// Find the Attribute instance by name (i.e. by code), with Attribute's method: findByName
	    	$attribute = Attribute::findByNameOrFail($attributeName);
	
	    	// Check if Attribute allows multiple values for the same entity. If yes, just save a new AttributeValue
	    	if ($attribute->is_multiple)
	    	{
		    	$attributeValue = new AttributeValue(array('attribute_id' => $attribute->id, 'value' => $value, 'user_id' => $this->id));
		    	return $this->attributeValues()->save($attributeValue);
	    	}
	
	    	// Otherwise, find the correct AttributeValue if exists, or create a new instance, set its value, and finally save the AttributeValue model.
	    	else
	    	{
	    		$attributeValue = AttributeValue::firstOrNew(array('attribute_id' => $attribute->id, 'book_id' => $this->id, 'user_id' => $this->id));
	    		$attributeValue->value = $value;
	    		return $attributeValue->save();
	    	}
	}
	
	public static function boot()
	{
		parent::boot();
		Book::observe(new BookObserver);
	}

}
