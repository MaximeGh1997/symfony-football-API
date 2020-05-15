<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Repository\TeamsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamsController extends AbstractController
{
    /**
     * @Route("/teams", name="teams_index")
     */
    public function index(TeamsRepository $teamsRepo)
    {
        return $this->render('teams/index.html.twig', [
            'teams' => $teamsRepo->findOrderName()
        ]);
    }

    /**
     * @Route("/teams/{id}/show", name="teams_show")
     * 
     * @return Response
     */
    public function show(Teams $team)
    {
        return $this->render('teams/show.html.twig', [
            'team' => $team
        ]);
    }
}
