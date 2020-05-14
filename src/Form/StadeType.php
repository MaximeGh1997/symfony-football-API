<?php

namespace App\Form;

use App\Entity\Teams;
use App\Entity\Groups;
use App\Entity\Stades;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class StadeType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getConfiguration('Nom du stade','Entrez le nom du stade'))
            ->add('city', TextType::class, $this->getConfiguration('Ville','Entrez la ville du stade'))
            ->add('capacity', IntegerType::class, $this->getConfiguration('Capacité du stade','Entrez le nombre de places du stade'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description du stade','Décrivez brièvement le stade'))
            ->add('cover', UrlType::class, $this->getConfiguration('Image de couverture','Entrez l\'url de l\'image représentant le stade'))
            //->add('resident', EntityType::class, [
            //    'class' => Teams::class,
            //    'choice_label' => 'name'
            //],
            //$this->getConfiguration('Résident du stade','Choisissez l\'équipe résidente')
            //)
            ->add('groups', EntityType::class, [
                'class' => Groups::class,
                'choice_label' => 'name'
            ],
            $this->getConfiguration('Groupe','Assignez un groupe au stade')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stades::class,
        ]);
    }
}
