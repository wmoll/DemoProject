<?php

namespace Calor\ApiBundle\Command;

use Calor\ApiBundle\Entity\SessionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class checkSessionsCommand
 *
 * @package Rebeam\EbayBundle\Command
 */

class checkSessionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('calor:sessions:check')
            ->setDescription('Check and remove expires sessions')
            ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var SessionRepository $sessionRepository */
        $sessionRepository = $em->getRepository('CalorApiBundle:Session');
        $sessionRepository->validateTokens();
    }
}