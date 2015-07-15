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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Mail2Print extends Command
{
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
            );;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $configFile = $input->getOption('file');
        $config = Configuration::parseIniFile($configPath);

        $messages = Mail::fromStdIn();

        $mailService = new MailService($messages);
        $attachmentsPart = $mailService->extractAttachments();
        /** @var JobsContainer $container */
        $container = $mailService->createJobs($attachmentsPart);

        $jobsPrint = $container->getPrintJobs();
        $filterService = FilterJobPrintService::factory();
        $filtered = $filterService->filter($jobsPrint);

        $printService = new PrintService();
        $printService->setLprPath($config->getLprBin());
        foreach ($filtered as $f) {
            $printService->sendToPrinter($f);
        }

        //summed up
        $report = new ReportService($container);
        $content = (string)$report;

        //reply to sender with report
        $mail = new \mail2print\Models\Reply\Mail();
        $mail->setTo($mailService->getFromMail());
        $mail->setSubject('Re: ' . $mailService->getSubject());
        $mail->setFrom($config->getMailFrom());
        $mail->setTransport(MailTransportConfiguration::factory($config->getMailConfig()));
        $mail->send($content);

    }
}