<?php

namespace mail2print\Models;


use Zend\Mail\Header\ContentTransferEncoding;
use Zend\Mail\Header\GenericHeader;
use Zend\Mail\Storage\Part;

class MailAttachment
{
    const TRANSFER_ENCODING_BASE64 = 'base64';
    const TRANSFER_ENCODING_UNKNOWN = '';

    /** @var  Part */
    protected $partMessage = null;

    protected $errors = [];
    protected $transferEncoding = self::TRANSFER_ENCODING_UNKNOWN;

    public function __construct(Part $partMessage = null)
    {
        if (null !== $partMessage) {
            $this->setPartMessage($partMessage);
        }
    }

    /**
     * @return Part
     */
    public function getPartMessage()
    {
        return $this->partMessage;
    }

    /**
     * @param Part $partMessage
     */
    public function setPartMessage($partMessage)
    {
        $this->resetState();
        $this->partMessage = $partMessage;
    }

    public function isSupported()
    {
        $part = $this->getPartMessage();
        $headers = $part->getHeaders();

        $headerContentTransferEncodingName = 'Content-Transfer-Encoding';
        if (!$headers->has($headerContentTransferEncodingName)) {
            $this->setError(sprintf('Header "Content-Transfer-Encoding" not found.'));
            return false;
        }

        /** @var ContentTransferEncoding $contentTransferEncodingHeader */
        $contentTransferEncodingHeader = $part->getHeader($headerContentTransferEncodingName);
        if ($contentTransferEncodingHeader->getTransferEncoding() !== 'base64'
            && $contentTransferEncodingHeader->getEncoding() !== 'ASCII') {
            $val = $contentTransferEncodingHeader->getFieldValue();
            $this->setError(sprintf('Unsupported transfer encoding algorithm. Supported only base64 with ASCII encoding. Get "%s"', $val));
            return false;
        }

        $this->setTransferEncoding(self::TRANSFER_ENCODING_BASE64);

        return true;
    }

    public function storeAttachment()
    {
        if (!$this->isSupported()) {
            throw new \RuntimeException(sprintf("Attachment is not supported and can't be store"));
        }

        $part = $this->getPartMessage();
        $content = $part->getContent();

        $content = $this->decodeAttachmentContent($content);

        $tmpfile = tempnam(sys_get_temp_dir(), 'mail2print');
        $result = file_put_contents($tmpfile, $content);
        if (false === $result) {
            throw new \RuntimeException(sprintf("Can't store attachment"));
        }

        return $tmpfile;
    }

    public function getTransferEncoding()
    {
        return $this->transferEncoding;
    }

    public function setError($errorMsg)
    {
        $this->errors = [$errorMsg];
    }

    public function getAttachmentName()
    {
        $part = $this->getPartMessage();
        $filename = '';
        $headerName = 'Content-Disposition';

        if ($part->getHeaders()->has($headerName)) {
            /** @var GenericHeader $value */
            $value = $part->getHeaders()->get($headerName);

            $matches = [];
            $pregResult = preg_match('#; filename="(.*?)"$#', $value->getFieldValue(), $matches);
            if ($pregResult && isset($matches[1])) {
                $filename = $matches[1];
            } else {
                $filename = '';
            }
        }

        return $filename;
    }

    protected function setTransferEncoding($transferEncoding)
    {
        $this->transferEncoding = $transferEncoding;
    }

    protected function resetState()
    {
        $this->errors = [];
        $this->transferEncoding = self::TRANSFER_ENCODING_UNKNOWN;
    }

    /**
     * @param $content
     * @return string
     */
    protected function decodeAttachmentContent($content)
    {
        switch ($this->getTransferEncoding()) {
            case self::TRANSFER_ENCODING_BASE64:
                $content = base64_decode($content);
                break;
            default:
                throw new \RuntimeException(sprintf('Attachment encoded unsupported algorithm.'));
        }
        return $content;
    }

    public function getErrors()
    {
        return $this->errors;
    }


    public static function fromPart(Part $part)
    {
        $obj = new static($part);
        return $obj;
    }
}