<?php

namespace App\Controller;

use App\Entity\University;
use App\Form\UniversityType;
use App\Repository\UniversityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/university')]
class UniversityController extends AbstractController
{
    #[Route('/', name: 'app_university_home', methods: ['GET'])]
    public function home(UniversityRepository $universityRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('university/home.html.twig', [
            'universities' => $paginator->paginate($universityRepository->getAllWithQueryBuilder(), $request->query->getInt('page', 1), 10)
        ]);
    }

    #[Route('/list', name: 'app_university_index', methods: ['GET']), isGranted('ROLE_ADMIN')]
    public function index(UniversityRepository $universityRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('university/index.html.twig', [
            'universities' => $paginator->paginate($universityRepository->findAll(), $request->query->getInt('page', 1), 10)

        ]);
    }

    #[Route('/new', name: 'app_university_new', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function new(Request $request): Response
    {
        $message = null;
        $university = new University();
        $form = $this->createForm(UniversityType::class, $university);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();

            if ($logo)
                $fichier = $university->getName() . '.' . $logo->guessExtension(); {
                try {
                    $logo->move($this->getParameter('upload_directory'), $fichier);
                    $university->setLogo($fichier);
                } catch (\Throwable $th) {
                    $message = "Impossible de télécharger le logo de l'école sur le serveur";
                }
            }
            $university->setIsActive(true);
            $university->setAddedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($university);
            $entityManager->flush();

            return $this->redirectToRoute('app_university_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('university/new.html.twig', [
            'university' => $university,
            'form' => $form,
            'message' => $message
        ]);
    }

    #[Route('/{id}/edit', name: 'app_university_edit', methods: ['GET', 'POST']), isGranted('ROLE_ADMIN')]
    public function edit(Request $request, University $university): Response
    {
        $message = null;
        $form = $this->createForm(UniversityType::class, $university);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form->get('logo')->getData();

            if ($logo)
                $fichier = $university->getName() . '.' . $logo->guessExtension(); {
                try {
                    $logo->move($this->getParameter('upload_directory'), $fichier);
                    $university->setLogo($fichier);
                } catch (\Throwable $th) {
                    $message = "Impossible de télécharger le logo de l'école sur le serveur";
                }
            }
            $university->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_university_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('university/edit.html.twig', [
            'university' => $university,
            'form' => $form,
            'message' => $message
        ]);
    }

    #[Route('/{id}', name: 'app_university_delete', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function delete(Request $request, University $university): Response
    {
        if ($this->isCsrfTokenValid('delete' . $university->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($university);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_university_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/activate/{id}', name: 'app_university_activate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function activate(Request $request, University $university): Response
    {
        if ($this->isCsrfTokenValid('activate' . $university->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $university->setIsActive(true);
            $university->setUpdatedAt(new \DateTime());
            $entityManager->persist($university);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_university_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desactivate/{id}', name: 'app_university_desactivate', methods: ['POST']), isGranted('ROLE_ADMIN')]
    public function desactivate(Request $request, University $university): Response
    {
        if ($this->isCsrfTokenValid('desactivate' . $university->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $university->setIsActive(false);
            $university->setUpdatedAt(new \DateTime());
            $entityManager->persist($university);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_university_index', [], Response::HTTP_SEE_OTHER);
    }
}