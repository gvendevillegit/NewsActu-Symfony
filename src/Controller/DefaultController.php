<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: "default_home", methods: ['GET'])]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->findBy([
            'deletedAt' => null,
        ]);
        return $this->render('default/home.html.twig', [
            'articles' => $article,
        ]);
    } // END function home
}// End class DefaultController
