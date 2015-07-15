<?php

namespace mail2print\Models\Filters;


use mail2print\Models\PrintJob;

class MimeType implements Filter
{
    protected static $supportedMimeTypes = ['application/pdf', 'text/plain', 'image/jpeg'];

    public function accept(PrintJob $printJob)
    {
        $mimeType = $printJob->detectMimeType();
        $supportedMimeTypes = $this->getSupportedMimeTypes();
        $result = in_array($mimeType, $supportedMimeTypes);

        if (!$result) {
            $supportedMimeTypesString = implode(', ', $supportedMimeTypes);
            $printJob->addError(sprintf('Detected mime type of file is not supported. Detected "%s". Allowed are: %s',
                $mimeType, $supportedMimeTypesString
            ));
        }

        return $result;
    }

    public function getSupportedMimeTypes()
    {
        return self::$supportedMimeTypes;
    }
}