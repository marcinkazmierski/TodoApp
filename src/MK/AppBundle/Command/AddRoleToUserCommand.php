<?php


namespace MK\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\Common\Persistence\ObjectManager;
use MK\UserBundle\Entity\User;

class AddRoleToUserCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:add-role-to-user')
            ->setDescription('Add role for user.')
            ->setHelp($this->getCommandHelp())
            // see http://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('username', InputArgument::OPTIONAL, 'The username')
            ->addArgument('role', InputArgument::OPTIONAL, 'The new role for user');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('username') && null !== $input->getArgument('role')) {
            return;
        }

        $output->writeln('');
        $output->writeln('Add Role to User Command Interactive Wizard');
        $output->writeln('-----------------------------------');

        $output->writeln('If you prefer to not use this interactive wizard, provide the arguments required by this command as follows:');
        $output->writeln(' $ php bin/console app:add-role-to-user username role');

        $output->writeln(array(
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ));

        // See http://symfony.com/doc/current/components/console/helpers/questionhelper.html
        $console = $this->getHelper('question');

        $maxAttempts = 5;
        // Ask for the username if it's not defined
        $username = $input->getArgument('username');
        if (null === $username) {
            $question = new Question(' > <info>Username</info>: ');
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('The username cannot be empty');
                }

                return $answer;
            });
            $question->setMaxAttempts($maxAttempts);

            $username = $console->ask($input, $output, $question);
            $input->setArgument('username', $username);
        } else {
            $output->writeln(' > <info>Username</info>: ' . $username);
        }

        // Ask for the role if it's not defined
        $role = $input->getArgument('role');
        if (null === $role) {
            $question = new Question(' > <info>Role</info>: ');
            $question->setValidator(array($this, 'roleValidator'));
            $question->setMaxAttempts($maxAttempts);

            $role = $console->ask($input, $output, $question);
            $input->setArgument('role', $role);
        } else {
            $output->writeln(' > <info>Role</info>: ' . $role);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $username = $input->getArgument('username');
        $role = $input->getArgument('role');

        $user = $this->entityManager->getRepository('MKAppBundle:User')->findOneBy(array('username' => $username));

        if (!$user) {
            throw new \RuntimeException(sprintf('User with "%s" username - not found.', $username));
        }

        $user->addRole($role);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('');

        $rolesString = '';
        foreach ($user->getRoles() as $r) {
            $rolesString .= $r . ', ';
        }

        $output->writeln(sprintf('[OK] Successfully updated roles to %s (for %s)', $rolesString, $user->getUsername()));

        if ($output->isVerbose()) { // if set -v parameters in command query
            $finishTime = microtime(true);
            $elapsedTime = $finishTime - $startTime;

            $output->writeln(sprintf('[INFO] Elapsed time: %.2f ms', $user->getId(), $elapsedTime * 1000));
        }
    }


    /**
     * Role validator.
     */
    public function roleValidator($role)
    {
        if (empty($role)) {
            throw new \Exception('The role can not be empty');
        }

        $roles = $this->getContainer()->getParameter('user_roles');

        if (!is_array($roles) || empty($roles)) {
            throw new \RuntimeException(sprintf('Set array parameters "user_roles".'));
        }

        if (!in_array($role, $roles)) {
            throw new \RuntimeException(sprintf('Incorrect user role name.'));
        }

        return $role;
    }

    private function getCommandHelp()
    {
        return <<<HELP
        The <info>%command.name%</info> command add role for exist user with username:
        <info>php %command.full_name%</info> <comment>username role</comment>
HELP;
    }
}