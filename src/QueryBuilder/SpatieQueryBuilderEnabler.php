<?php

namespace Dannerz\Api\QueryBuilder;

use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sort;

class SpatieQueryBuilderEnabler extends QueryBuilder
{
    # Filters

    protected function findFilter(string $property): ? Filter
    {
        $filter = $this->allowedFilters
            ->first(function (Filter $filter) use ($property) {
                return $filter->isForProperty($property);
            });

        return $filter ?: Filter::custom($property, FiltersExactWithNull::class);
    }

    protected function guardAgainstUnknownFilters()
    {
        return true;
    }

    # Sorts

    protected function findSort(string $property): ? Sort
    {
        $sort = $this->allowedSorts
            ->merge($this->defaultSorts)
            ->first(function (Sort $sort) use ($property) {
                return $sort->isForProperty($property);
            });

        if (is_null($sort)) {
            if (strpos($property, '.') !== false) {
                return Sort::custom($property, SortsScope::class);
            }
            return Sort::field(ltrim($property, '-'));
        }

        return $sort;
    }

    protected function guardAgainstUnknownSorts()
    {
        return true;
    }

    # Includes

    protected function guardAgainstUnknownIncludes()
    {
        return true;
    }
}
