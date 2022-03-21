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

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/tableau-de-bord', name: 'show_dashboard', methods:['GET|POST'])]
    public function showDashboard(EntityManagerInterface $entityManager): Response
    {
        // Récupération des articles non archivés (deletedAt == null)
        //$articles = $entityManager->getRepository(Article::class)->findBy(['deletedAt' => null]);

        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('admin/show_dashboard.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/creer-un-article', name: 'create_article', methods:['GET|POST|POST'])]
    public function createArticle(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleFormType::class, $article)->handleRequest($request);
        
        // Traitement du formulaire
        if($form->isSubmitted() && $form->isValid()){
            // Pour accéder à une valeur d'un input de $form, on fait :
                // $form->get('title')->getData()

            // Setting des propriétés non mappées dans le formulaire
            $article->setAlias($slugger->slug($article->getTitle()));
            $article->setCreatedAt(new DateTime());
            $article->setUpdateAt(new DateTime());

            // Variabilisation du fichier 'photo' uploadé.
            $file = $form->get('photo')->getData();

            // if($file === true)
            // Si un fichier est uploadé (depuis le formulaire )
            if($file){
                // Maintenant il s'agit de reconstruire le nom du fichier pour le sécuriser.

                // 1ère Etape : on déconstruit le nom du ficheir et on variabilise
                $extension = '.'.$file->guessExtension();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);                

                // Assainissement du nom de fichier (du filename)
                //$safeFilename = $slugger->slug($originalFilename);
                $safeFilename = $article->getAlias();

                // 2éme Etape : on reconstruit le nom du fichier maintenant qu'il est safe (sécurisé).
                // uniqid() est une fonction native de PHP, elle permet d'ajouter une valeur numérique (id) unique et auto-générée
                // Possibilité d'entropier pour diminuer la probabilité d'avoir des doublons : uniqid("", true)
                $newFilename = $safeFilename. '_' .uniqid().$extension;

                // try/catch fait partie de PHP nativement
                try{
                    // On a configuré un paramètre 'uploads_dir' dans le fichier config/services.yaml
                        // Ce param contient le chemin absolu de notre dossier d'upload de photo
                    $file->move($this->getParameter('uploads_dir'), $newFilename);

                    // On set le Nom de la photo, pas le CHEMIN
                    $article->setPhoto($newFilename);

                }catch(FileException $exception){

                } // END catch()
            } // END if($file)

            $entityManager->persist($article);
            $entityManager->flush();

            // Ici on ajoute un message qu'on affichera en twig
            $this->addFlash('success', 'Bravo, votre article est bien en ligne !');
            return $this->redirectToRoute('show_dashboard');
        } // END if($form)

        return $this->render('admin/form/form_article.html.twig', ['form' => $form->createView()]);
    } // END function createArticle

    // L'action est exéxutée 2x et accessible par les deux méthods (GET|POST)
    #[Route('/modifier-un-article/{id}', name: 'update_article', methods:['GET|POST'])]
    public function updateArticle(Article $article, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Condition ternaire : $article->getPhoto() ?? ''
        // => est égal à : isset($article->getPhoto()) ? $article->getPhoto() : '';
        $originalPhoto = $article->getPhoto() ?? '';

        // 1er TOUR en méthode GET
        $form = $this->createForm(ArticleFormType::class, $article, [
            'photo' => $originalPhoto
        ])->handleRequest($request);
        
        // 2ème TOUR de l'action en méthode POST
        if($form->isSubmitted() && $form->isValid()){
            $article->setAlias($slugger->slug($article->getTitle()));
            $article->setUpdateAt(new DateTime());

            $file = $form->get('photo')->getData();

            if($file){
       
                $extension = '.'.$file->guessExtension();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);                
                $safeFilename = $article->getAlias();
                $newFilename = $safeFilename. '_' .uniqid().$extension;

                try{

                    $file->move($this->getParameter('uploads_dir'), $newFilename);
                    $article->setPhoto($newFilename);

                }catch(FileException $exception){
                    // code à executer si une erreur est attrapée

                } // END catch()

            }else{
                $article->setPhoto($originalPhoto);
            } // END if($file)

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "L'article ".$article->getTitle()." a bien été modifié !");

            return $this->redirectToRoute("show_dashboard");

        } // END if($form)

        // On retourne la vue pour la méthode GET
        return $this->render('admin/form/form_article.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    } // END function updateArticle

    #[Route('/archiver-un-article/{id}', name: 'soft_delete_article', methods:['GET'])]
    public function softDeleteArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        // set la propriété deleteAt pour archiver l'article. De l'autre côté on affichera les article où deletedAt === null
        $article->setDeletedAt(new DateTime());

        $entityManager->persist($article);
        $entityManager->flush();

        $this->addFlash('success', "L'article ".$article->getTitle(). " a bien été archivé !");

        return $this->redirectToRoute('show_dashboard');
    }// END function softDeleteArticle

    #[Route('/supprimer-un-article/{id}', name: 'hard_delete_article', methods:['GET'])]
    public function hardDeleteArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        // Cette méthode supprime une ligne en BDD
        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('success', "L'article ".$article->getTitle(). " a bien été supprimé de la base de données !");

        return $this->redirectToRoute('show_dashboard');
    }// END function hardDeleteArticle

    #[Route('/restaurer-un-article/{id}', name: 'restore_article', methods:['GET'])]
    public function restoreArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        $article->setDeletedAt();
        $entityManager->persist($article);
        $entityManager->flush();
        $this->addFlash('success', "L'article ".$article->getTitle(). " a bien été restauré des archives !");

        return $this->redirectToRoute('show_dashboard');
    }// END function restoreArticle

} // END Class
