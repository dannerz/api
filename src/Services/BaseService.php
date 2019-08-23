<?php

namespace Dannerz\Api\Services;

use Illuminate\Database\Eloquent\Model;

class BaseService
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data)->fresh();
    }

    public function delete($id)
    {
        $this->model->destroy($id);
    }

    public function update($id, array $data)
    {
        $model = $this->model->findOrFail($id);

        $model->update($data);

        return $model->fresh();
    }

    // TODO: Implement basic child & pivot handling again.

    // TODO: Implement basic delete guard? Where guards against any children?

    // TODO: Make commands.

    // TODO: Spatie media?
}
