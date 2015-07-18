<?php

namespace mail2print\Exceptions;


use Exception;

class LprException extends RuntimeException
{
    protected $stderrOutput = '';

    public function __construct($message = "", $code = 0, $stderr = '', Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setStderrOutput($stderr);
    }

    /**
     * @return string
     */
    public function getStderrOutput()
    {
        return $this->stderrOutput;
    }

    /**
     * @param string $stderrOutput
     */
    public function setStderrOutput($stderrOutput)
    {
        if (is_array($stderrOutput)) {
            $stderrOutput = implode(PHP_EOL, $stderrOutput);
        }

        $this->stderrOutput = $stderrOutput;
    }



}