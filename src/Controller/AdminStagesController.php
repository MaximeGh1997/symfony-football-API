<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Entity\Stages;
use App\Services\SortService;
use App\Repository\DatesRepository;
use App\Repository\TeamsRepository;
use App\Repository\GroupsRepository;
use App\Repository\MatchsRepository;
use App\Repository\StadesRepository;
use App\Repository\StagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStagesController extends AbstractController
{
    /**
     * @Route("/admin/stages", name="admin_stages_index")
     */
    public function index(TeamsRepository $teamsRepo, GroupsRepository $groupsRepo, SortService $sortService, StagesRepository $stagesRepo, StadesRepository $stadesRepo, DatesRepository $datesRepo, EntityManagerInterface $manager)
    {
        $groups = $groupsRepo->findAll();

        foreach ($groups as $group) {
            $teams = $teamsRepo->findBestsTeams($group->getId(),2);

            foreach ($teams as $team) {
                $beststeams[] = $team;
            }
        }

        foreach ($groups as $group) {
            $teams = $teamsRepo->findBestsTeams($group->getId(),3);
            $third = array_slice($teams, 2);
            $thirds[] = $third[0];
        }

        $thirds = $sortService->array_sort($thirds, 'points', SORT_ASC);
        $beststhirds = array_slice($thirds, 0, 4);

        $qualifiedTeams = array_merge($beststeams, $beststhirds);
        

        return $this->render('admin/stages/index.html.twig', [
            'teams' => $qualifiedTeams,
            'stages' => $stagesRepo->findAll()
        ]);
    }

    /**
     * @Route("/admin/stages-1/drawing", name="admin_stages-1_drawing")
     * 
     * @return Response
     */
    public function EighthFinals(TeamsRepository $teamsRepo, GroupsRepository $groupsRepo, StadesRepository $stadesRepo, DatesRepository $datesRepo, StagesRepository $stagesRepo, EntityManagerInterface $manager, SortService $sortService)
    {
        // DONNER UNE PLACE POUR CHAQUE EQUIPE
        // LES 2 MEILLEURS DE CHAQUE GROUPE
        $teamsG1 = $teamsRepo->findBestsTeams(1,2);
        $T1G1 = $teamsG1[0];
        $T2G1 = $teamsG1[1];

        $teamsG2 = $teamsRepo->findBestsTeams(2,2);
        $T1G2 = $teamsG2[0];
        $T2G2 = $teamsG2[1];

        $teamsG3 = $teamsRepo->findBestsTeams(3,2);
        $T1G3 = $teamsG3[0];
        $T2G3 = $teamsG3[1];

        $teamsG4 = $teamsRepo->findBestsTeams(4,2);
        $T1G4 = $teamsG4[0];
        $T2G4 = $teamsG4[1];

        $teamsG5 = $teamsRepo->findBestsTeams(5,2);
        $T1G5 = $teamsG5[0];
        $T2G5 = $teamsG5[1];

        $teamsG6 = $teamsRepo->findBestsTeams(6,2);
        $T1G6 = $teamsG6[0];
        $T2G6 = $teamsG6[1];

        // LES MEILLEURS 3EMES
        $groups = $groupsRepo->findAll();
        foreach ($groups as $group) {
            $teams = $teamsRepo->findBestsTeams($group->getId(),3);
            $third = array_slice($teams, 2);
            $thirds[] = $third[0];
        }

        $thirds = $sortService->array_sort($thirds, 'points', SORT_ASC);
        $bestsT3 = array_slice($thirds, 0, 4);

        // CREER LES 8 MATCHS
        // MATCH 1
        $stades = $stadesRepo->findAll();
        $dates = $datesRepo->findFreeDates();
        $stage = $stagesRepo->findOneByName('Huitième de finale');

        $match1 = new Matchs();

        $match1->setTeam1($T2G1)
               ->setTeam2($T2G2)
               ->setDate($dates[0])
               ->setStade($stades[6])
               ->setStage($stage);

        $manager->persist($match1);

        //MATCH 2
        $match2 = new Matchs();

        $match2->setTeam1($T1G1)
               ->setTeam2($T2G3)
               ->setDate($dates[1])
               ->setStade($stades[0])
               ->setStage($stage);

        $manager->persist($match2);

        //MATCH 3
        $match3 = new Matchs();

        $match3->setTeam1($T1G3)
               ->setTeam2($bestsT3[0])
               ->setDate($dates[2])
               ->setStade($stades[8])
               ->setStage($stage);

        $manager->persist($match3);

        //MATCH 4
        $match4 = new Matchs();

        $match4->setTeam1($T1G2)
               ->setTeam2($bestsT3[1])
               ->setDate($dates[3])
               ->setStade($stades[7])
               ->setStage($stage);

        $manager->persist($match4);

        //MATCH 5
        $match5 = new Matchs();

        $match5->setTeam1($T2G4)
               ->setTeam2($T2G5)
               ->setDate($dates[4])
               ->setStade($stades[10])
               ->setStage($stage);

        $manager->persist($match5);

        //MATCH 6
        $match6 = new Matchs();

        $match6->setTeam1($T1G6)
               ->setTeam2($bestsT3[2])
               ->setDate($dates[5])
               ->setStade($stades[5])
               ->setStage($stage);

        $manager->persist($match6);

        //MATCH 7
        $match7 = new Matchs();

        $match7->setTeam1($T1G4)
               ->setTeam2($T2G6)
               ->setDate($dates[6])
               ->setStade($stades[11])
               ->setStage($stage);

        $manager->persist($match7);

        //MATCH 8
        $match8 = new Matchs();

        $match8->setTeam1($T1G5)
               ->setTeam2($bestsT3[3])
               ->setDate($dates[7])
               ->setStade($stades[9])
               ->setStage($stage);

        $manager->persist($match8);
        
        $manager->flush();

        $this->addFlash(
            "success",
            "Les huitièmes de finale ont bien été ajouté"
        );
        return $this->redirectToRoute('admin_stages_index');
    }

    /**
     * @Route("/admin/stages-2/drawing", name="admin_stages-2_drawing")
     * 
     * @return Response
     */
    public function QuarterFinals(TeamsRepository $teamsRepo, GroupsRepository $groupsRepo, StadesRepository $stadesRepo, DatesRepository $datesRepo, StagesRepository $stagesRepo, MatchsRepository $matchsRepo, EntityManagerInterface $manager, SortService $sortService)
    {   
        // RECUPERER LES MATCHS DE 1/8
        $matchs = $matchsRepo->findByStage(1);

        // RECUPERER LES GAGNANTS PAR MATCH
        foreach ($matchs as $match) {
            $winners[] = $match->getWinner();
        }

        // CREER LES MATCHS
        $stades = $stadesRepo->findAll();
        $dates = $datesRepo->findFreeDates();
        $stage = $stagesRepo->findOneByName('Quart de finale');

        //MATCH 1
        $match1 = new Matchs();

        $match1->setTeam1($winners[4])
               ->setTeam2($winners[5])
               ->setDate($dates[0])
               ->setStade($stades[4])
               ->setStage($stage);

        $manager->persist($match1);
        
        //MATCH 2
        $match2 = new Matchs();

        $match2->setTeam1($winners[2])
               ->setTeam2($winners[0])
               ->setDate($dates[1])
               ->setStade($stades[1])
               ->setStage($stage);

        $manager->persist($match2);

        //MATCH 3
        $match3 = new Matchs();

        $match3->setTeam1($winners[3])
               ->setTeam2($winners[1])
               ->setDate($dates[2])
               ->setStade($stades[3])
               ->setStage($stage);

        $manager->persist($match3);

        //MATCH 4
        $match4 = new Matchs();

        $match4->setTeam1($winners[6])
               ->setTeam2($winners[7])
               ->setDate($dates[3])
               ->setStade($stades[2])
               ->setStage($stage);

        $manager->persist($match4);

        $manager->flush();

        $this->addFlash(
            "success",
            "Les quarts de finale ont bien été ajouté"
        );
        return $this->redirectToRoute('admin_stages_index');
    }

    /**
     * @Route("/admin/stages-3/drawing", name="admin_stages-3_drawing")
     * 
     * @return Response
     */
    public function SemiFinals(TeamsRepository $teamsRepo, GroupsRepository $groupsRepo, StadesRepository $stadesRepo, DatesRepository $datesRepo, StagesRepository $stagesRepo, MatchsRepository $matchsRepo, EntityManagerInterface $manager, SortService $sortService)
    {   
        // RECUPERER LES MATCHS DE 1/4
        $matchs = $matchsRepo->findByStage(2);

        // RECUPERER LES GAGNANTS PAR MATCH
        foreach ($matchs as $match) {
            $winners[] = $match->getWinner();
        }

        // CREER LES MATCHS
        $stades = $stadesRepo->findAll();
        $dates = $datesRepo->findFreeDates();
        $stage = $stagesRepo->findOneByName('Demi finale');

        //MATCH 1
        $match1 = new Matchs();

        $match1->setTeam1($winners[0])
               ->setTeam2($winners[1])
               ->setDate($dates[0])
               ->setStade($stades[0])
               ->setStage($stage);

        $manager->persist($match1);
        
        //MATCH 2
        $match2 = new Matchs();

        $match2->setTeam1($winners[3])
               ->setTeam2($winners[2])
               ->setDate($dates[1])
               ->setStade($stades[0])
               ->setStage($stage);

        $manager->persist($match2);

        $manager->flush();

        $this->addFlash(
            "success",
            "Les demis finale ont bien été ajouté"
        );
        return $this->redirectToRoute('admin_stages_index');
    }

    /**
     * @Route("/admin/stages-4/drawing", name="admin_stages-4_drawing")
     * 
     * @return Response
     */
    public function Final(TeamsRepository $teamsRepo, GroupsRepository $groupsRepo, StadesRepository $stadesRepo, DatesRepository $datesRepo, StagesRepository $stagesRepo, MatchsRepository $matchsRepo, EntityManagerInterface $manager, SortService $sortService)
    {   
        // RECUPERER LES MATCHS DE 1/2
        $matchs = $matchsRepo->findByStage(3);

        // RECUPERER LES GAGNANTS PAR MATCH
        foreach ($matchs as $match) {
            $winners[] = $match->getWinner();
        }

        // CREER LES MATCHS
        $stades = $stadesRepo->findAll();
        $dates = $datesRepo->findFreeDates();
        $stage = $stagesRepo->findOneByName('Finale');

        //FINALE
        $finale = new Matchs();

        $finale->setTeam1($winners[0])
               ->setTeam2($winners[1])
               ->setDate($dates[0])
               ->setStade($stades[0])
               ->setStage($stage);

        $manager->persist($finale);
        $manager->flush();

        $this->addFlash(
            "success",
            "La finale à bien été ajouté"
        );
        return $this->redirectToRoute('admin_stages_index');
    }
}
