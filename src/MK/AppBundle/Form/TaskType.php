<?php

namespace MK\AppBundle\Form;

use MK\AppBundle\Entity\CategoryTask;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    protected $user;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->user = $options['user'];
        $builder
            ->add('title', TextType::class, array('label' => "Task title"))
            ->add('description', TextareaType::class, array('required' => false))
            ->add('deadline', TextType::class, array('required' => false))
            ->add('category', EntityType::class, array(
                'class' => CategoryTask::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->queryAllBuilder($this->user);
                },
                'placeholder' => 'Choose an option',
                'choice_label' => 'name',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MK\AppBundle\Entity\Task',
            'user' => 'MK\UserBundle\Entity\User'
        ));
    }

}