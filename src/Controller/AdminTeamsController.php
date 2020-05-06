<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Form\TeamType;
use App\Repository\TeamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTeamsController extends AbstractController
{
    /**
     * @Route("/admin/teams", name="admin_teams_index")
     */
    public function index(TeamsRepository $teamsRepo)
    {
        return $this->render('admin/teams/index.html.twig', [
            'teams' => $teamsRepo->findAll()
        ]);
    }

    /**
     * @Route("/admin/teams/new", name="admin_teams_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $team = new Teams();
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($team);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'événement {$team->getName()} à bien été ajouté"
            );

            return $this->redirectToRoute('admin_teams_index');
        }

        return $this->render('admin/teams/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{id}/edit", name="admin_edit")
     * 
     * @return Response
     */
    /*
    public function edit(Event $event, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $event->setCreatedAt(new \DateTime('Europe/Brussels'));

            $manager->persist($event);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'événement {$event->getName()} à bien été modifié"
            );

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/admin/{id}/delete", name="admin_delete")
     * 
     * @return Response
     */
    /*
    public function delete(Event $event, EntityManagerInterface $manager)
    {
        $manager->remove($event);
        $manager->flush();
        $this->addFlash(
            "success",
            "L'événement {$event->getName()} a bien été supprimé"
        );
        return $this->redirectToRoute('admin_index');
    }

    */
}
