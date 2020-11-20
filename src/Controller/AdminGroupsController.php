<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Groups;
use App\Repository\TeamsRepository;
use App\Repository\GroupsRepository;
use App\Repository\MatchsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminGroupsController extends AbstractController
{
    /**
     * @Route("/admin/groups", name="admin_groups_index")
     */
    public function index(GroupsRepository $groupsRepo, TeamsRepository $teamsRepo)
    {
        return $this->render('admin/groups/index.html.twig', [
            'groups' => $groupsRepo->findAll(),
            'teams' => $teamsRepo->findAll()
        ]);
    }

    /**
     * @Route("/admin/groups/drawing", name="admin_groups_drawing")
     * 
     * @return Response
     */
    public function drawing(GroupsRepository $groupsRepo, TeamsRepository $teamsRepo, EntityManagerInterface $manager)
    {
        $groups = $groupsRepo->findAll();

        foreach($groups as $group) {
            $teams = $teamsRepo->findFreeTeams();
            $countFreeTeams = count($teams);
            $countGroupTeams = count($group->getTeams());
            $max = $countFreeTeams - 1;

            while($countGroupTeams < 4 && $countFreeTeams > 0) {
                $choosenTeam = $teams[mt_rand(0,$max)];
                $group->addTeam($choosenTeam);
                $manager->persist($choosenTeam);
                $manager->persist($group);
                $manager->flush();

                $teams = $teamsRepo->findFreeTeams();
                $countFreeTeams = count($teams);
                $countGroupTeams = count($group->getTeams());
                $max = $countFreeTeams - 1;
            }
        }

        $this->addFlash(
            "success",
            "Les groupes ont bien été formé"
        );
        return $this->redirectToRoute('admin_groups_index');
    }

    /**
     * @Route("/admin/groups/{id}/compose", name="admin_groups_compose")
     * 
     * @return Response
     */
    public function compose(Groups $group, TeamsRepository $teamsRepo)
    {
        $freeTeams = $teamsRepo->findFreeTeams();

        return $this->render('admin/groups/compose.html.twig', [
            'group' => $group,
            'freeTeams' => $freeTeams
        ]);
    }

    /**
     * @Route("/admin/groups/{id}/compose/treatment", name="compose_treatment")
     * 
     */
    public function composeTreatment(Groups $group, TeamsRepository $teamsRepo, Request $request, EntityManagerInterface $manager)
    {
        $team1Id = $request->request->get('team1');
        $team2Id = $request->request->get('team2');
        $team3Id = $request->request->get('team3');
        $team4Id = $request->request->get('team4');
        $params = $request->request->all();
        
        // GESTION ERREUR SI UN CHAMP VIDE
        foreach($params as $param) {
            if ($param == "null") {
                $this->addFlash(
                    "danger",
                    "Veuillez selectionnez 4 équipes"
                );
                return $this->redirectToRoute('admin_groups_compose', [
                    'id' => $group->getId()
                ]);
            }
        }

        
        // GESTION ERREUR SI 2 EQUIPES IDENTIQUES 
        if ($team1Id == $team2Id || $team1Id == $team3Id || $team1Id == $team4Id) {
            $this->addFlash(
                "danger",
                "Veuillez selectionnez 4 équipes différentes"
            );
            return $this->redirectToRoute('admin_groups_compose', [
                'id' => $group->getId()
            ]);
        } elseif ($team2Id == $team3Id || $team2Id == $team4Id) {
            $this->addFlash(
                "danger",
                "Veuillez selectionnez 4 équipes différentes"
            );
            return $this->redirectToRoute('admin_groups_compose', [
                'id' => $group->getId()
            ]);
        } elseif ($team3Id == $team4Id) {
            $this->addFlash(
                "danger",
                "Veuillez selectionnez 4 équipes différentes"
            );
            return $this->redirectToRoute('admin_groups_compose', [
                'id' => $group->getId()
            ]);
        }

        $countGroupTeams = count($group->getTeams());
        if ($countGroupTeams >= 4) {
            $this->addFlash(
                "danger",
                "Ce groupe est déjà rempli"
            );
            return $this->redirectToRoute('admin_groups_index');
        }
        
        $team1 = $teamsRepo->findById($team1Id);
        $team2 = $teamsRepo->findById($team2Id);
        $team3 = $teamsRepo->findById($team3Id);
        $team4 = $teamsRepo->findById($team4Id);

        $group->addTeam($team1[0]);
        $group->addTeam($team2[0]);
        $group->addTeam($team3[0]);
        $group->addTeam($team4[0]);

        $manager->persist($group);
        $manager->flush();

        $this->addFlash(
            "success",
            "Le groupe à bien été rempli"
        );
        return $this->redirectToRoute('admin_groups_index');
    }

    /**
     * @Route("/admin/groups/emptying", name="admin_groups_emptying")
     * 
     * @return Response
     */
    public function emptying(GroupsRepository $groupsRepo, MatchsRepository $matchsRepo, EntityManagerInterface $manager)
    {
        $groups = $groupsRepo->findAll();
        
        if($matchsRepo->findAll() == null){
            
            foreach ($groups as $group) {
            $teams = $group->getTeams()->toArray();
                
                foreach ($teams as $team) {
                    $group->removeTeam($team);
                    $team->setPoints(0);
                    $manager->persist($team);
                    $manager->persist($group);
                }
            }

            $manager->flush();

            $this->addFlash(
                "success",
                "Les groupes ont bien été vidé"
            );
            return $this->redirectToRoute('admin_groups_index');
        }else{
            $this->addFlash(
                "danger",
                "Vous ne pouvez pas vider les groupes tant qu'il y des matchs au calendrier !"
            );
            return $this->redirectToRoute('admin_groups_index');
        }
    }
}
