<?php

namespace MK\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="MK\AppBundle\Repository\TaskRepository")
 * @ORM\Table(name="task")
 */
class Task
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank(message = "task.title.not_blank")
     * @ORM\Column(type="text")
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;


    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $doneDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deadline;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastSendNotice;

    /**
     * (message = "task.category.not_blank")
     * @ORM\ManyToOne(targetEntity="CategoryTask", inversedBy="tasks")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\Column(type="integer")
     *
     * Values:
     *  0 - deleted
     *  1 - active
     *  2 - done
     *
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="\MK\UserBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param \MK\AppBundle\Entity\CategoryTask $category
     *
     * @return Task
     */
    public function setCategory(\MK\AppBundle\Entity\CategoryTask $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \MK\AppBundle\Entity\CategoryTask
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Task
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Task
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Task
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user
     *
     * @param \MK\UserBundle\Entity\User $user
     *
     * @return Task
     */
    public function setUser(\MK\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MK\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set doneDate
     *
     * @param \DateTime $doneDate
     *
     * @return Task
     */
    public function setDoneDate($doneDate)
    {
        $this->doneDate = $doneDate;

        return $this;
    }

    /**
     * Get doneDate
     *
     * @return \DateTime
     */
    public function getDoneDate()
    {
        return $this->doneDate;
    }

    /**
     * Set lastSendNotice
     *
     * @param \DateTime $lastSendNotice
     *
     * @return Task
     */
    public function setLastSendNotice($lastSendNotice)
    {
        $this->lastSendNotice = $lastSendNotice;

        return $this;
    }

    /**
     * Get lastSendNotice
     *
     * @return \DateTime
     */
    public function getLastSendNotice()
    {
        return $this->lastSendNotice;
    }
}
