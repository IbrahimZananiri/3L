<?php

class AttributeValue extends Eloquent {

    use InteractableTrait;

    // InteractableTrait will use this value to store attribute ID in additional relation id field
    protected $interactableRelatedId = 'attribute_id';
    protected $interactableRelatedType = 'Attribute';


    public function attribute()
    {
        return $this->belongsTo('Attribute')->select(array('id', 'name'));
    }

    public function book()
    {
        return $this->belongsTo('Book');
    }

    public static function boot()
    {
        parent::boot();
        static::observe(new AttributeValueObserver);
    }

}
