<?php

namespace App\Controller;

use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comment-{id}/del", name="comment_del")
     * @IsGranted("ROLE_USER")
     */
    public function index(Comments $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash(
            "success",
            "Votre commentaire à bien été supprimé"
        );
        return $this->redirectToRoute('account_index');
    }
}
