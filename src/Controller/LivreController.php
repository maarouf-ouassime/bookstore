<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Form\LivFormNote;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livre')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }
    #[Route('/rechercher', name: 'rechercher', methods: ['GET'])]
    public function rechercherNom(LivreRepository $livreRepository, Request $req): Response
    {
        $b = $req->query->get('se');
        $r =  "%" . $b . "%";
        $query = $livreRepository->createQueryBuilder('j')
            ->Where('j.titre LIKE :r')
            ->setParameter('r', $r);
        $a = $query->getQuery()->getResult();
        return $this->render('livre/index.html.twig', [
            'livres' => $a,
        ]);
    }
    #[Route('/showdateRes', name: 'showdateRes', methods: ['GET'])]
    public function showdateRes(LivreRepository $livreRepository, Request $req): Response
    {
        $r = $req->query->get('date1');
        $r2 = $req->query->get('date2');

        $query = $livreRepository->createQueryBuilder('j')
            ->Where('j.date_de_parution < :date2')
            ->andWhere('j.date_de_parution > :date1')
            ->setParameter('date1', $r)
            ->setParameter('date2', $r2);
        $a = $query->getQuery()->getResult();


        return $this->render('livre/index2.html.twig', [
            'livres' => $a,
        ]);
    }
    #[Route('/search_date', name: 'search_date', methods: ['GET'])]
    public function showdate(): Response
    {

        return $this->renderForm('livre/showdate.html.twig');
    }
    #[Route('/new', name: 'livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'livre_show', methods: ['GET'])]
    public function show(Livre $livre, AuteurRepository $auteurRepository, GenreRepository $genreRepository): Response
    {
        $query = $auteurRepository->createQueryBuilder('s')
            ->from('App:Auteur', 'j')
            ->join('s.livre', 'u')
            ->andWhere('u.id = :autId')
            ->setParameter('autId', $livre->getId());
        $auteurs = $query->getQuery()->getResult();

        $query = $genreRepository->createQueryBuilder('s')
            ->from('App:Genre', 'j')
            ->join('s.livres', 'u')
            ->andWhere('u.id = :autId')
            ->setParameter('autId', $livre->getId());
        $genres = $query->getQuery()->getResult();

        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
            'auteurs' => $auteurs,
            'genres' => $genres,
        ]);
    }

    #[Route('/{id}/edit', name: 'livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/note_edit', name: 'note_edit', methods: ['GET', 'POST'])]
    public function editNote(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(LivFormNote::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/editnote.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
