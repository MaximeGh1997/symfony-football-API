<?php

namespace App\Controller;

use App\Entity\Groups;
use App\Entity\Matchs;
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
    public function index(MatchsRepository $matchsRepo, GroupsRepository $groupsRepo, Request $request)
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
            'group' => $group
        ]);
    }

    /**
     * Tirage au sort des matchs pour chaque groupe
     * @Route("/admin/matchs/group-{id}/drawing", name="admin_matchs_drawing")  
     * 
     * @return Response
     */
    public function drawing(Groups $group, StadesRepository $stadesRepo, MatchsRepository $matchsRepo, EntityManagerInterface $manager)
    {
        $teams = $group->getTeams()->toArray();
        $stades = $stadesRepo->findAll();

        foreach($teams as $team){
                // Ajout Match 1
                $opponent1 = $teams[mt_rand(0,3)];
                    while($opponent1 == $team){
                        $opponent1 = $teams[mt_rand(0,3)];
                    }

                $stade1 = $stades[mt_rand(0,9)];
                
                $teamId = $team->getId();
                $opponent1Id = $opponent1->getId();
                $teamMatchs = count($matchsRepo->findByTeam($teamId));
                $opponent1Matchs = count($matchsRepo->findByTeam($opponent1Id));

                
                if($teamMatchs < 3 && $opponent1Matchs < 3){
                    $match1 = new Matchs();

                    $match1->setTeam1($team)
                        ->setTeam2($opponent1)
                        ->setDate(new \DateTime('Europe/Brussels'))
                        ->setStade($stade1)
                        ->setGroupName($group);

                    $manager->persist($match1);
                    $manager->persist($team);
                    $manager->persist($opponent1);
                    $manager->flush();
                }

                // ajout Match 2
                $opponent2 = $teams[mt_rand(0,3)];
                    while($opponent2 == $team || $opponent2 == $opponent1){
                        $opponent2 = $teams[mt_rand(0,3)];
                    }

                $stade2 = $stades[mt_rand(0,9)];

                $opponent2Id = $opponent2->getId();
                $teamMatchs = count($matchsRepo->findByTeam($teamId));
                $opponent2Matchs = count($matchsRepo->findByTeam($opponent2Id));

                if($teamMatchs < 3 && $opponent2Matchs < 3){
                    $match2 = new Matchs();

                    $match2->setTeam1($opponent2)
                        ->setTeam2($team)
                        ->setDate(new \DateTime('Europe/Brussels'))
                        ->setStade($stade2)
                        ->setGroupName($group);
                
                    $manager->persist($match2);
                    $manager->persist($team);
                    $manager->persist($opponent2);
                    $manager->flush();
                }

                // ajout Match 3
                $opponent3 = $teams[mt_rand(0,3)];
                    while($opponent3 == $team || $opponent3 == $opponent1 || $opponent3 == $opponent2){
                        $opponent3 = $teams[mt_rand(0,3)];
                    }

                $stade3 = $stades[mt_rand(0,9)];

                $opponent3Id = $opponent3->getId();
                $teamMatchs = count($matchsRepo->findByTeam($teamId));
                $opponent3Matchs = count($matchsRepo->findByTeam($opponent3Id));

                if($teamMatchs < 3 && $opponent3Matchs < 3){
                    $match3 = new Matchs();

                    $match3->setTeam1($team)
                        ->setTeam2($opponent3)
                        ->setDate(new \DateTime('Europe/Brussels'))
                        ->setStade($stade3)
                        ->setGroupName($group);
                
                    $manager->persist($match3);
                    $manager->persist($team);
                    $manager->persist($opponent3);
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
     * @Route("/admin/matchs/emptying", name="admin_matchs_emptying")
     * 
     * @return Response
     */
    public function emptying(MatchsRepository $matchsRepo, EntityManagerInterface $manager)
    {
        $matchs = $matchsRepo->findAll();

        foreach ($matchs as $match) {
                $manager->remove($match);
                $manager->flush();
            }

        $this->addFlash(
            "success",
            "Tout les matchs ont bien été supprimé"
        );
        return $this->redirectToRoute('admin_matchs_index');
    }
}
