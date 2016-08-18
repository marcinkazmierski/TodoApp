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

    public function queryAllFromCategory(User $user, CategoryTask $category)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->where('t.status > 0')
            ->andWhere('t.user = :user')
            ->andWhere('t.category = :category')
            ->orderBy('t.deadline', 'ASC')
            ->setParameter('category', $category)
            ->setParameter('user', $user);
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

        //todo join with category
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('t')
            ->from('MKAppBundle:Task', 't')
            ->leftJoin('t.category', 'c')
            ->where('t.status > 0')
            ->andWhere('c.reminder = true')
            ->andWhere('t.deadline <= :deadline')
            ->andWhere('t.lastSendNotice <= :yesterday')
            ->setParameter('deadline', $today)
            ->setParameter('yesterday', $yesterday)
            ->orderBy('t.deadline', 'ASC');
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }
}