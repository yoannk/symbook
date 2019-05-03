<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/books/search", name="search_book", methods={"POST"})
     */
    public function searchBooks(Request $request, BookRepository $bookRepository)
    {
        $search = $request->request->get('search');

        if (empty($search)) {
            return $this->redirectToRoute('book_index');
        }

        $books = $bookRepository->search($search);

        return $this->render('search/book.html.twig', [
            'books' => $books,
        ]);
    }
}
