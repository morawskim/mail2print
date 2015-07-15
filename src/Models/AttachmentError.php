<?php

namespace mail2print\Models;


class AttachmentError
{
    protected $errors = [];
    protected $attachmentName = '';


    public static function fromAttachment(MailAttachment $attachment)
    {
        $obj = new static;
        $obj->setErrors($attachment->getErrors());
        $obj->setAttachmentName($attachment->getAttachmentName());
        $attachment->getAttachmentName();
        return $obj;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
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
}