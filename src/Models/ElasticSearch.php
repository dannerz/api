<?php

namespace Sota\Api\Models;

use Elasticquent\ElasticquentTrait;
use Elasticsearch\Common\Exceptions\Missing404Exception;

trait ElasticSearch
{
    use ElasticquentTrait;

    public function getIndexName()
    {
        return config('app.name').$this->getTable();
    }

    public function getIndexDocumentData()
    {
        return array_filter($this->toArray());
    }

    public static function restartElasticsearch()
    {
        try {
            self::deleteIndex();
            self::createIndex();
            self::putMapping();
            self::addAllToIndex();
        } catch (Missing404Exception $e) {
            self::createIndex();
            self::putMapping();
            self::addAllToIndex();
        }
    }
}
