<?php

namespace Dannerz\Api\QueryBuilder;

trait ElasticSearch
{
    protected function search($search)
    {
        $terms = [];

        foreach (explode(' ', $search) as $word) {
            $terms[] = ['regexp' => ['*' => strtolower($word).'.*']];
        }

        return $this->model::searchByQuery([
            'constant_score' => [
                'filter' => [
                    'bool' => [
                        'must' => $terms,
                    ],
                ],
            ],
        ], null, null, 10000)->modelKeys();
    }
}
