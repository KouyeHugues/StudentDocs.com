<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Repository\CompetitionRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/competition')]
class CompetitionController extends AbstractController
{
    #[Route('/', name: 'app_competition_home', methods: ['GET'])]
    public function home(CompetitionRepository $competitionRepository): Response
    {
        return $this->render('competition/home.html.twig', [
            'competitions' => $competitionRepository->getAllWithQueryBuilder(),
        ]);
    }

    #[Route('/list', name: 'app_competition_index', methods: ['GET']), isGranted('ROLE_ADMIN')]
    public function index(CompetitionRepository $competitionRepository): Response
    {
        return $this->render('competition/index.html.twig', [
            'competitions' => $competitionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_competition_new', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitionFile = $form->get('fileName')->getData();
            if($competitionFile)
            {
                $competitionFileName = $fileUploader->upload($competitionFile);
                $competition->setFileName($competitionFileName);
            }
            $competition->setIsActive(true);
            $competition->setAddedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($competition);
            $entityManager->flush();

            return $this->redirectToRoute('app_competition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('competition/new.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_competition_edit', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function edit(Request $request, Competition $competition, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitionFile = $form->get('fileName')->getData();
            if($competitionFile)
            {
                $competitionFileName = $fileUploader->upload($competitionFile);
                $competition->setFileName($competitionFileName);
            }
            $competition->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_competition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('competition/edit.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_competition_delete', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function delete(Request $request, Competition $competition): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($competition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_competition_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/activate/{id}', name: 'app_competition_activate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function activate(Request $request, Competition $competition): Response
    {
        if ($this->isCsrfTokenValid('activate'.$competition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $competition->setIsActive(true);
            $competition->setUpdatedAt(new \DateTime());
            $entityManager->persist($competition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_competition_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desactivate/{id}', name: 'app_competition_desactivate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function desactivate(Request $request, Competition $competition): Response
    {
        if ($this->isCsrfTokenValid('desactivate'.$competition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $competition->setIsActive(false);
            $competition->setUpdatedAt(new \DateTime());
            $entityManager->persist($competition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_competition_index', [], Response::HTTP_SEE_OTHER);
    }
}