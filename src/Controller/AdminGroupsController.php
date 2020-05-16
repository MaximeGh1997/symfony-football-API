<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Groups;
use App\Repository\TeamsRepository;
use App\Repository\GroupsRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/admin/groups/emptying", name="admin_groups_emptying")
     * 
     * @return Response
     */
    public function emptying(GroupsRepository $groupsRepo, EntityManagerInterface $manager)
    {
        $groups = $groupsRepo->findAll();

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
    }
}
