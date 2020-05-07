<?php

namespace App\Controller;

use App\Entity\Stades;
use App\Form\StadeType;
use App\Repository\StadesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStadesController extends AbstractController
{
    /**
     * @Route("/admin/stades", name="admin_stades_index")
     */
    public function index(StadesRepository $stadesRepo)
    {
        return $this->render('admin/stades/index.html.twig', [
            'stades' => $stadesRepo->findAll()
        ]);
    }

    /**
     * @Route("/admin/stades/new", name="admin_stades_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $stade = new Stades();
        $form = $this->createForm(StadeType::class, $stade);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($stade);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le {$stade->getName()} à bien été ajouté"
            );

            return $this->redirectToRoute('admin_stades_index');
        }

        return $this->render('admin/stades/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/stades/{id}/edit", name="admin_stades_edit")
     * 
     * @return Response
     */
    public function edit(Stades $stade, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(StadeType::class, $stade);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //$team->setCreatedAt(new \DateTime('Europe/Brussels'));

            $manager->persist($stade);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le {$stade->getName()} à bien été modifié"
            );

            return $this->redirectToRoute('admin_stades_index');
        }

        return $this->render('admin/stades/edit.html.twig', [
            'form' => $form->createView(),
            'stade' => $stade
        ]);
    }

    /**
     * @Route("/admin/stades/{id}/delete", name="admin_stades_delete")
     * 
     * @return Response
     */
    public function delete(Stades $stade, EntityManagerInterface $manager)
    {
        $manager->remove($stade);
        $manager->flush();
        $this->addFlash(
            "success",
            "Le {$stade->getName()} a bien été supprimé"
        );
        return $this->redirectToRoute('admin_stades_index');
    }
}
