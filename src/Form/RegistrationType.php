<?php

namespace App\Form;

use App\Entity\Users;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, $this->getConfiguration("Prénom", "Votre prénom"))
            ->add('lastname', TextType::class, $this->getConfiguration("Nom", "Votre nom"))
            ->add('username', TextType::class, $this->getConfiguration("Nom d'utilisateur", "Votre nom d'utilisateur"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Votre adresse e-mail"))
            ->add('password', PasswordType::class, $this->getConfiguration("Mot de passe", "Votre mot de passe"))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot de passe","Veuillez confirmer votre mot de passe"))
            ->add('picture', FileType::class, [
                'label' => "Image de profil (jpg,png,gif)",
                'required' => false
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
