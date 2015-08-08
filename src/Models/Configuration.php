<?php

namespace mail2print\Models;


use mail2print\Exceptions\InvalidArgumentException;
use mail2print\Exceptions\RuntimeException;
use Psr\Log\LogLevel;
use Zend\Config\Exception\ExceptionInterface;
use Zend\Config\Reader\Ini;

class Configuration
{
    /** @var  string */
    protected $lprBin;

    /** @var  array */
    protected $mailConfig;

    /** @var  string */
    protected $logLevel;

    /**
     * @return string
     */
    public function getLprBin()
    {
        return $this->lprBin;
    }

    /**
     * @param string $lprBin
     */
    public function setLprBin($lprBin)
    {
        $this->lprBin = $lprBin;
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @see Logger::toMonologLevel
     * @param string $logLevel
     */
    public function setLogLevel($logLevel)
    {
        $logLevel = strval($logLevel);
        if (defined("Psr\\Log\\LogLevel::$logLevel")) {
            $this->logLevel = $logLevel;
        } else {
            throw new InvalidArgumentException(sprintf('Log level "%s" is not supported', $logLevel));
        }
    }

    /**
     * @return array
     */
    public function getMailConfig()
    {
        return $this->mailConfig;
    }

    public function getMailFrom()
    {
        $config = $this->getMailConfig();
        return isset($config['from']) ? $config['from'] : '';
    }

    /**
     * @param array $mailConfig
     */
    public function setMailConfig(array $mailConfig)
    {
        $this->mailConfig = $mailConfig;
    }

    public static function parseIniFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException(sprintf('File "%s" not exist', $filePath));
        }

        if (!is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('File "%s" is not readable', $filePath));
        }

        if (!is_file($filePath)) {
            throw new InvalidArgumentException(sprintf('File "%s" is not regular file', $filePath));
        }

        $reader = new Ini();
        $array = $reader->fromFile($filePath);

        return self::parseArray($array);
    }

    public static function parseArray(array $config)
    {
        $obj = new static;

        if (isset($config['mail2print']) && is_array($config['mail2print'])) {
            $array = $config['mail2print'];
        } else {
            $array = array();
        }

        if (!empty($array['lpr_bin'])) {
            $obj->setLprBin($array['lpr_bin']);
        }

        if (!empty($array['log_level'])) {
            $obj->setLogLevel($array['log_level']);
        } else {
            $obj->setLogLevel('WARNING');
        }

        if (!empty($array['mail'])) {
            $obj->setMailConfig($array['mail']);
        }

        return $obj;
    }
}