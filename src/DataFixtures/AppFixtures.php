<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Teams;
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
              ->setRoles(['ROLE_USER','ROLE_ADMIN'])
              ->setPassword($this->passwordEncoder->encodePassword($admin,'epse'))
              ->setFirstName('Maxime')
              ->setLastName('Ghislain')
              ->setEmail('maxime@symfoot.be');
        
        $manager->persist($admin);

        // ajout d'une équipe
        $team = new Teams();
        $now = new \DateTime('Europe/Brussels');

        $team->setName('Belgique')
            ->setLogo('https://upload.wikimedia.org/wikipedia/fr/thumb/6/65/Logo_F%C3%A9d%C3%A9ration_Belge_Football_2019.svg/1200px-Logo_F%C3%A9d%C3%A9ration_Belge_Football_2019.svg.png')
            ->setDescription('Description de l\'équipe nationale de foot belge')
            ->setCover('https://www.aimgroupinternational.com/upload/_1600x900/brussels-cover.jpg')
            ->setPoints(0)
            ->setCreatedAt($now);
            
        $manager->persist($team);


        $manager->flush();
    }
}
