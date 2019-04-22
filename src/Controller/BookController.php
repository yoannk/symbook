<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/book/{name}", name="book_show")
     */
    public function show(string $name)
    {
        $book = $name;
        return $this->render('book/show.html.twig', [
            'book' => $book
        ]);
    }
}
