<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auteur')]
class AuteurController extends AbstractController
{
    #[Route('/', name: 'auteur_index', methods: ['GET'])]
    public function index(AuteurRepository $auteurRepository): Response
    {
        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurRepository->findAll(),
        ]);
    }
    #[Route('/rechercher', name: 'rechercher', methods: ['GET'])]
    public function rechercherNom(AuteurRepository $auteurRepository, Request $req): Response
    {
        $b = $req->query->get('se');
        $r =  "%" . $b . "%";

        $query = $auteurRepository->createQueryBuilder('j')
            ->Where('j.nom_prenom LIKE :r')
            ->setParameter('r', $r);
        $a = $query->getQuery()->getResult();


        return $this->render('auteur/index.html.twig', [
            'auteurs' => $a,
        ]);
    }

    #[Route('/new', name: 'auteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $auteur = new Auteur();
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($auteur);
            $entityManager->flush();

            return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auteur/new.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'auteur_show', methods: ['GET'])]
    public function show(Auteur $auteur, LivreRepository $livreRepository): Response
    {
        $query = $livreRepository->createQueryBuilder('s')
            ->from('App:Livre', 'j')
            ->join('s.auteurs', 'u')
            ->andWhere('u.id = :autId')
            ->setParameter('autId', $auteur->getId());
        $livres = $query->getQuery()->getResult();

        return $this->render('auteur/show.html.twig', [
            'auteur' => $auteur,
            'livres' => $livres,
        ]);
    }

    #[Route('/{id}/edit', name: 'auteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auteur/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'auteur_delete', methods: ['POST'])]
    public function delete(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete' . $auteur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($auteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
