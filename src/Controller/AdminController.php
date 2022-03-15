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

class AdminController extends AbstractController
{
    #[Route('/admin/tableau-de-bord', name: 'show_dashboard', methods:['GET'])]
    public function showDashboard(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();
        return $this->render('admin/show_dashboard.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/admin/creer-un-article', name: 'create_article', methods:['GET|POST'])]
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

        return $this->render('admin/form/create_article.html.twig', ['form' => $form->createView()]);
    }
}
