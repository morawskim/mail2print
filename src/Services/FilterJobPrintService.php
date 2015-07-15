<?php

namespace mail2print\Services;


use mail2print\Models\Filters\Filter;
use mail2print\Models\Filters\MimeType;
use mail2print\Models\PrintJob;

class FilterJobPrintService
{
    protected $filters = [];

    public static function factory()
    {
        $obj = new static;
        $obj->addFilter(new MimeType());

        return $obj;
    }

    /**
     * @param PrintJob[] $jobsPrint
     * @return PrintJob[]
     */
    public function filter(array $jobsPrint)
    {
        $filtered = [];
        foreach ($jobsPrint as $j) {
            foreach ($this->getFilters() as $filter) {
                if($filter->accept($j)) {
                    $filtered[] = $j;
                }
            }
        }

        return $filtered;
    }

    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }
}