<?php

namespace mail2print\Services;


use mail2print\Exceptions\InvalidArgumentException;
use mail2print\Exceptions\LprException;
use mail2print\Models\PrintJob;

class PrintService
{
    protected $lprPath;

    public function setLprPath($path)
    {
        if (!is_file($path)) {
            throw new InvalidArgumentException('Lpr bin "%s" is not file.', $path);
        }

        if (!is_executable($path)) {
            throw new InvalidArgumentException('Lpr bin "%s" is not executable', $path);
        }

        $this->lprPath = $path;
    }

    public function getLprPath()
    {
        return $this->lprPath;
    }

    public function sendToPrinter(PrintJob $printJob)
    {
        $filePath = $printJob->getFilePath();
        $returnCode = null;
        $output = [];
        $cmd = $this->getCommand($filePath);

        echo $cmd;

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new LprException(sprintf('Lpr fails'), $returnCode, $output);
        }

        return true;
    }

    protected function getCommand($filePath)
    {
        return $this->getLprPath() . ' ' . escapeshellarg($filePath) . ' 2>&1';
    }
}