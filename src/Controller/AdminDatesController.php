<?php

namespace App\Controller;

use App\Entity\Dates;
use App\Form\DateForm;
use App\Form\DateType;
use App\Form\CalendarDateFormType;
use App\Repository\DatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDatesController extends AbstractController
{
    /**
     * @Route("/admin/dates", name="admin_dates_index")
     */
    public function index(DatesRepository $datesRepo)
    {
        return $this->render('admin/dates/index.html.twig', [
            'dates' => $datesRepo->findByDate('ASC'),
            'freeDates' => $datesRepo->findFreeDates()
        ]);
    }

    /**
     * @Route("/admin/dates/new", name="admin_dates_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $date = new Dates();
        $form = $this->createForm(CalendarDateFormType::class, $date);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($date);
            $manager->flush();

            $this->addFlash(
                'success',
                "La date à bien été ajoutée"
            );

            return $this->redirectToRoute('admin_dates_index');
        }

        return $this->render('admin/dates/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/dates/{id}/edit", name="admin_dates_edit")
     * 
     * @return Response
     */
    public function edit(Dates $date, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(CalendarDateFormType::class, $date);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($date);
            $manager->flush();

            $this->addFlash(
                'success',
                "La date à bien été modifiée"
            );

            return $this->redirectToRoute('admin_dates_index');
        }

        return $this->render('admin/dates/edit.html.twig', [
            'form' => $form->createView(),
            'date' => $date
        ]);
    }

    /**
     * @Route("/admin/dates/{id}/delete", name="admin_dates_delete")
     * 
     * @return Response
     */
    public function delete(Dates $date, EntityManagerInterface $manager)
    {
        $manager->remove($date);
        $manager->flush();
        $this->addFlash(
            "success",
            "La date a bien été supprimée"
        );
        return $this->redirectToRoute('admin_dates_index');
    }
}
