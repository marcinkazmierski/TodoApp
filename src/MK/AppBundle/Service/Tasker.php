<?php
namespace MK\AppBundle\Service;

use Doctrine\ORM\EntityManager;
use MK\AppBundle\Entity\Task;
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

    /**
     * {% set tab = mk_app_tasker.getDailyTasks(app.user) %}
     */
    public function getDailyTasks(User $user)
    {
        $tab = array();
        $last = new \DateTime();
        $now = new \DateTime();
        $now->setTime(23, 59);

        $results = $this->entityManager->getRepository('MKAppBundle:Task')->queryAll($user, 2);
        if ($results) {
            /** @var $task Task */
            foreach ($results as $task) {
                $date = $task->getDoneDate();
                if ($last > $date) {
                    $last = $date;
                }
                if (!isset($tab[$date->format('Y-m-d')]['done'])) {
                    $tab[$date->format('Y-m-d')]['done'] = 0;
                }
                $tab[$date->format('Y-m-d')]['done']++;
            }
        }

        while ($last < $now) {
            if (!isset($tab[$last->format('Y-m-d')]['done'])) {
                $tab[$last->format('Y-m-d')]['done'] = 0;
            }
            $last->modify('+1 day');
        }
        return $tab;
    }

    public function allTasksFromCategory(User $user, $categoryId)
    {
        $category = $this->entityManager->getRepository('MKAppBundle:CategoryTask')->find($categoryId);
        $results = $this->entityManager->getRepository('MKAppBundle:Task')->allFromCategory($user, $category);
        return $results;
    }
}