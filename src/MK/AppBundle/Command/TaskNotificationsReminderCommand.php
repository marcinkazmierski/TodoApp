<?php

namespace MK\AppBundle\Command;

use MK\AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\Common\Persistence\ObjectManager;
use MK\UserBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TaskNotificationsReminderCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    protected function configure()
    {
        $this
            ->setName('app:cron:task:reminder')
            ->addOption('max-runtime', 'r', InputOption::VALUE_REQUIRED, 'The maximum runtime in seconds.', 900)
            ->setDescription('Task notifications reminder.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $maxRuntime = (int)$input->getOption('max-runtime');
        if ($maxRuntime <= 0) {
            throw new \RuntimeException('The maximum runtime must be greater than zero.');
        }

        $service = $this->getContainer()->get('mk_mail_engine.class');
        $serviceSMS = $this->getContainer()->get('mk_sms_engine.class');

        $tasks = $this->entityManager->getRepository('MKAppBundle:Task')->findCurrentAllWithReminder();
        $now = new \DateTime();
        $base_domain = $this->getContainer()->getParameter('base_domain');

        /** @var $task Task */
        foreach ($tasks as $task) {
            $now_time = microtime(true);
            if ((int)($now_time - $startTime) > $maxRuntime) {
                $output->writeln('Max. runtime reached, exiting...');
                break;
            }

            $this->getContainer()->get('translator')->setLocale('pl');

            $url = $base_domain . $this->getContainer()->get('router')->generate('edit_task', array('id' => $task->getId()));

            $content = $this->getContainer()->get('translator')->trans('Your task: <a href="%link%">%title%</a> is expired: %date%.',
                array('%title%' => $task->getTitle(), '%date%' => $task->getDeadline()->format("d-m-Y H:i"), '%link%' => $url)
            );

            $subject = $this->getContainer()->get('translator')->trans('task.reminder.subject');
            $user = $task->getUser();
            $service->sendMail($subject, $content, $user->getEmail());

            $phone = $user->getPhone();
            if (!empty($phone)) {
                $serviceSMS->sendSMS($user->getPhone(), $content);
            }
            $task->setLastSendNotice($now);
            $this->entityManager->persist($task);
        }
        $output->writeln('Send tasks: ' . count($tasks));

        $this->entityManager->flush();
        $this->entityManager->clear();

        if ($output->isVerbose()) { // if set -v parameters in command query
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;
            $output->writeln(sprintf('[INFO] Elapsed time: %.2f ms', $elapsedTime * 1000));
        }
    }
}