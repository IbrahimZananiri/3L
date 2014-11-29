<?php

trait InteractableTrait {

    /**
     * Define the interactions relation, on "interactable" models that uses this trait
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     *
     */
    public function interactions()
    {
        return $this->morphMany('Interaction', 'interactable');
    }

    public function relatedInteractions()
    {
        return $this->morphMany('Interaction', 'relatable');
    }

    public static function bootInteractableTrait()
    {


        // Attach to created event, create an Interaction for given object and user
        static::created(function($model)
        {
            $interaction = new Interaction;
            $interaction->action = 'created';
            if (isset($model->user_id))
            {
                $interaction->user_id = $model->user_id;
            }
            if (isset($model->interactableRelatedId) && isset($model->interactableRelatedType))
            {
                $interaction->setAttribute($interaction->relatable()->getForeignKey(), $model->getAttribute($model->interactableRelatedId));
                $interaction->setAttribute($model->relatedInteractions()->getPlainMorphType(), $model->interactableRelatedType);
            }
            $model->interactions()->save($interaction);
        });


        // Attach to updated event, create an Interaction for given object and user
        static::updated(function($model)
        {
            $interaction = new Interaction;
            $interaction->action = 'updated';
            if (isset($model->user_id))
            {
                $interaction->user_id = $model->user_id;
            }
            if (isset($model->interactableRelatedId) && isset($model->interactableRelatedType))
            {
                $interaction->setAttribute($interaction->relatable()->getForeignKey(), $model->getAttribute($model->interactableRelatedId));
                $interaction->setAttribute($model->relatedInteractions()->getPlainMorphType(), $model->interactableRelatedType);
            }
            $model->interactions()->save($interaction);
        });


        // Attach to deleted event, delete all Interaction(s) for given object
        static::deleted(function($model)
        {
            Interaction::where(array($model->interactions()->getMorphType() => $model->getMorphClass(), 'interactable_id' => $model->id))->delete();

        });


    }

}
