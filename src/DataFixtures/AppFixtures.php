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
              ->setPassword($this->passwordEncoder->encodePassword($admin,'epse'))
              ->setFirstName('Maxime')
              ->setLastName('Ghislain')
              ->setEmail('maxime@symfoot.be');
        
        $manager->persist($admin);

        //fixtures ajout des groupes
        $groupA = new Groups();
        $groupA->setName('A');
        $manager->persist($groupA);

        $groupB = new Groups();
        $groupB->setName('B');
        $manager->persist($groupB);

        $groupC = new Groups();
        $groupC->setName('C');
        $manager->persist($groupC);

        $groupD = new Groups();
        $groupD->setName('D');
        $manager->persist($groupD);

        $groupE = new Groups();
        $groupE->setName('E');
        $manager->persist($groupE);

        $groupF = new Groups();
        $groupF->setName('F');
        $manager->persist($groupF);

        $groups = [
            $groupA,
            $groupB,
            $groupC,
            $groupD,
            $groupE,
            $groupF
        ];

        //fixtures ajout des phases
        $stage1 = new Stages();
        $stage1->setName('1/8');
        $manager->persist($stage1);

        $stage2 = new Stages();
        $stage2->setName('1/4');
        $manager->persist($stage2);

        $stage3 = new Stages();
        $stage3->setName('1/2');
        $manager->persist($stage3);

        $stage4 = new Stages();
        $stage4->setName('Finale');
        $manager->persist($stage4);

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

        // ajout de plusieurs équipes
        $names = [
            'France',
            'Allemagne',
            'Espagne',
            'Angleterre',
            'Croatie',
            'Portugal',
            'Suisse',
            'Russie',
            'Pologne',
            'Suède'
        ];

        for($i=1; $i<=15; $i++){
            $teams = new Teams();

            $teams->setName($names[mt_rand(0,9)])
                 ->setLogo('https://upload.wikimedia.org/wikipedia/fr/thumb/e/ea/Football_Br%C3%A9sil_federation.svg/1200px-Football_Br%C3%A9sil_federation.svg.png')
                 ->setDescription('Description de l\'équipe nationale de foot')
                 ->setCover('https://cdn.pixabay.com/photo/2016/11/16/02/19/new-york-city-1828013_960_720.jpg')
                 ->setPoints(0)
                 ->setCreatedAt($now);
            
            $manager->persist($teams);
        }

        // ajout d'un stade
        $stade = new Stades();
        $now = new \DateTime('Europe/Brussels');

        $stade->setName('Stade Roi Baudouin')
            ->setCity('Bruxelles')
            ->setCapacity(50093)
            ->setDescription('Description du stade Roi Baudouin')
            ->setCover('https://pbs.twimg.com/media/DOTUEAlX4AEQrxX.jpg')
            ->setResident($team)
            ->setCreatedAt($now);
            
        $manager->persist($stade);


        $manager->flush();
    }
}
