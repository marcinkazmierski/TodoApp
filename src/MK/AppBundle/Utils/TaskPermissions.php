<?php

namespace MK\AppBundle\Utils;

use MK\AppBundle\Entity\Task;
use MK\UserBundle\Entity\User;

class TaskPermissions
{

    public function __construct()
    {

    }

    public function isTaskAuthor(Task $task, User $user)
    {
        if ($task && $user && $task->getUser()->getId() === $user->getId()) {
            return true;
        }
        return false;
    }
}