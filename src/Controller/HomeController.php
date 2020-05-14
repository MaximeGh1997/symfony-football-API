<?php

namespace App\Controller;

use DateTime;
use App\Repository\TeamsRepository;
use App\Repository\MatchsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(MatchsRepository $matchsRepo)
    {
        $now = new \DateTime('Europe/Brussels');

        return $this->render('home/index.html.twig', [
            'lastsMatchs' => $matchsRepo->findLastsResults(3),
            'nextsMatchs' => $matchsRepo->findByDate('ASC', 3),
            'now' => $now
        ]);
    }
}
