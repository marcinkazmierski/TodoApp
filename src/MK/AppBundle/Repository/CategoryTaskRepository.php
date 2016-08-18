<?php

namespace MK\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\User;

class CategoryTaskRepository extends EntityRepository
{
    public function queryAllBuilder(User $user)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('c')
            ->from('MKAppBundle:CategoryTask', 'c')
            ->where('c.status > 0')
            ->andWhere('c.user = :user')
            ->orderBy('c.name', 'ASC')
            ->setParameter('user', $user);
        return $queryBuilder;
    }

    public function queryAll(User $user)
    {
        $results = $this->queryAllBuilder($user)->getQuery()->getResult(); // HYDRATE_ARRAY -> as array results
        return $results;
    }


}