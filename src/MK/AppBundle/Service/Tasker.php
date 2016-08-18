<?php
namespace MK\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use MK\UserBundle\Entity\User;

/**
 * Class Tasker
 * @package MK\AppBundle\Service
 *
 * How to use:
 *          $service = $this->get('mk_app_tasker');
 *
 */
class Tasker
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllTasksTodoCount(User $user)
    {
        $results = $this->entityManager->getRepository('MKAppBundle:Task')->queryAll($user, 1);
        if ($results) {
            return count($results);
        }
        return 0;
    }

    public function getAllTasksArchivedCount(User $user)
    {
        $results = $this->entityManager->getRepository('MKAppBundle:Task')->queryAll($user, 2);
        if ($results) {
            return count($results);
        }
        return 0;
    }

    public function getAllTasksCount(User $user)
    {
        return $this->getAllTasksTodoCount($user) + $this->getAllTasksArchivedCount($user);
    }

    public function getAllTaskTodoTodayCount(User $user)
    {
        $results = $this->entityManager->getRepository('MKAppBundle:Task')->getAllTodayTodo($user);
        if ($results) {
            return count($results);
        }
        return 0;
    }


    public function getAllTaskDoneTodayCount(User $user)
    {
        $results = $this->entityManager->getRepository('MKAppBundle:Task')->getAllTodayDone($user);
        if ($results) {
            return count($results);
        }
        return 0;
    }
}