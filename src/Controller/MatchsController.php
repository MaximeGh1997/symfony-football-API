<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MatchsController extends AbstractController
{
    /**
     * @Route("/matchs/{id}/show", name="matchs_show")
     */
    public function show(Matchs $match, CommentsRepository $commentsRepo, Request $request, EntityManagerInterface $manager)
    {   
        $matchId = $match->getId();
        $comments = $commentsRepo->findByMatchAndDate($matchId);
        $ratingCom = $commentsRepo->findByRatings($matchId);
        
        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setMatchNbr($match)
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('matchs_show', array('id' => $match->getId()));
        }

        return $this->render('matchs/show.html.twig', [
            'match' => $match,
            'comments' => $comments,
            'rating' => $ratingCom,
            'form' => $form->createView()
        ]);
    }
}
