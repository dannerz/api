<?php

namespace Sota\Api\Export;

use Maatwebsite\Excel\Excel;

class BaseExport
{
    protected $query;

    protected $type;

    public function __construct($query, $type)
    {
        $this->query = $query;

        $this->type = $type;
    }

    public function download()
    {
        //return Excel::download(new $export($this->queryBuilder->getQuery()), $export->getFileName().'.'.$type);
    }
}
