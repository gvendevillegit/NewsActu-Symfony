<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commentary;
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
    // Pour PHP 8
    // #[Route('/voir/{cat_alias}/{article_alias}_{id}.html', name: "show_article", methods: ['GET'])]
    /** 
     * @Route("/voir/{cat_alias}/{article_alias}_{id}.html", name="show_article", methods={"GET"})
     */
    public function showArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        $commentaries = $entityManager->getRepository(Commentary::class)->findBy([
            'article' => $article->getId(),
        ]);
        return $this->render('article/show_article.html.twig', [
            'article' => $article,
            'commentaries' => $commentaries
        ]);
    } // END Function showArticle

    // Pour PHP < 8
    // #[Route('/voir/{alias}.html', name: "show_articles_from_category", methods: ['GET'])]
    /**
     * @Route("/voir/{alias}.html", name="show_articles_from_category", methods={"GET"})
     */
    public function showArticlesFromCategorie(Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findBy([
            'category' => $categorie->getId(),
            'deletedAt' => null
        ]);

        return $this->render('article/show_articles_from_category.html.twig', [
            'articles' => $articles,
            'categorie' => $categorie
        ]);
    } // END Function showArticlesFromCategorie

} // END Class ArticleController