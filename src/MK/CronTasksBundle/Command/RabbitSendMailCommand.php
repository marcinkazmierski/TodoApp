<?php

namespace MK\CronTasksBundle\Command;

use MK\CronTasksBundle\Service\CronPrepareMail;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RabbitSendMailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('rabbit:admin:send-mail');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $cronPrepareMail CronPrepareMail */
        $cronPrepareMail = $this->getContainer()->get('cron_tasks.prepare_mail');
        $cronPrepareMail->process("Subject for Admin", "Content for Admin", "sklepflock+admin@gmail.com");
    }
}