<?php

namespace Dannerz\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Dannerz\Api\Services\BaseService;
use Dannerz\Api\Exports\BaseExport;
use Dannerz\Api\QueryBuilder\BaseQueryBuilder;

class BaseController extends Controller
{
    protected $request;

    protected $modelName;

    protected $model;

    protected $service;

    protected $queryBuilder;

    protected $export;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->modelName = studly_case(str_singular($request->segment(2)));

        $this->model = resolve('App\Models\\'.$this->modelName);

        $this->service = $this->getService();

        $this->queryBuilder = $this->getQueryBuilder();

        $this->export = $this->getExport();
    }

    public function create()
    {
        return response()->json(['data' => $this->service->create($this->request->all())]);
    }

    public function delete($id)
    {
        $this->service->delete($id);

        return response()->json();
    }

    public function find($id)
    {
        return response()->json(['data' => $this->queryBuilder->findOrFail($id)]);
    }

    public function get()
    {
        if ($this->request->has('page')) {
            $dataset = $this->queryBuilder->paginate();
            return response()->json([
                'pagination' => array_except($dataset, 'data'),
                'data' => $dataset['data'],
            ]);
        }

        return response()->json(['data' => $this->queryBuilder->get()]);
    }

    public function update($id)
    {
        $model = $this->service->update($id, $this->request->all());

        return response()->json(['data' => $model]);
    }

    public function export()
    {
        return $this->export->download();
    }

    protected function getService()
    {
        $serviceName = 'App\Services\\'.$this->modelName.'Service';

        if (! class_exists($serviceName)) {
            return new BaseService($this->model);
        }

        return new $serviceName($this->model);
    }

    protected function getQueryBuilder()
    {
        $queryBuilderName = 'App\QueryBuilders\\'.$this->modelName.'QueryBuilder';

        if (! class_exists($queryBuilderName)) {
            return new BaseQueryBuilder($this->request, $this->model);
        }

        return new $queryBuilderName($this->request, $this->model);
    }

    protected function getExport()
    {
        $exportName = 'App\Exports\\'.$this->modelName.'Export';

        if (! class_exists($exportName)) {
            return new BaseExport($this->queryBuilder->getQuery(), $this->request->type);
        }

        return new $exportName($this->model);
    }
}
