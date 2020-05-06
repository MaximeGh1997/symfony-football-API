<?php

namespace App\Form;

use App\Entity\Teams;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('logo')
            ->add('description')
            ->add('cover')
            ->add('points')
            ->add('createdAt')
            ->add('stade')
            ->add('groupName')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teams::class,
        ]);
    }
}
