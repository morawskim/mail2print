<?php

namespace mail2print\Models;


class PrintJob
{
    protected $filePath;

    protected $mimeType;

    protected $from;

    protected $attachmentName;

    protected $date;

    protected $errors = [];

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }

    /**
     * @param string $attachmentName
     */
    public function setAttachmentName($attachmentName)
    {
        $this->attachmentName = $attachmentName;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function detectMimeType()
    {
        $filepath = $this->getFilePath();
        if (is_file($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            return $mimetype;
        }

        return null;
    }

    public static function fromMailAttachment(MailAttachment $mailAttachment)
    {
        $job = new static;
        $job->setAttachmentName($mailAttachment->getAttachmentName());
        $job->setFilePath($mailAttachment->storeAttachment());
        $job->setDate(time());
        $job->setFrom('aa@aa.pl');
        $job->setMimeType($job->detectMimeType());

        return $job;
    }

    public function addError($msg)
    {
        $this->errors[] = $msg;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasError()
    {
        $errors = $this->getErrors();
        return !empty($errors);
    }
}