<?php

namespace MK\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use MK\UserBundle\Validator\Constraints as UserAssert;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="\MK\AppBundle\Entity\CategoryTask", mappedBy="user")
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="\MK\AppBundle\Entity\Task", mappedBy="user")
     */
    protected $tasks;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @UserAssert\ConstraintPhone
     */
    private $phone;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Add category
     *
     * @param \MK\AppBundle\Entity\CategoryTask $category
     *
     * @return User
     */
    public function addCategory(\MK\AppBundle\Entity\CategoryTask $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \MK\AppBundle\Entity\CategoryTask $category
     */
    public function removeCategory(\MK\AppBundle\Entity\CategoryTask $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add task
     *
     * @param \MK\AppBundle\Entity\Task $task
     *
     * @return User
     */
    public function addTask(\MK\AppBundle\Entity\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \MK\AppBundle\Entity\Task $task
     */
    public function removeTask(\MK\AppBundle\Entity\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
