<?php
namespace MK\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => "Category title"))
            ->add('color', TextType::class, array('label' => 'Choose color'))
            ->add('reminder', CheckboxType::class, array('label' => 'Auto reminder?'))
            ->add('save', SubmitType::class, array('label' => 'Add category'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MK\AppBundle\Entity\CategoryTask',
        ));
    }
}