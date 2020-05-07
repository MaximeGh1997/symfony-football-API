<?php

namespace App\Form;

use App\Entity\Teams;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TeamType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom de l\'équipe','Entrez le nom de l\'équipe'))
            ->add('logo', UrlType::class, $this->getConfiguration('Logo de l\'équipe','Entrez l\'url du logo'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description de l\'équipe','Décrivez brièvement l\'équipe'))
            ->add('cover', UrlType::class, $this->getConfiguration('Image de couverture','Entrez l\'url de l\'image représentant l\'équipe'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teams::class,
        ]);
    }
}
