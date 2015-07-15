<?php

namespace mail2print\Models\Filters;


use mail2print\Models\PrintJob;

interface Filter
{
    public function accept(PrintJob $job);
}