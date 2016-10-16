<?php
namespace MK\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('color', ChoiceType::class, array('label' => 'Choose color', 'choices' => array(
                'Default' => 'default',
                'Info' => 'info',
                'Success' => 'success',
                'Warning' => 'warning',
                'Danger' => 'danger',
                'Primary' => 'primary',
            )))
            ->add('reminder', CheckboxType::class, array('label' => 'Auto reminder?', 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MK\AppBundle\Entity\CategoryTask',
        ));
    }
}