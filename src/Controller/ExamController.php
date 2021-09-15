<?php

namespace App\Controller;

use App\Entity\Exam;
use App\Form\ExamType;
use App\Repository\ExamRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\School;

#[Route('/exam')]
class ExamController extends AbstractController
{
    #[Route('/', name: 'app_exam_home', methods: ['GET'])]
    public function home(ExamRepository $examRepository): Response
    {
        return $this->render('exam/home.html.twig', [
            'exams' => $examRepository->getAllWithQueryBuilder(),
        ]);
    }

    #[Route('/list', name: 'app_exam_index', methods: ['GET']), isGranted('ROLE_ADMIN')]
    public function index(ExamRepository $examRepository): Response
    {
        return $this->render('exam/index.html.twig', [
            'exams' => $examRepository->findAll(),
        ]);
    }

    
    #[Route('/school/{id}', name: 'app_exams_from_school_index', methods: ['GET']), isGranted('ROLE_ADMIN')]
    public function examsOf(ExamRepository $examRepository, School $school): Response
    {
        return $this->render('exam/exams_of_index.html.twig', [
            'school' =>$school,
            'exams' => $examRepository->getAllExamsOfWithQueryBuider($school),
        ]);
    }

    #[Route('/school/home/{id}', name: 'app_exams_from_school_home', methods: ['GET'])]
    public function examsActive(ExamRepository $examRepository, School $school): Response
    {
        return $this->render('exam/exams_of_home.html.twig', [
            'school' =>$school,
            'exams' => $examRepository->getAllActiveExamsOfWithQueryBuider($school),
        ]);
    }


    #[Route('/new', name: 'app_exam_new', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $exam = new Exam();
        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $examFile = $form->get('fileName')->getData();
            if($examFile)
            {
                $examFileName = $fileUploader->upload($examFile);
                $exam->setFileName($examFileName);
            }
            $exam->setIsActive(true);
            $exam->setAddedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exam);
            $entityManager->flush();

            return $this->redirectToRoute('app_exam_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/new.html.twig', [
            'exam' => $exam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exam_edit', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function edit(Request $request, Exam $exam, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $examFile = $form->get('fileName')->getData();
            if($examFile)
            {
                $examFileName = $fileUploader->upload($examFile);
                $exam->setFileName($examFileName);
            }
            $exam->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_exam_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exam/edit.html.twig', [
            'exam' => $exam,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exam_delete', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function delete(Request $request, Exam $exam): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exam->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($exam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exam_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/activate/{id}', name: 'app_exam_activate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function activate(Request $request, Exam $exam): Response
    {
        if ($this->isCsrfTokenValid('activate'.$exam->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $exam->setIsActive(true);
            $exam->setUpdatedAt(new \DateTime());
            $entityManager->persist($exam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exam_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desactivate/{id}', name: 'app_exam_desactivate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function desactivate(Request $request, Exam $exam): Response
    {
        if ($this->isCsrfTokenValid('desactivate'.$exam->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $exam->setIsActive(false);
            $exam->setUpdatedAt(new \DateTime());
            $entityManager->persist($exam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exam_index', [], Response::HTTP_SEE_OTHER);
    }
}