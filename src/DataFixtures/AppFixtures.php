<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Teams;
use App\Entity\Users;
use App\Entity\Groups;
use App\Entity\Stades;
use App\Entity\Stages;
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
              ->setPassword($this->passwordEncoder->encodePassword($admin,'password'))
              ->setFirstName('Prénom')
              ->setLastName('Nom')
              ->setEmail('admin@symfoot.be')
              ->setPicture('http://www.placehold.it/60x60');
        
        $manager->persist($admin);

        //fixtures ajout des groupes
        $groupNames = ['A','B','C','D','E','F'];
        $g=0;
        for($i=1; $i<=6; $i++){
            $group = new Groups();
            $group->setName($groupNames[$g]);
            $manager->persist($group);
            $g++;
        }

        //fixtures ajout des phases
        $stageNames = [
            'Huitième de finale',
            'Quart de finale',
            'Demi finale',
            'Finale'
        ];
        $s=0;
        for($i=1; $i<=4; $i++){
            $stage = new Stages();
            $stage->setName($stageNames[$s]);
            $manager->persist($stage);
            $s++;
        }


        // ajout des équipes
        $now = new \DateTime('Europe/Brussels');

        for($i=1; $i<=24; $i++){
            $team = new Teams();

            $team->setName('Nom de l\'équipe')
                 ->setLogo('http://www.placehold.it/100x100')
                 ->setDescription('Description de l\'équipe')
                 ->setCover('http://www.placehold.it/1000x300')
                 ->setPoints(0)
                 ->setCreatedAt($now);
            
            $manager->persist($team);
        }


        // ajout des stade
        for($i=1; $i<=12; $i++){
        $stade = new Stades();

        $stade->setName('Nom du stade')
            ->setCity('Ville du stade')
            ->setCapacity(1000)
            ->setDescription('Description du stade')
            ->setCover('http://www.placehold.it/1000x300')
            ->setCreatedAt($now);
            
        $manager->persist($stade);
        }

        $manager->flush();
    }
}
