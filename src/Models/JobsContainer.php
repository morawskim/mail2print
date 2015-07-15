<?php

namespace mail2print\Models;


class JobsContainer
{
    protected $printJobs = [];
    protected $attachmentsError = [];

    public function addPrintJob(PrintJob $printJob)
    {
        $this->printJobs[] = $printJob;
    }

    /**
     * @return PrintJob[]
     */
    public function getPrintJobs()
    {
        return $this->printJobs;
    }

    public function addAttachmentError(AttachmentError $attachmentError)
    {
        $this->attachmentsError[] = $attachmentError;
    }

    public function getAttachmentsError()
    {
        return $this->attachmentsError;
    }


}