<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/genre')]
class GenreController extends AbstractController
{
    #[Route('/', name: 'genre_index', methods: ['GET'])]
    public function index(GenreRepository $genreRepository): Response
    {
        return $this->render('genre/index.html.twig', [
            'genres' => $genreRepository->findAll(),
        ]);
    }
    #[Route('/rechercher', name: 'rechercher', methods: ['GET'])]
    public function rechercherNom(GenreRepository $genreRepository, Request $req): Response
    {
        $b = $req->query->get('se');
        $r =  "%" . $b . "%";
        $query = $genreRepository->createQueryBuilder('j')
            ->Where('j.nom LIKE :r')
            ->setParameter('r', $r);
        $a = $query->getQuery()->getResult();
        return $this->render('genre/index.html.twig', [
            'genres' => $a,
        ]);
    }
    #[Route('/new', name: 'genre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('genre_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('genre/new.html.twig', [
            'genre' => $genre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'genre_show', methods: ['GET'])]
    public function show(Genre $genre): Response
    {
        return $this->render('genre/show.html.twig', [
            'genre' => $genre,
        ]);
    }

    #[Route('/{id}/edit', name: 'genre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Genre $genre, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();
            return $this->renderForm('genre/edit.html.twig', [
                'genre' => $genre,
                'form' => $form,
            ]);
        }
        return $this->renderForm('genre/edit.html.twig', [
            'genre' => $genre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'genre_delete', methods: ['POST'])]
    public function delete(Request $request, Genre $genre, EntityManagerInterface $entityManager, LivreRepository $LivreRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $query = $LivreRepository->createQueryBuilder('s')
            ->from('App:Livre', 'j')
            ->join('s.genre', 'u')
            ->andWhere('u.id = :autId')
            ->setParameter('autId', $genre->getId());
        $genres = $query->getQuery()->getResult();
        if (sizeof($genres) == 0) {
            if ($this->isCsrfTokenValid('delete' . $genre->getId(), $request->request->get('_token'))) {
                $entityManager->remove($genre);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('genre_index', [], Response::HTTP_SEE_OTHER);
    }
}
