<?php

namespace Dannerz\Api\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaseService
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->save(new $this->model, $data)->fresh();
    }

    public function delete($id)
    {
        $this->model->destroy($id);
    }

    public function update($id, array $data)
    {
        return $this->save($this->model->findOrFail($id), $data)->fresh();
    }

    protected function save(Model $model, array $data): Model
    {
        $hasManyRelationKeys = $this->getRelationKeys($model, $data, HasMany::class);
        $belongsToManyRelationKeys = $this->getRelationKeys($model, $data, BelongsToMany::class);

        $attributes = array_except($data, array_merge($belongsToManyRelationKeys, $hasManyRelationKeys));
        $hasManyRelationsToSync = array_only($data, $hasManyRelationKeys);
        $belongsToManyRelationsToSync = array_only($data, $belongsToManyRelationKeys);

        $model->fill($attributes);

        $model->save();

        foreach ($hasManyRelationsToSync as $hasManyRelation => $rows) {
            if (! is_array($rows)) continue;
            $this->syncHasManyRelationModels($model, camel_case($hasManyRelation), collect($rows));
        }

        foreach ($belongsToManyRelationsToSync as $belongsToManyRelation => $ids) {
            if (! is_array($ids)) continue;
            $model->{camel_case($belongsToManyRelation)}()->sync($ids);
        }

        return $model->fresh();
    }

    protected function getRelationKeys($model, $data, $relationClass)
    {
        $relationKeys = [];

        foreach (array_keys($data) as $key) {
            $method = camel_case($key);
            if (method_exists($model, $method)) {
                if (get_class($model->$method()) == $relationClass) {
                    $relationKeys[] = $key;
                }
            }
        }

        return $relationKeys;
    }

    protected function syncHasManyRelationModels($model, $hasManyRelation, $rows)
    {
        $hasManyModel = $model->$hasManyRelation()->getRelated();

        // Deleted.
        $rowIds = $rows->pluck($hasManyModel->getKeyName())->filter()->all();
        $model->$hasManyRelation->each(function ($hasManyModel) use ($rowIds) {
            if (! in_array($hasManyModel->getKey(), $rowIds)) $hasManyModel->delete();
        });

        // Created.
        $rows->where($hasManyModel->getKeyName(), null)->each(function ($attributes) use ($model, $hasManyRelation, $hasManyModel) {
            $model->$hasManyRelation()->create($attributes);
        });

        // Updated.
        $rows->where($hasManyModel->getKeyName(), '!=', null)->each(function ($attributes) use ($hasManyModel) {
            $hasManyModel->findOrFail($attributes[$hasManyModel->getKeyName()])->update($attributes);
        });
    }
}
