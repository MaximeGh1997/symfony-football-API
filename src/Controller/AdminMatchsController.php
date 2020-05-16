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
        $matchs = $matchsRepo->findAll();
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

        foreach($teams as $team){
                // Ajout Match 1
                $opponent1 = $teams[mt_rand(0,3)];
                    while($opponent1 == $team){
                        $opponent1 = $teams[mt_rand(0,3)];
                    }

                $stade1 = $stades[mt_rand(0,1)];
                
                $teamId = $team->getId();
                $opponent1Id = $opponent1->getId();
                $teamMatchs = count($matchsRepo->findByTeam($teamId));
                $opponent1Matchs = count($matchsRepo->findByTeam($opponent1Id));
                $max = count($dates) - 1;
                if($max >= 0){
                    $date = $dates[mt_rand(0,$max)];
                }
                
                
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

                // ajout Match 2
                $opponent2 = $teams[mt_rand(0,3)];
                    while($opponent2 == $team || $opponent2 == $opponent1){
                        $opponent2 = $teams[mt_rand(0,3)];
                    }

                $stade2 = $stades[mt_rand(0,1)];
                    while($stade2 == $stade1){
                        $stade2 = $stades[mt_rand(0,1)];
                    }

                $opponent2Id = $opponent2->getId();
                $teamMatchs = count($matchsRepo->findByTeam($teamId));
                $opponent2Matchs = count($matchsRepo->findByTeam($opponent2Id));
                $dates = $datesRepo->findFreeDates();
                $max = count($dates) - 1;
                if($max >= 0){
                    $date = $dates[mt_rand(0,$max)];
                }

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

                // ajout Match 3
                $opponent3 = $teams[mt_rand(0,3)];
                    while($opponent3 == $team || $opponent3 == $opponent1 || $opponent3 == $opponent2){
                        $opponent3 = $teams[mt_rand(0,3)];
                    }

                $opponent3Id = $opponent3->getId();
                $teamMatchs = count($matchsRepo->findByTeam($teamId));
                $opponent3Matchs = count($matchsRepo->findByTeam($opponent3Id));
                $dates = $datesRepo->findFreeDates();
                $max = count($dates) - 1;
                if($max >= 0){
                    $date = $dates[mt_rand(0,$max)];
                }

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
        $this->addFlash(
            "success",
            "Les matchs du groupe {$group->getName()} ont bien été ajouté"
        );
        return $this->redirectToRoute('admin_matchs_index');
    }

    /**
     * Permet d'encoder le resultat d'un match
     * @Route("/admin/matchs/{id}/edit-result", name="admin_matchs_edit")
     * 
     * @return Response
     */
    public function editResult(Matchs $match, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(MatchScoreType::class, $match);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $team1 = $match->getTeam1();
            $team2 = $match->getTeam2();
            $scoreT1 = $match->getScoreT1();
            $scoreT2 = $match->getScoreT2();

            $match->setIsPlayed(true);

            if($scoreT1 > $scoreT2){
                $match->setWinner($team1)
                      ->setLooser($team2)
                      ->setDraw(false);
                
                $points = $team1->getPoints();
                $newPoints = $points + 3;
                $team1->setPoints($newPoints);
            }
            elseif($scoreT1 == $scoreT2){
                $match->setDraw(true);

                $pointsT1 = $team1->getPoints();
                $pointsT2 = $team2->getPoints();
                $newPointsT1 = $pointsT1 + 1;
                $newPointsT2 = $pointsT2 + 1;
                
                $team1->setPoints($newPointsT1);
                $team2->setPoints($newPointsT2);
            }
            else{
                $match->setWinner($team2)
                      ->setLooser($team1)
                      ->setDraw(false);
                
                $points = $team2->getPoints();
                $newPoints = $points + 3;
                $team2->setPoints($newPoints);
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
                
                $points = $team1->getPoints();
                $newPoints = $points + 3;
                $team1->setPoints($newPoints);
            }
            elseif($scoreT1 == $scoreT2){
                $match->setScoreT1($scoreT1)
                      ->setScoreT2($scoreT2)
                      ->setDraw(true);

                $pointsT1 = $team1->getPoints();
                $pointsT2 = $team2->getPoints();
                $newPointsT1 = $pointsT1 + 1;
                $newPointsT2 = $pointsT2 + 1;
                
                $team1->setPoints($newPointsT1);
                $team2->setPoints($newPointsT2);
            }
            else{
                $match->setScoreT1($scoreT1)
                      ->setScoreT2($scoreT2)
                      ->setWinner($team2)
                      ->setLooser($team1)
                      ->setDraw(false);
                
                $points = $team2->getPoints();
                $newPoints = $points + 3;
                $team2->setPoints($newPoints);
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
