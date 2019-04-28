<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index")
     */
    public function index(BookRepository $bookRepository)
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET", "POST"})
     */
    public function new(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $book->getImage()->getFile();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('uploads_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $book->getImage()->setName($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté avec succès !');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<[1-9]\d*>}", name="book_show", methods={"GET"})
     */
    public function show(Book $book)
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET", "POST"})
     */
    public function edit(Book $book)
    {
    }

    /**
     * @Route("/{id}/delete", name="book_delete", methods={"GET"})
     */
    public function delete(Book $book, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->query->get('token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a bien été supprimé');
        }

        return $this->redirectToRoute('book_index');
    }
}
