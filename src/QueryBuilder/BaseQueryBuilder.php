<?php

namespace Sota\Api\QueryBuilder;

use Illuminate\Http\Request;

class BaseQueryBuilder
{
    protected $request;

    protected $model;

    protected $query;

    public function __construct(Request $request, $model)
    {
        $this->request = $request;

        $this->model = $model;

        $this->query = SpatieQueryBuilderEnabler::for(get_class($model));

        $this->prepareSearch();

        $this->prepareFilters();

        $this->prepareSorts();

        $this->prepareIncludes();

        // Fields not necessary.

        // Appends not necessary.
    }

    protected function prepareSearch()
    {
        if (! method_exists($this, 'search')) return;

        if (! $this->request->has('search')) return;

        $ids = $this->search($this->request->search);

        $this->query->whereIn($this->model->getTable().'.'.$this->model->getKeyName(), $ids);
    }

    protected function prepareFilters()
    {
        if (method_exists($this, 'customFilters')) {
            $customFilters = $this->customFilters();
        }

        $this->query->allowedFilters($customFilters ?? []);
    }

    protected function prepareSorts()
    {
        if (method_exists($this, 'customSorts')) {
            $customSorts = $this->customSorts();
        }

        $this->query->allowedSorts($customSorts ?? []);
    }

    protected function prepareIncludes()
    {
        $this->query->allowedIncludes([]);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function findOrFail($id)
    {
        return $this->query->where($this->model->getKeyName(), $id)->firstOrFail();
    }

    public function paginate()
    {
        return $this->query->jsonPaginate()->jsonSerialize();
    }

    public function get()
    {
        return $this->query->get();
    }
}
