<?php

namespace App\Controller;

use App\Entity\School;
use App\Form\SchoolType;
use App\Repository\SchoolRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\University;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/school')]
class SchoolController extends AbstractController
{
    #[Route('/', name: 'app_school_home', methods: ['GET'])]
    public function home(SchoolRepository $schoolRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('school/home.html.twig', [
            'schools' => $paginator->paginate($schoolRepository->getAllWithQueryBuilder(), $request->query->getInt('page', 1), 10)
        ]);
    }

    #[Route('/list', name: 'app_school_index', methods: ['GET']), isGranted('ROLE_ADMIN')]
    public function index(SchoolRepository $schoolRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('school/index.html.twig', [
            'schools' => $paginator->paginate($schoolRepository->findAll(), $request->query->getInt('page', 1), 10)
        ]);
    }

    #[Route('/university/{id}', name: 'app_school_from_university_index', methods: ['GET']), isGranted('ROLE_ADMIN')]
    public function schoolOf(SchoolRepository $schoolRepository, University $university, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('school/schools_of_index.html.twig', [
            'university' => $university,
            'schools' => $paginator->paginate($schoolRepository->getAllSchoolsOfWithQueryBuider($university), $request->query->getInt('page', 1), 10)
        ]);
    }

    #[Route('/university/home/{id}', name: 'app_school_active_from_university_index', methods: ['GET'])]
    public function schoolsActive(SchoolRepository $schoolRepository, University $university, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('school/schools_of_home.html.twig', [
            'university' => $university,
            'schools' => $paginator->paginate($schoolRepository->getAllActivesSchoolsOfWithQueryBuider($university), $request->query->getInt('page', 1), 10)
        ]);
    }

    #[Route('/new', name: 'app_school_new', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $message = null;
        $school = new School();
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();

            if ($logo)
                $fichier = $school->getName() . '.' . $logo->guessExtension(); {
                try {
                    $logo->move($this->getParameter('upload_directory'), $fichier);
                    $school->setLogo($fichier);
                } catch (\Throwable $th) {
                    $message = "Impossible de télécharger le logo de l'école sur le serveur";
                }
            }
            $school->setIsActive(true);
            $school->setAddedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($school);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('school/new.html.twig', [
            'school' => $school,
            'form' => $form,
            'message' => $message
        ]);
    }

    #[Route('/{id}/edit', name: 'app_school_edit', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function edit(Request $request, School $school): Response
    {
        $message = null;
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();

            if ($logo)
                $fichier = $school->getName() . '.' . $logo->guessExtension(); {
                try {
                    $logo->move($this->getParameter('upload_directory'), $fichier);
                    $school->setLogo($fichier);
                } catch (\Throwable $th) {
                    $message = "Impossible de télécharger le logo de l'école sur le serveur";
                }
            }
            $school->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('school/edit.html.twig', [
            'school' => $school,
            'form' => $form,
            'message' => $message
        ]);
    }

    #[Route('/{id}', name: 'app_school_delete', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function delete(Request $request, School $school): Response
    {
        if ($this->isCsrfTokenValid('delete' . $school->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/activate/{id}', name: 'app_school_activate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function activate(Request $request, School $school): Response
    {
        if ($this->isCsrfTokenValid('activate' . $school->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $school->setIsActive(true);
            $school->setUpdatedAt(new \DateTime());
            $entityManager->persist($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desactivate/{id}', name: 'app_school_desactivate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function desactivate(Request $request, School $school): Response
    {
        if ($this->isCsrfTokenValid('desactivate' . $school->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $school->setIsActive(false);
            $school->setUpdatedAt(new \DateTime());
            $entityManager->persist($school);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_index', [], Response::HTTP_SEE_OTHER);
    }
}