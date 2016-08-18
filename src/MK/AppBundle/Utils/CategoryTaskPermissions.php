<?php

namespace MK\AppBundle\Utils;

use MK\AppBundle\Entity\CategoryTask;
use MK\UserBundle\Entity\User;

class CategoryTaskPermissions
{

    public function __construct()
    {

    }

    public function isCategoryTaskAuthor(CategoryTask $category, User $user)
    {
        if ($category && $user && $category->getUser()->getId() === $user->getId()) {
            return true;
        }
        return false;
    }
}