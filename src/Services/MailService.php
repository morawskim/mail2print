<?php

namespace mail2print\Services;


use mail2print\Models\AttachmentError;
use mail2print\Models\JobsContainer;
use mail2print\Models\MailAttachment;
use mail2print\Models\PrintJob;
use Zend\Mail\Header\From;
use Zend\Mail\Header\GenericHeader;
use Zend\Mail\Storage\Message;
use Zend\Mail\Storage\Part;

class MailService
{
    /** @var  Message */
    protected $message = null;

    public function __construct(Message $message = null)
    {
        $this->setMessage($message);
    }

    public function extractAttachments()
    {
        $message = $this->getMessage();

        /** @var MailAttachment[] $parts */
        $parts = [];
        while ($message->valid()) {
            $part = $message->current();
            if (self::isAttachment($part)) {
                $parts[] = MailAttachment::fromPart($part);
            }
            $message->next();
        }

        return $parts;
    }

    /**
     * @param MailAttachment[] $attachments
     * @return JobsContainer
     */
    public function createJobs(array $attachments)
    {
        $container = new JobsContainer();

        foreach ($attachments as $attachment) {
            if ($attachment->isSupported()) {
                $job = PrintJob::fromMailAttachment($attachment);
                $job->setFrom($this->getFromMail());
                $container->addPrintJob($job);
            } else {
                //dodajemy do zadania z bledami!
                $container->addAttachmentError(AttachmentError::fromAttachment($attachment));
            }
        }

        return $container;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param Message $message
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;
    }

    public function getFromMail()
    {
        /** @var From $header */
        $header = $this->getMessage()->getHeaders()->get('From');
        return $header->getAddressList()->current()->getEmail();
    }

    public function getSubject()
    {
        $headers = $this->getMessage()->getHeaders();
        if ($headers->has('Subject')) {
            return $headers->get('Subject')->getFieldValue();
        }

        return '';
    }

    public static function isAttachment(Part $part)
    {
        $headerContentDispositionName = 'Content-Disposition';
        $headers = $part->getHeaders();

        if (!$headers->has($headerContentDispositionName)) {
            return false;
        }

        /** @var GenericHeader $contentDispositionHeader */
        $contentDispositionHeader = $headers->get($headerContentDispositionName);
        if (stripos($contentDispositionHeader->getFieldValue(), 'attachment;') !== 0) {
            return false;
        }

        return true;
    }
}