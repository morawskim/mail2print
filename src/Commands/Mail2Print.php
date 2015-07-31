<?php

namespace mail2print\Commands;

use mail2print\Models\Configuration;
use mail2print\Models\JobsContainer;
use mail2print\Models\MailTransportConfiguration;
use mail2print\Services\FilterJobPrintService;
use mail2print\Models\Mail;
use mail2print\Services\MailService;
use mail2print\Services\PrintService;
use mail2print\Services\ReportService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Mail2Print extends Command
{
    /** @var  Logger */
    protected $logger;

    /**
     * @var null|Configuration
     */
    protected $config;

    protected function configure()
    {
        $this
            ->setName('mail2print:run')
            ->setDescription('Print all supported attachments from mail message.')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_OPTIONAL,
                'The path to configuration file',
                '/etc/mail2print.ini'
            );
    }

    public function setLogger(Logger $loggerInterface)
    {
        $this->logger = $loggerInterface;
    }

    public function setConfiguration(Configuration $configuration)
    {
        $this->config = $configuration;
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $configPath = $input->getOption('file');
        $this->setConfiguration(Configuration::parseIniFile($configPath));


        $logPath = $input->getOption('log');
        $this->logger->pushHandler(new StreamHandler($logPath, $this->getConfiguration()->getLogLevel()));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getLogger();
        $logger->debug(sprintf('Using config file "%s".', $input->getOption('file')));

        $messages = Mail::fromStdIn();

        $mailService = new MailService($messages);
        $logger->info(sprintf('New mail message from "%s".', $mailService->getFromMail()));

        $attachmentsPart = $mailService->extractAttachments();
        $logger->debug(sprintf('Found "%d" attachments.', count($attachmentsPart)));

        /** @var JobsContainer $container */
        $container = $mailService->createJobs($attachmentsPart);

        foreach ($container->getAttachmentsError() as $attachmentError) {
            $logger->info(sprintf('Attachment "%s" is not supported.', $attachmentError->getAttachmentName()), $attachmentError->getErrors());
        }

        $jobsPrint = $container->getPrintJobs();
        $filterService = FilterJobPrintService::factory();
        $filtered = $filterService->filter($jobsPrint);

        foreach ($jobsPrint as $job) {
            if ($job->hasError()) {
                $logger->info(sprintf('Attachment "%s" can\'t be print due to filter restrictions.', $job->getAttachmentName()), $job->getErrors());
            }
        }

        $printService = new PrintService();
        $printService->setLprPath($this->getConfiguration()->getLprBin());
        foreach ($filtered as $f) {
            $printService->sendToPrinter($f);
            $logger->info(sprintf('Add print job "%s" to queue.', $f->getAttachmentName()));
        }

        //summed up
        $report = new ReportService($container);
        $content = (string)$report;

        //reply to sender with report
        $mail = new \mail2print\Models\Reply\Mail();
        $mail->setTo($mailService->getFromMail());
        $mail->setSubject('Re: ' . $mailService->getSubject());
        $mail->setFrom($this->getConfiguration()->getMailFrom());
        $mail->setTransport(MailTransportConfiguration::factory($this->getConfiguration()->getMailConfig()));
        $mail->send($content);

        $logger->info(sprintf('Send report to "%s".', $mailService->getFromMail()));
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }
}