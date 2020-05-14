<?php

namespace App\Form;

use App\Entity\Matchs;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MatchScoreType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scoreT1', IntegerType::class, $this->getConfiguration('Buts de l\'équipe à domicile','Entrez le nombre de buts'))
            ->add('scoreT2', IntegerType::class, $this->getConfiguration('Buts de l\'équipe à l\'extérieur','Entrez le nombre de buts'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
        ]);
    }
}
