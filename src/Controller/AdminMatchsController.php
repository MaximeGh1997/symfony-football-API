<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Matchs;
use App\Form\MatchScoreType;
use App\Services\MatchsService;
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
    public function drawing(Groups $group, MatchsService $matchsService, DatesRepository $datesRepo)
    {
        //RECUPERATION DES EQUIPES,STADES, ET DATES DISPO DANS LE GROUPE
        $teams = $group->getTeams()->toArray();
        $stadiums = $group->getStades()->toArray();
        $dates = $datesRepo->findFreeDates();

        //VERIFICATION QU'IL Y A SUFFISAMENT D'ELEMENTS POUR FAIRE LE TIRAGE
        if(count($teams) == 4){
            if(count($dates) >= 6){
                if(count($stadiums) >= 2){
                    // SI TOUT EST OK POUR CHAQUE EQUIPE DU GROUPE ON AJOUTE 3 MATCHS
                    $matchsService->drawingGroupMatchs($group, $teams, $stadiums, $dates);
                    
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
                }else{                          // SI MATCH DE PHASE FINALE ET MATCH NUL ON PREND UN GAGNANT AU HASARD (APRES PROLONGATION)
                    $teams = [
                        $team1,
                        $team2
                    ];
                    $winner = $teams[mt_rand(0,1)];
                    $looser = $teams[mt_rand(0,1)];
                    while($looser == $winner){
                        $looser = $teams[mt_rand(0,1)];
                    }
                    $match->setWinner($winner)
                          ->setLooser($looser);
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
                "Le résultat du match n° {$match->getId()} : {$match->getTeam1()->getName()} {$match->getScoreT1()} - {$match->getScoreT2()} {$match->getTeam2()->getName()} à bien été encodé"
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
            $scoreT1 = rand(0,4);
            $scoreT2 = rand(0,4);

            if($match->getStage()->getId() == 4){
                while($scoreT2 == $scoreT1){
                    $scoreT2 = rand(0,4);
                }
            }

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
                }else{
                    $teams = [
                        $team1,
                        $team2
                    ];
                    $winner = $teams[mt_rand(0,1)];
                    $looser = $teams[mt_rand(0,1)];
                    while($looser == $winner){
                        $looser = $teams[mt_rand(0,1)];
                    }
                    $match->setWinner($winner)
                          ->setLooser($looser);
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
                "Le résultat du match n° {$match->getId()} : {$match->getTeam1()->getName()} {$match->getScoreT1()} - {$match->getScoreT2()} {$match->getTeam2()->getName()} à bien été simulé"
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
