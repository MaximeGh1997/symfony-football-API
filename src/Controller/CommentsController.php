<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Repository\UsersRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Permet de supp un commentaire depuis front VueJS
     * @Route("/remove-comment", name="removeComment", methods="POST")
     * @return Response
     */
    public function removeComment(Request $request, EntityManagerInterface $manager, UsersRepository $usersRepo, CommentsRepository $commentsRepo)
    {   
        $userId = $request->get('userId');
        $commentId = $request->get('commentId');

        $comment = $commentsRepo->findById($commentId);
        $comment = $comment[0];
        $authorId = $comment->getAuthor()->getId();
        
        if($authorId == $userId) {
            $manager->remove($comment);
            $manager->flush();
            $response = new Response();
            $response->setStatusCode(200);
            return $response;
        } else {
            $response = new Response();
            $response->setContent('Vous n\'êtes pas autorisé a éffectué cette opération !');
            $response->setStatusCode(401);
            return $response;
        }
    }
}
