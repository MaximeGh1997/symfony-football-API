<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Matchs;
use App\Form\MatchScoreType;
use App\Repository\DatesRepository;
use App\Repository\TeamsRepository;
use App\Repository\GroupsRepository;
use App\Repository\MatchsRepository;
use App\Repository\StadesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMatchsController extends AbstractController
{
    /**
     * @Route("/admin/matchs", name="admin_matchs_index")
     */
    public function index(MatchsRepository $matchsRepo, GroupsRepository $groupsRepo, DatesRepository $datesRepo, Request $request)
    {
        $matchs = $matchsRepo->findByDate('ASC');
        $group = null;

        $groupId = $request->request->get('group'); // récupération de l'id correspondant au groupe
        if($groupId != null){
            $matchs = $matchsRepo->findByGroup($groupId);
            $group = $groupsRepo->find($groupId);
        }

        return $this->render('admin/matchs/index.html.twig', [
            'matchs' => $matchs,
            'groups' => $groupsRepo->findAll(),
            'group' => $group,
            'dates' => $datesRepo->findFreeDates()
        ]);
    }

    /**
     * Tirage au sort des matchs pour chaque groupe
     * @Route("/admin/matchs/group-{id}/drawing", name="admin_matchs_drawing")  
     * 
     * @return Response
     */
    public function drawing(Groups $group, StadesRepository $stadesRepo, MatchsRepository $matchsRepo, DatesRepository $datesRepo, EntityManagerInterface $manager)
    {
        $teams = $group->getTeams()->toArray();
        $stades = $group->getStades()->toArray();
        $dates = $datesRepo->findFreeDates();

        //RECUPERATION DES EQUIPES,STADES, ET DATES DISPO DANS LE GROUPE

        //VERIFICATION QU'IL Y A SUFFISAMENT D'ELEMENTS POUR FAIRE LE TIRAGE
        if(count($teams) == 4){
            if(count($dates) >= 6){
                if(count($stades) >= 2){
                    // SI TOUT EST OK POUR CHAQUE EQUIPE DU GROUPE ON AJOUTE 3 MATCHS
                    foreach($teams as $team){
                        // MATCH 1
                        // Choix de l'adversaire, il ne peut être égale a lui même
                        $opponent1 = $teams[mt_rand(0,3)];
                            while($opponent1 == $team){
                                $opponent1 = $teams[mt_rand(0,3)];
                            }

                        $stade1 = $stades[mt_rand(0,1)];
                        
                        //Je compte le nbr de matchs que l'équipe et l'adversaire ont déjà
                        //Je récupère la clé maximale de mon tableau de dates dispos
                        //S'il reste au moins une date, j'en choisis une
                        $teamId = $team->getId();
                        $opponent1Id = $opponent1->getId();
                        $teamMatchs = count($matchsRepo->findByTeam($teamId));
                        $opponent1Matchs = count($matchsRepo->findByTeam($opponent1Id));
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
                                ->setStade($stade1)
                                ->setGroupName($group);

                            $manager->persist($match1);
                            $manager->flush();
                        }

                        // MATCH 2
                        // Choix de l'adversaire, il ne peut ni être égale a lui même ni au premier adversaire
                        $opponent2 = $teams[mt_rand(0,3)];
                            while($opponent2 == $team || $opponent2 == $opponent1){
                                $opponent2 = $teams[mt_rand(0,3)];
                            }

                        $stade2 = $stades[mt_rand(0,1)];
                            while($stade2 == $stade1){
                                $stade2 = $stades[mt_rand(0,1)];
                            }

                        //Je compte le nbr de matchs que l'équipe et l'adversaire ont déjà
                        //Je récupère la clé maximale de mon tableau de dates dispos
                        //S'il reste au moins une date, j'en choisis une
                        $opponent2Id = $opponent2->getId();
                        $teamMatchs = count($matchsRepo->findByTeam($teamId));
                        $opponent2Matchs = count($matchsRepo->findByTeam($opponent2Id));
                        $dates = $datesRepo->findFreeDates();
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
                                ->setStade($stade2)
                                ->setGroupName($group);
                        
                            $manager->persist($match2);
                            $manager->flush();
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
                        $opponent3Id = $opponent3->getId();
                        $teamMatchs = count($matchsRepo->findByTeam($teamId));
                        $opponent3Matchs = count($matchsRepo->findByTeam($opponent3Id));
                        $dates = $datesRepo->findFreeDates();
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
                                ->setStade($stades[mt_rand(0,1)])
                                ->setGroupName($group);
                        
                            $manager->persist($match3);
                            $manager->flush();
                        }
                    }
                    // FIN DE BOUCLE TOUT EST OK
                    $this->addFlash(
                        "success",
                        "Les matchs du groupe {$group->getName()} ont bien été ajouté"
                    );
                    return $this->redirectToRoute('admin_matchs_index');

                }else{
                    // GESTION ERREUR PAS 2 STADES DISPOS
                    $this->addFlash(
                        "danger",
                        "Attention, vous devez assigné deux stades hôtes pour le groupe !"
                    );
                    return $this->redirectToRoute('admin_matchs_index');
                }
            }else{
                // GESTION ERREUR PAS 6 DATES DISPOS
                $this->addFlash(
                    "danger",
                    "Attention, vous devez avoir au moins 6 dates disponibles !"
                );
                return $this->redirectToRoute('admin_matchs_index');
            }
        }else{
            // GESTION ERREUR PAS 4 EQUIPES DANS LE GROUPE
            $this->addFlash(
                "danger",
                "Attention, vous devez d'abord tirer au sort les groupes !"
            );
            return $this->redirectToRoute('admin_matchs_index');
        }
    }

    /**
     * Permet d'encoder le resultat d'un match
     * @Route("/admin/matchs/{id}/edit-result", name="admin_matchs_edit")
     * 
     * @return Response
     */
    public function editResult(Matchs $match, Request $request, EntityManagerInterface $manager)
    {
        // FORMULAIRE D'ENCODAGE DU SCORE
        $form = $this->createForm(MatchScoreType::class, $match);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // RECUPERATION DES DEUX EQUIPES ET LEURS SCORE
            $team1 = $match->getTeam1();
            $team2 = $match->getTeam2();
            $scoreT1 = $match->getScoreT1();
            $scoreT2 = $match->getScoreT2();

            $match->setIsPlayed(true); // MATCH JOUER
     
            // SI EQUIPE 1 A GAGNER
            if($scoreT1 > $scoreT2){
                $match->setWinner($team1)
                      ->setLooser($team2)
                      ->setDraw(false);
                
                if($match->getStage() == null){ // VERIFIER SI C'EST UN MATCH DE GROUPE POUR AJOUT DES POINTS
                    $points = $team1->getPoints();
                    $newPoints = $points + 3;
                    $team1->setPoints($newPoints);
                }
            }
            // SI MATCH NUL
            elseif($scoreT1 == $scoreT2){
                $match->setDraw(true);

                if($match->getStage() == null){ // VERIFIER SI C'EST UN MATCH DE GROUPE POUR AJOUT DES POINTS
                    $pointsT1 = $team1->getPoints();
                    $pointsT2 = $team2->getPoints();
                    $newPointsT1 = $pointsT1 + 1;
                    $newPointsT2 = $pointsT2 + 1;
                
                    $team1->setPoints($newPointsT1);
                    $team2->setPoints($newPointsT2);
                }
            }
            // SI EQUIPE 2 A GAGNER
            else{
                $match->setWinner($team2)
                      ->setLooser($team1)
                      ->setDraw(false);
                
                if($match->getStage() == null){ // VERIFIER SI C'EST UN MATCH DE GROUPE POUR AJOUT DES POINTS
                    $points = $team2->getPoints();
                    $newPoints = $points + 3;
                    $team2->setPoints($newPoints);
                }
            }

            $manager->persist($team1);
            $manager->persist($team2);
            $manager->persist($match);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le résultat du match n°{$match->getId()}: {$match->getTeam1()->getName()} - {$match->getTeam2()->getName()} à bien été encodé"
            );

            return $this->redirectToRoute('admin_matchs_index');
        }

        return $this->render('admin/matchs/editResult.html.twig', [
            'form' => $form->createView(),
            'match' => $match
        ]);
    }

    /**
     * Permet de simuler le resultat d'un match
     * @Route("/admin/matchs/{id}/simul-result", name="admin_matchs_simul")
     * 
     * @return Response
     */
    public function simulResult(Matchs $match, EntityManagerInterface $manager)
    {
        // PROCECUS IDEM QUE MATCH EDIT
            $team1 = $match->getTeam1();
            $team2 = $match->getTeam2();
            $scoreT1 = rand(0,5);
            $scoreT2 = rand(0,5);

            $match->setIsPlayed(true);

            if($scoreT1 > $scoreT2){
                $match->setScoreT1($scoreT1)
                      ->setScoreT2($scoreT2)
                      ->setWinner($team1)
                      ->setLooser($team2)
                      ->setDraw(false);
                
                if($match->getStage() == null){
                    $points = $team1->getPoints();
                    $newPoints = $points + 3;
                    $team1->setPoints($newPoints);
                }
            }
            elseif($scoreT1 == $scoreT2){
                $match->setScoreT1($scoreT1)
                      ->setScoreT2($scoreT2)
                      ->setDraw(true);

                if($match->getStage() == null){
                    $pointsT1 = $team1->getPoints();
                    $pointsT2 = $team2->getPoints();
                    $newPointsT1 = $pointsT1 + 1;
                    $newPointsT2 = $pointsT2 + 1;
                        
                    $team1->setPoints($newPointsT1);
                    $team2->setPoints($newPointsT2);
                }
            }
            else{
                $match->setScoreT1($scoreT1)
                      ->setScoreT2($scoreT2)
                      ->setWinner($team2)
                      ->setLooser($team1)
                      ->setDraw(false);
                
                if($match->getStage() == null){
                    $points = $team2->getPoints();
                    $newPoints = $points + 3;
                    $team2->setPoints($newPoints);
                }
            }

            $manager->persist($team1);
            $manager->persist($team2);
            $manager->persist($match);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le match n°{$match->getId()}: {$match->getTeam1()->getName()} - {$match->getTeam2()->getName()} à bien été simulé"
            );

            return $this->redirectToRoute('admin_matchs_index');
    }

    /**
     * @Route("/admin/matchs/emptying", name="admin_matchs_emptying")
     * 
     * @return Response
     */
    public function emptying(MatchsRepository $matchsRepo, TeamsRepository $teamsRepo, EntityManagerInterface $manager)
    {
        $matchs = $matchsRepo->findAll();
        $teams = $teamsRepo->findAll();

        foreach ($matchs as $match) {
                $manager->remove($match);
                $manager->flush();
            }
        
        foreach ($teams as $team){
            $team->setPoints(0);
            $manager->persist($team);
        }

        $manager->flush();

        $this->addFlash(
            "success",
            "Tout les matchs ont bien été supprimé"
        );
        return $this->redirectToRoute('admin_matchs_index');
    }
}
