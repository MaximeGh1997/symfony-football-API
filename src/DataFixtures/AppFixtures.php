<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // fixtures ajout d'un admin
        $admin = new Users();

        $admin->setUsername('admin')
              ->setRoles(['ROLE_ADMIN','ROLE_USER'])
              ->setPassword($this->passwordEncoder->encodePassword($admin,'epse'))
              ->setFirstName('Maxime')
              ->setLastName('Ghislain')
              ->setEmail('maxime@symfoot.be');
        
        $manager->persist($admin);


        $manager->flush();
    }
}
