<?php

namespace Sota\Api\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\FiltersExact;

class FiltersExactWithNull extends FiltersExact
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $value = $this->parseNullsInValue($value);

        return parent::__invoke($query, $value, $property);
    }

    protected function parseNullsInValue($value)
    {
        $values = is_array($value) ? $value : [$value];

        foreach ($values as $key => $value) {
            if (strtolower($value) == 'null') {
                $values[$key] = null;
            }
        }

        return $values;
    }
}
