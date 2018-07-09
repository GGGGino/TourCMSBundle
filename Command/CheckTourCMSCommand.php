<?php

namespace GGGGino\TourCMSBundle\Command;

use GGGGino\TourCMSBundle\Service\TourCMSChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckTourCMSCommand extends Command
{
    /**
     * @var TourCMSChecker
     */
    private $tourCMSChecker;

    public function __construct(TourCMSChecker $tourCMSChecker)
    {
        $this->tourCMSChecker = $tourCMSChecker;
        //$this->tourCMSChecker->setTestAsString(false);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ggggino:tourcms:check')
            ->setDescription('Check the connection to the TourCMS API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            'Check Tour CMS API',
            '==================',
        ]);

        $checks = $this->tourCMSChecker->checkAll();

        foreach($checks as $check){
            $output->writeln($check);
        }
    }
}