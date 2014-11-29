<?php

class Interaction extends Eloquent {

    public function user()
    {
        return $this->belongsTo('user');
    }

    public function interactable()
    {
        return $this->morphTo();
    }

    public function relatable()
    {
        return $this->morphTo();
    }

    public static function analytics($options)
    {
        $type = $options['type'];
        $action = $options['action'];
        $groupBy = $options['groupBy'];
        $orderBy = $options['orderBy'];
        $orderDirection = $options['orderDirection'];
        $dateStart = $options['dateStart'];
        $dateEnd = $options['dateEnd'];
        $with = $options['with'];

        $collection = Interaction::where(array((new $type)->interactions()->getMorphType() => (new $type)->getMorphClass()));

        $collection = $collection->where(array('action' => $action));

        if ($dateStart)
            $collection = $collection->where('created_at', '>=', $dateStart);
        if ($dateEnd)
            $collection = $collection->where('created_at', '<=', $dateEnd);

        $collection = $collection->groupBy($groupBy);

        $properties = array('interactable_id', 'interactable_type', 'relatable_id', 'relatable_type', DB::raw('COUNT(*) as count'));
        if (!in_array($groupBy, $properties))
        {
            $properties[] = $groupBy;
        }
        $collection = $collection->select($properties);

        $collection = $collection->orderBy($orderBy, $orderDirection);

        $collection = $collection->with($with);

        return $collection;
    }
}