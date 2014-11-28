<?php

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;

class AttributeScope implements ScopeInterface {

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function apply(Builder $builder)
	{
		$model = $builder->getModel();
		$query = $builder->getQuery();

		// $model::with('Attribute');

		// $builder->with('attribute');

		$attributeTable = (new Attribute)->getTable();
		$query->join($attributeTable, $attributeTable.'.id', '=', $model->getQualifiedAttributeIdColumn(), 'inner');



	}

	public function remove(Builder $builder)
	{

	}

}
