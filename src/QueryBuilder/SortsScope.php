<?php

namespace Dannerz\Api\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class SortsScope implements Sort
{
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $scope = studly_case(str_replace('.', '_', $property));

        $scope = 'sortBy'.$scope;

        if (! method_exists($query->getModel(), 'scope'.studly_case($scope))) {
            return $query->sortByParentColumn($property, $descending);
        }

        return $query->$scope($descending);
    }
}
