<?php

namespace App\Controller;

use App\Entity\Stages;
use App\Services\SortService;
use App\Repository\DatesRepository;
use App\Repository\TeamsRepository;
use App\Repository\GroupsRepository;
use App\Repository\StadesRepository;
use App\Repository\StagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStagesController extends AbstractController
{
    /**
     * @Route("/admin/stages", name="admin_stages_index")
     */
    public function index(TeamsRepository $teamsRepo, GroupsRepository $groupsRepo, SortService $sortService, StagesRepository $stagesRepo)
    {
        $groups = $groupsRepo->findAll();

        foreach ($groups as $group) {
            $teams = $teamsRepo->findBestsTeams($group->getId(),2);

            foreach ($teams as $team) {
                $beststeams[] = $team;
            }
        }

        foreach ($groups as $group) {
            $teams = $teamsRepo->findBestsTeams($group->getId(),3);
            $third = array_slice($teams, 2);
            $thirds[] = $third[0];
        }

        $thirds = $sortService->array_sort($thirds, 'points', SORT_ASC);
        $beststhirds = array_slice($thirds, 0, 4);

        $qualifiedTeams = array_merge($beststeams, $beststhirds);

        
        // TIRAGE AU SORT 1/8 DE FINALE

        $chunk = array_chunk($qualifiedTeams, 8);
        $leftSide = $chunk[0];
        $rightSide = $chunk[1];

        

        //

        return $this->render('admin/stages/index.html.twig', [
            'teams' => $qualifiedTeams,
            'stages' => $stagesRepo->findAll()
        ]);
    }

    /**
     * @Route("/admin/stages-{id}/drawing", name="admin_stages_drawing")
     * 
     * @return Response
     */
    public function drawing(Stages $stage, StadesRepository $stadesRepo, DatesRepository $datesRepo, EntityManagerInterface $manager)
    {

    }
}
