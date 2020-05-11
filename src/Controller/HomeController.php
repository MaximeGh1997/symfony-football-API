<?php

namespace App\Controller;

use App\Repository\TeamsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TeamsRepository $teamsRepo)
    {
        return $this->render('home/index.html.twig', [
            'teams' => $teamsRepo->findFreeTeams()
        ]);
    }
}
