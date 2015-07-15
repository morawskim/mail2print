<?php


namespace mail2print\Models\Reply;

use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class Mail implements Notify
{
    protected $subject;
    protected $from;
    protected $to;
    /**
     * @var TransportInterface
     */
    protected $transport;

    public function send($content)
    {
//        $textPart = new Part($content);
//        $textPart->type = "text/plain";

//        $body = new MimeMessage();
//        $body->setParts(array($textPart));

        $message = new Message();
        $message->setFrom($this->getFrom());
        $message->addTo($this->getTo());
        $message->setSubject($this->getSubject());

        $message->setEncoding("UTF-8");
        $message->setBody($content);
//        $message->getHeaders()->get('content-type')->setType('multipart/alternative');

        $transport = $this->getTransport();
        $transport->send($message);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param TransportInterface $transport
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

}