<?php

namespace mail2print\Services;


use mail2print\Models\JobsContainer;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ReportService
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var JobsContainer
     */
    protected $jobContainer;

    public function __construct(JobsContainer $container)
    {
        $this->jobContainer = $container;

        $path = __DIR__ . '/../views';
        $loader = new Twig_Loader_Filesystem($path);
        $this->twig = new Twig_Environment($loader);
    }

    public function __toString()
    {
        $content = $this->twig->render('report.twig',
            [
                'jobs' => $this->jobContainer->getPrintJobs(),
                'attachmentsError' => $this->jobContainer->getAttachmentsError(),
            ]
        );

        return $content;
    }
}