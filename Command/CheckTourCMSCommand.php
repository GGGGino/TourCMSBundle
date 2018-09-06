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

    /**
     * CheckTourCMSCommand constructor.
     * @param TourCMSChecker $tourCMSChecker
     */
    public function __construct(TourCMSChecker $tourCMSChecker)
    {
        $this->tourCMSChecker = $tourCMSChecker;
        $this->tourCMSChecker->setRenderType($tourCMSChecker::RENDER_STRUCTURE);

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('ggggino:tourcms:check')
            ->setDescription('Check the connection to the TourCMS API');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            'Check Tour CMS API',
            $this->tourCMSChecker->getTourCMS()->getPrivateKey(),
            '==================',
        ]);

        $checks = $this->tourCMSChecker->checkAll();

        foreach($checks as $key => $check){
            list($status, $text) = $check;

            if( $status )
                $output->writeln('<info>' . $key . ': ' . $text . '</info>');
            else
                $output->writeln('<comment>' . $key . ': ' . $text . '</comment>');
        }
    }
}