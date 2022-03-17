<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    // Pour PHP < 8
    // @Route("/voir/{cat_alias}/{article_alias}_{id}", name="show_article", methods={"GET"})
    #[Route('/voir/{cat_alias}/{article_alias}_{id}.html', name: "show_article", methods: ['GET'])]
    public function showArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        return $this->render('article/show_article.html.twig', [
            'article' => $article,
        ]);
    } // END Function showArticle

} // END Class ArticleController