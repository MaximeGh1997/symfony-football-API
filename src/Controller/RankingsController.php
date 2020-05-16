<?php

namespace App\Controller;

use App\Repository\TeamsRepository;
use App\Repository\GroupsRepository;
use App\Repository\MatchsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RankingsController extends AbstractController
{
    /**
     * @Route("/rankings&results", name="rankings_index")
     */
    public function index(GroupsRepository $groupsRepo, MatchsRepository $matchsRepo, TeamsRepository $teamsRepo, Request $request)
    {
        $group = $groupsRepo->findOneByName('A');
        $groupName = $request->request->get('group');

        if($groupName != null){
            $group = $groupsRepo->findOneByName($groupName);
        }

        $teamId = $request->request->get('team');
        $matchs = null;
        if($teamId != null){
            $matchs = $matchsRepo->findByTeam($teamId);
            $team = $teamsRepo->findOneById($teamId);
            $group = $team->getGroupName();
        }

        return $this->render('rankings/index.html.twig', [
            'group' => $group,
            'groups' => $groupsRepo->findAll(),
            'matchs' => $matchs
        ]);
    }
}
