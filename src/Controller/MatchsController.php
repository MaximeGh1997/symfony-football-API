<?php

namespace App\Controller;

use App\Entity\Matchs;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MatchsController extends AbstractController
{
    /**
     * @Route("/matchs/{id}/show", name="matchs_show")
     */
    public function show(Matchs $match)
    {
        return $this->render('matchs/show.html.twig', [
            'match' => $match
        ]);
    }
}
