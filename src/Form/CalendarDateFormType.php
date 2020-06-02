<?php

namespace App\Form;

use App\Entity\Dates;
use App\Entity\Matchs;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CalendarDateFormType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimeType::class,[
                "label" => "Ajoutez une date au calendrier"
            ])
            /*->add('matchNbr', EntityType::class,[
                'label' => 'Match n° (Laissez vide pour libérer la date)',
                'class' => Matchs::class,
                'choice_label' => 'id',
                'required'   => false,
                'empty_data' => null
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dates::class
        ]);
    }
}
