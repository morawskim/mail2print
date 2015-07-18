<?php

namespace mail2print\Models;


use mail2print\Exceptions\InvalidArgumentException;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\TransportInterface;

class MailTransportConfiguration
{
    /**
     * @param array $options
     * @return TransportInterface
     */
    public static function factory(array $options)
    {
        if (isset($options['transport'])) {
            switch ($options['transport']) {
                case 'sendmail':
                    unset($options['transport']);
                    $transport = new Sendmail($options);
                    return $transport;
                    break;
                case 'smtp':
                    unset($options['transport']);
                    unset($options['from']);
                    $transport = new Smtp(new SmtpOptions($options));
                    return $transport;
                    break;
                default:
                    throw new InvalidArgumentException(sprintf('Unknown "%s" transport. Supported only smtp and sendmail.', $options['transport']));
            }
        } else {
            throw new InvalidArgumentException(sprintf('Configuration for mail repley must have key transport.'));
        }
    }
}