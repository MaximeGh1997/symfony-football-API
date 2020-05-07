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
                "L'équipe {$team->getName()} à bien été ajoutée"
            );

            return $this->redirectToRoute('admin_teams_index');
        }

        return $this->render('admin/teams/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/teams/{id}/edit", name="admin_teams_edit")
     * 
     * @return Response
     */
    public function edit(Teams $team, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //$team->setCreatedAt(new \DateTime('Europe/Brussels'));

            $manager->persist($team);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'équipe {$team->getName()} à bien été modifiée"
            );

            return $this->redirectToRoute('admin_teams_index');
        }

        return $this->render('admin/teams/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    /**
     * @Route("/admin/teams/{id}/delete", name="admin_teams_delete")
     * 
     * @return Response
     */
    public function delete(Teams $team, EntityManagerInterface $manager)
    {
        $manager->remove($team);
        $manager->flush();
        $this->addFlash(
            "success",
            "L'équipe {$team->getName()} a bien été supprimée"
        );
        return $this->redirectToRoute('admin_teams_index');
    }
}
