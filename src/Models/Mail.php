<?php

namespace mail2print\Models;



use Zend\Mail\Storage\Message;
use Zend\Mail\Storage\Part;

class Mail
{
    const MAX_MAIL_SIZE = 15728640; //15mb

    public static function fromStdIn()
    {
        $readed = 0;
        $content = '';
        $length = 2048;
        $handler = fopen('php://stdin', 'r');
        if (false === $handler) {
            throw new \RuntimeException(sprintf("Can't open stdin stream"));
        }

        while (!feof($handler)) {
            $content .= fread($handler, $length);
            $readed += $length;
            if ($readed > self::MAX_MAIL_SIZE) {
                throw new \RuntimeException(sprintf('Max mail size excessive.'));
            }
        }

        $message = new Message(['raw' => $content, 'noToplines' => false]);
        return $message;
    }
}