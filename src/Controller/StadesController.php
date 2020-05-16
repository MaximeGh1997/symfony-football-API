<?php

namespace App\Controller;

use App\Entity\Stades;
use App\Repository\StadesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StadesController extends AbstractController
{
    /**
     * @Route("/stades", name="stades_index")
     */
    public function index(StadesRepository $stadesRepo)
    {
        return $this->render('stades/index.html.twig', [
            'stades' => $stadesRepo->findOrderName()
        ]);
    }

    /**
     * @Route("/stades/{id}/show", name="stades_show")
     * 
     * @return Response
     */
    public function show(Stades $stade)
    {
        return $this->render('stades/show.html.twig', [
            'stade' => $stade
        ]);
    }
}
