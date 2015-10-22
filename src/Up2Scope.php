<?php namespace Teepluss\Up2;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;

class Up2Scope implements ScopeInterface 
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function apply(Builder $builder)
    {
        $model = $builder->getModel();

        $builder->whereNull('created_at');
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function remove(Builder $builder)
    {
        // $column = $builder->getModel()->getQualifiedDeletedAtColumn();

        // $query = $builder->getQuery();

        // foreach ((array) $query->wheres as $key => $where)
        // {
        //     // If the where clause is a soft delete date constraint, we will remove it from
        //     // the query and reset the keys on the wheres. This allows this developer to
        //     // include deleted model in a relationship result set that is lazy loaded.
        //     if ($this->isSoftDeleteConstraint($where, $column))
        //     {
        //         unset($query->wheres[$key]);

        //         $query->wheres = array_values($query->wheres);
        //     }
        // }
    }

}
