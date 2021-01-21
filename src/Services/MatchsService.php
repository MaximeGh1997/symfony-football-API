<?php

namespace App\Services;

use App\Entity\Matchs;
use App\Repository\DatesRepository;
use App\Repository\MatchsRepository;
use Doctrine\ORM\EntityManagerInterface;

class MatchsService {

    private $datesRepo;
    private $matchsRepo;
    private $manager;

    public function __construct (DatesRepository $datesRepo, MatchsRepository $matchsRepo, EntityManagerInterface $manager) {
        $this->datesRepo = $datesRepo;
        $this->matchsRepo = $matchsRepo;
        $this->manager = $manager;
    }

    /* permet d'ajouter les matchs du groupe aléatoirement (3 matchs par équipe) */
    public function drawingGroupMatchs ($group, $teams, $stadiums, $dates) {

        // on parcoure chaque équipe pour lui ajouter ses 3 matchs de groupe
        foreach ($teams as $team) {
            // MATCH 1
            // Choix de l'adversaire, il ne peut être égale a lui même
            $opponent1 = $teams[mt_rand(0,3)];
            while($opponent1 == $team){
                $opponent1 = $teams[mt_rand(0,3)];
            }

            $stadium1 = $stadiums[mt_rand(0,1)];

            //Je compte le nbr de matchs que l'équipe et l'adversaire ont déjà
            //Je récupère la clé maximale de mon tableau de dates dispos
            //S'il reste au moins une date, j'en choisis une
            $teamMatchs = count($this->matchsRepo->findByTeam($team->getId()));
            $opponent1Matchs = count($this->matchsRepo->findByTeam($opponent1->getId()));
            $max = count($dates) - 1;
            if($max >= 0){
                $date = $dates[mt_rand(0,$max)];
            }

            //Je vérifie que mon équipe et l'adversaire ont moins de 3 matchs et que j'ai une date
            // Si oui, j'ajoute le match
            if($teamMatchs < 3 && $opponent1Matchs < 3 && $max >= 0){
                $match1 = new Matchs();
                $match1->setTeam1($team)
                    ->setTeam2($opponent1)
                    ->setDate($date)
                    ->setStade($stadium1)
                    ->setGroupName($group);

                $this->manager->persist($match1);
                $this->manager->flush();
            }

            // MATCH 2
            // Choix de l'adversaire, il ne peut ni être égale a lui même ni au premier adversaire
            $opponent2 = $teams[mt_rand(0,3)];
                while($opponent2 == $team || $opponent2 == $opponent1){
                    $opponent2 = $teams[mt_rand(0,3)];
                }

            $stadium2 = $stadiums[mt_rand(0,1)];
                while($stadium2 == $stadium1){
                    $stadium2 = $stadiums[mt_rand(0,1)];
                }

            //Je compte le nbr de matchs que l'équipe et l'adversaire ont déjà
            //Je récupère la clé maximale de mon tableau de dates dispos
            //S'il reste au moins une date, j'en choisis une
            $teamMatchs = count($this->matchsRepo->findByTeam($team->getId()));
            $opponent2Matchs = count($this->matchsRepo->findByTeam($opponent2->getId()));
            $dates = $this->datesRepo->findFreeDates();
            $max = count($dates) - 1;
            if($max >= 0){
                $date = $dates[mt_rand(0,$max)];
            }
                        
            // Je vérifie que mon équipe et l'adversaire ont moins de 3 matchs et que j'ai une date
            // Si oui, j'ajoute le match
            if($teamMatchs < 3 && $opponent2Matchs < 3 && $max >= 0){
                $match2 = new Matchs();

                $match2->setTeam1($opponent2)
                    ->setTeam2($team)
                    ->setDate($date)
                    ->setStade($stadium2)
                    ->setGroupName($group);
                        
                $this->manager->persist($match2);
                $this->manager->flush();
            }

            // MATCH 3
            // Choix de l'adversaire, il ne peut ni être égale a lui même ni au premier et au second adversaire
            $opponent3 = $teams[mt_rand(0,3)];
                while($opponent3 == $team || $opponent3 == $opponent1 || $opponent3 == $opponent2){
                    $opponent3 = $teams[mt_rand(0,3)];
                }

            //Je compte le nbr de matchs que l'équipe et l'adversaire ont déjà
            //Je récupère la clé maximale de mon tableau de dates dispos
            //S'il reste au moins une date, j'en choisis une
            $teamMatchs = count($this->matchsRepo->findByTeam($team->getId()));
            $opponent3Matchs = count($this->matchsRepo->findByTeam($opponent3->getId()));
            $dates = $this->datesRepo->findFreeDates();
            $max = count($dates) - 1;
            if($max >= 0){
                $date = $dates[mt_rand(0,$max)];
            }

            // Je vérifie que mon équipe et l'adversaire ont moins de 3 matchs et que j'ai une date
            // Si oui, j'ajoute le match
            if($teamMatchs < 3 && $opponent3Matchs < 3 && $max >= 0){
                $match3 = new Matchs();

                $match3->setTeam1($team)
                    ->setTeam2($opponent3)
                    ->setDate($date)
                    ->setStade($stadiums[mt_rand(0,1)])
                    ->setGroupName($group);
                        
                $this->manager->persist($match3);
                $this->manager->flush();
            }
        }
    }
}