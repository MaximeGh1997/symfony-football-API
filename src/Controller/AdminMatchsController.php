<?php

namespace App\Controller;

use App\Repository\GroupsRepository;
use App\Repository\MatchsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMatchsController extends AbstractController
{
    /**
     * @Route("/admin/matchs", name="admin_matchs_index")
     */
    public function index(MatchsRepository $matchsRepo, GroupsRepository $groupsRepo, Request $request)
    {
        $matchs = $matchsRepo->findAll();
        $group = null;

        $groupId = $request->request->get('group'); // récupération de l'id correspondant au groupe
        if($groupId != null){
            $matchs = $matchsRepo->findByGroup($groupId);
            $group = $groupsRepo->find($groupId);
        }

        return $this->render('admin/matchs/index.html.twig', [
            'matchs' => $matchs,
            'groups' => $groupsRepo->findAll(),
            'group' => $group
        ]);
    }

    /**
     * @Route("/admin/matchs/emptying", name="admin_matchs_emptying")
     * 
     * @return Response
     */
    public function emptying(MatchsRepository $matchsRepo, EntityManagerInterface $manager)
    {
        $matchs = $matchsRepo->findAll();

        foreach ($matchs as $match) {
                $manager->remove($match);
                $manager->flush();
            }

        $this->addFlash(
            "success",
            "Tout les matchs ont bien été supprimé"
        );
        return $this->redirectToRoute('admin_matchs_index');
    }
}
