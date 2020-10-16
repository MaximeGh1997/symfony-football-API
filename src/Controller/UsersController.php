<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\UsersRepository;
use Symfony\Component\Form\FormError;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * Permet d'afficher le profil d'un utilisateur
     * @Route("/user/{id}", name="user_show")
     */
    public function index(Users $user, CommentsRepository $commentsRepo)
    {
        $userId = $user->getId();
        $comments = $commentsRepo->findByUserAndDate($userId);

        return $this->render('users/index.html.twig', [
            'user' => $user,
            'comments' => $comments
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount(CommentsRepository $commentsRepo)
    {
        $userId = $this->getUser()->getId();
        $comments = $commentsRepo->findByUserAndDate($userId);

        return $this->render('users/index.html.twig',[
            'user' => $this->getUser(),
            'comments' => $comments
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register")
     *
     * @return void
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $user = new Users();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $file = $form['picture']->getData();

            if(!empty($file)){
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }

            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé'
            );

            return $this->redirectToRoute('app_login');


        }

        return $this->render('users/registration.html.twig',[
            "form" => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *@Route("/account/edit", name="account_edit")
     *@IsGranted("ROLE_USER")

     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager){
        
        $user = $this->getUser(); //récup l'utilisateur connecté
        $userPic = $user->getPicture();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $file = $form['picture']->getData();

            if(!empty($file)){
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }else{
                if(!empty($userPic)){
                    $user->setPicture($userPic);
                }
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre profil à bien été modifié'
            );

            return $this->redirectToRoute('account_index');
        }

        return $this->render('users/edit.html.twig',[

            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier le mdp
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

        $passwordUpdate = new PasswordUpdate(); // fausse entité

        $user = $this->getUser(); // récup l'utilisateur connecté

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // verification que le mot de passe corresponde à l'ancien (oldPassword)
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())){
                //Gérer l'érreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez inséré est incorrect"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user,$newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe à bien été modifié'
                );

                return $this->redirectToRoute('account_index');
            } 

        }

        return $this->render('users/password.html.twig',[

            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mdp depuis le front VueJS
     * @Route("/password-edit", name="editPassword", methods="POST")
     *
     * @return Response
     */
    public function editPassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, UsersRepository $usersRepo){

        $userId = $request->get('userId');
        $oldPassword = $request->get('oldPassword');

        $user = $usersRepo->findById($userId); // récup l'utilisateur connecté
        $user = $user[0];

            // verification que le mot de passe corresponde à l'ancien (oldPassword)
            if(!password_verify($oldPassword, $user->getPassword())){
                //Gérer l'érreur
                $response = new Response();
                $response->setContent('Votre ancien mot de passe est incorrect !');
                $response->setStatusCode(401);
                return $response;
            }else{
                $newPassword = $request->get('newPassword');
                $hash = $encoder->encodePassword($user,$newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $response = new Response();
                $response->setStatusCode(200);
                return $response;
            }
    }

    /**
     * Permet de supp l'image de l'user
     * @Route("/account/delimg", name="account_delimg")
     * @IsGranted("ROLE_USER")
     *
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function removeImage(EntityManagerInterface $manager, Request $request, UsersRepository $usersRepo){

        $user = $this->getUser();
        $unknow = "unknow.jpg";
        if($user->getPicture() !== $unknow){
            unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
        }
        $user->setPicture($unknow);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre image à bien été supprimée'
        );

        return $this->redirectToRoute('account_index');
    }

    /**
     * Permet de supp l'image de l'user depuis front VueJS
     * @Route("/remove-picture", name="removePicture", methods="POST")
     * @return Response
     *
     */
    public function removePicture(EntityManagerInterface $manager, Request $request, UsersRepository $usersRepo){

        $userId = $request->get('userId');
        $user = $usersRepo->findById($userId); // récup l'utilisateur connecté
        $user = $user[0];

        $unknow = "unknow.jpg";
        if($user->getPicture() !== $unknow){
            unlink($this->getParameter('uploads_directory').'/'.$user->getPicture());
        }
        $user->setPicture($unknow);
        $manager->persist($user);
        $manager->flush();

        $response = new Response();
        $response->setStatusCode(200);
        return $response;

    }

    /**
     * @Route("/upload-picture", name="uploadPicture", methods="POST")
     * @return Response
     */
    public function uploadPicture(Request $request, EntityManagerInterface $manager, UsersRepository $usersRepo)
    {
        $userId = $request->get('userId');
        $user = $usersRepo->findById($userId); // récup l'utilisateur connecté
        $user = $user[0];

        $file = $request->files->get('file');

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $user->setPicture($newFilename);
                $manager->persist($user);
                $manager->flush();
                
                $response = new Response();
                $response->setStatusCode(200);
                return $response;
    }
}
