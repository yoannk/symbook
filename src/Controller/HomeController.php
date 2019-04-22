<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     */
    public function index()
    {
        $books = [
            "Le Seigneur des anneaux - Intégrale",
            "L'Étranger",
            "Voyage au bout de la nuit",
            "Les Fleurs du mal",
            "Le Petit Prince",
        ];

        return $this->render('home/index.html.twig', [
            'books' => $books
        ]);
    }
}