<?php

namespace App\Controller;

use App\Repository\MatchsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagesController extends AbstractController
{
    /**
     * @Route("/stages", name="stages_index")
     */
    public function index(MatchsRepository $matchsRepo)
    {
        return $this->render('stages/index.html.twig', [
            'eight' => $matchsRepo->findByStage(1),
            'quarter' => $matchsRepo->findByStage(2),
            'semi' => $matchsRepo->findByStage(3),
            'final' => $matchsRepo->findByStage(4)
        ]);
    }
}
