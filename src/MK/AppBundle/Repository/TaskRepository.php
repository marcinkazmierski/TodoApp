<?php

namespace MK\AppBundle\Repository;

use MK\AppBundle\Entity\CategoryTask;
use MK\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function queryAll(User $user, $status = 1)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->where('t.status = :status')
            ->andWhere('t.user = :user')
            ->orderBy('t.deadline', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('status', $status);
        $results = $queryBuilder->getQuery()->getResult(); // HYDRATE_ARRAY -> as array results
        return $results;
    }

    public function getAllTodayDone(User $user)
    {
        $doneDateStart = new \DateTime();
        $doneDateStart->setTime(0, 0);
        $doneDateEnd = new \DateTime();
        $doneDateEnd->setTime(23, 59);

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->where('t.status = :status')
            ->andWhere('t.user = :user')
            ->andWhere('t.doneDate > :doneDateStart')
            ->andWhere('t.doneDate < :doneDateEnd')
            ->orderBy('t.deadline', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('doneDateStart', $doneDateStart)
            ->setParameter('doneDateEnd', $doneDateEnd)
            ->setParameter('status', 2);
        $results = $queryBuilder->getQuery()->getResult(); // HYDRATE_ARRAY -> as array results
        return $results;
    }

    public function getAllTodayTodo(User $user)
    {
        $deadlineDateStart = new \DateTime();
        $deadlineDateStart->setTime(0, 0);
        $deadlineDateEnd = new \DateTime();
        $deadlineDateEnd->setTime(23, 59);

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->where('t.status = :status')
            ->andWhere('t.user = :user')
            ->andWhere('t.deadline > :deadlineDateStart')
            ->andWhere('t.deadline < :deadlineDateEnd')
            ->orderBy('t.deadline', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('deadlineDateStart', $deadlineDateStart)
            ->setParameter('deadlineDateEnd', $deadlineDateEnd)
            ->setParameter('status', 1);
        $results = $queryBuilder->getQuery()->getResult(); // HYDRATE_ARRAY -> as array results
        return $results;
    }

    public function allFromCategory(User $user, CategoryTask $category, $limit = 0)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->where('t.status = 1')
            ->andWhere('t.user = :user')
            ->andWhere('t.category = :category')
            ->orderBy('t.id', 'DESC')
            ->setParameter('category', $category)
            ->setParameter('user', $user);
        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }
        $results = $queryBuilder->getQuery()->getResult(); // HYDRATE_ARRAY -> as array results
        return $results;
    }

    public function findCurrentAll()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->where('t.status > 0')
            ->orderBy('t.deadline', 'ASC');
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }

    public function findCurrentAllWithReminder()
    {
        $today = new \DateTime();
        $yesterday = new \DateTime('-24 hours');

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->leftJoin('t.category', 'c')
            ->where('t.status > 0')
            ->andWhere('c.reminder = true')
            ->andWhere('t.deadline <= :deadline')
            ->andWhere('t.lastSendNotice <= :yesterday OR t.lastSendNotice is NULL')
            ->setParameter('deadline', $today)
            ->setParameter('yesterday', $yesterday)
            ->orderBy('t.deadline', 'ASC');
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }
}