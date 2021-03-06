<?php

namespace MK\AppBundle\Command;

use MK\AppBundle\Entity\Task;
use MK\AppBundle\Repository\TaskRepository;
use MK\MailBundle\Service\MailTemplate;
use MK\SMSBundle\Service\PlusSMSSender;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\Common\Persistence\ObjectManager;
use MK\UserBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

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
        date_default_timezone_set('Europe/Warsaw');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $maxRuntime = (int)$input->getOption('max-runtime');
        if ($maxRuntime <= 0) {
            throw new \RuntimeException('The maximum runtime must be greater than zero.');
        }

        /** @var $serviceMail MailTemplate */
        $serviceMail = $this->getContainer()->get('mk_mail_engine.class');

        /** @var $serviceSMS PlusSMSSender */
        $serviceSMS = $this->getContainer()->get('mk_sms_engine.class');

        /** @var $taskRepository TaskRepository */
        $taskRepository = $this->entityManager->getRepository('MKAppBundle:Task');
        $tasks = $taskRepository->findCurrentAllWithReminder();
        $now = new \DateTime();
        $base_domain = $this->getContainer()->getParameter('base_domain');
        $sendTasks = 0;

        /** @var $router Router */
        $router = $this->getContainer()->get('router');

        $translator = $this->getContainer()->get('translator');
        /** @var $task Task */
        foreach ($tasks as $task) {
            $now_time = microtime(true);
            if ((int)($now_time - $startTime) > $maxRuntime) {
                $output->writeln('Max. runtime reached, exiting...');
                break;
            }
            /** @var $user User */
            $user = $task->getUser();

            $locale = $user->getLocale();
            $translator->setLocale($locale);
            $url = $base_domain . $router->generate('homepage', array('_locale' => $locale));
    
            $params = array(
                'title' => $task->getTitle(),
                'deadline' => $task->getDeadline()->format("d-m-Y H:i"),
                'url' => $url,
            );

            $subject = $this->getContainer()->get('translator')->trans('task.reminder.subject');

            $serviceMail->sendMailWithTemplate($subject, $params, $user->getEmail(), 'task-notice');

            $phone = $user->getPhone();
            if (!empty($phone)) {
                $content = $this->getContainer()->get('translator')->trans('Your task: %title% expired: %date%.',
                    array('%title%' => $task->getTitle(), '%date%' => $task->getDeadline()->format("d-m-Y H:i"))
                );
                $serviceSMS->sendSMS($user->getPhone(), $content);
            }
            $task->setLastSendNotice($now);
            $this->entityManager->persist($task);
            $sendTasks++;
        }
        $output->writeln('Send tasks: ' . $sendTasks);

        $this->entityManager->flush();
        $this->entityManager->clear();

        if ($output->isVerbose()) { // if set -v parameters in command query
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;
            $output->writeln(sprintf('[INFO] Elapsed time: %.2f ms', $elapsedTime * 1000));
        }
    }
}