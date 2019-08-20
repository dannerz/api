<?php

namespace Dannerz\Api\Models;

trait SortsByParentColumn
{
    public function scopeSortByParentColumn($query, $property, $descending)
    {
        $parts = explode('.', $property);

        $model = $query->getModel();
        $relation = $model->{$parts[0]}();
        $table = $model->getTable();
        $parentModel = $relation->getModel();
        $parentTable = $parentModel->getTable();
        $id = $parentModel->getKeyName();
        $foreignKey = $relation->getForeignKeyName();
        $column = $parts[1];

        return $query
            ->select($table.'.*')
            ->leftJoin($parentTable, $table.'.'.$foreignKey, '=', $parentTable.'.'.$id)
            ->orderBy($parentTable.'.'.$column, $descending ? 'desc' : 'asc');
    }
}