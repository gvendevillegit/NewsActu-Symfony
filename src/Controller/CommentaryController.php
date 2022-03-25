<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\CommentaryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaryController extends AbstractController
{
    // En PHP < 8  
    //#[Route('/ajouter-un-commentaire?article_id={id}', name: "add_commentary", methods: ['GET|POST'])]
    /**
     * @Route("/ajouter-un-commentaire?article_id={id}", name="add_commentary", methods={"GET|POST"})]
     */
    public function addCommentary(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentary = new Commentary;
        // Création d'un formulaire basé sur notre prototype de formulaire 
        $form = $this->createForm(CommentaryFormType::class, $commentary)
            ->handleRequest($request);

        # Cas où le formulaire n'est pas valide. Lorsque le champ 'comment' est vide, il y la contrainte NotBlank qui se déclenche.
        if($form->isSubmitted() && $form->isValid() === false)
        {
            $this->addFlash('warning', 'votre commentaire est vide !');

            return $this->redirectToRoute('show_article', [
                'cat_alias' => $article->getCategory()->getAlias(),
                'article_alias' => $article->getAlias(),
                'id' => $article->getId(),
            ]);
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $commentary->setArticle($article);
            $commentary->setCreatedAt(new DateTime);
            $commentary->setUpdatedAt(new DateTime);

            // On set l'auteur du commentaire

            $commentary->setAuthor($this->getUser());

            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez commenté l'article <strong>".$article->getTitle()."</strong> avec succès !");

            return $this->redirectToRoute('show_article', [
                'cat_alias' => $article->getCategory()->getAlias(),
                'article_alias' => $article->getAlias(),
                'id' => $article->getId(),
            ]);
        }

        return $this->render('rendered/form_commentary.html.twig', [
            'form' => $form->createView()
        ]);
    } // End function addCommentary

     /**
     * 1ère FACON ==>
     *              Inconvénient :  C'est très verbeux.
     *                              Les paramètres de la route pour faire un redirectToRoute() peuvent ne pas être accessibles.
     *              Avantage :      La redirection sera STATIQUE, tous les utilsiateurs seront redirigés au même endroit.
     * 
     * 2ème FACON ==>
     *              Inconvénient :  La redirection se fera en fonction de l'utl de provenance de la requête, à savoir si vous utilisez cette action à plusieur endrooits différents de votre site, l'uitlisateur sera redirigé ailleurs que ce que vous avez décidé.
     *              Avantage :      LA redirection devient DYNAMIQUE (Elle changera en focntoon de la provenance de la requête).
     * *                            
     * @Route("/archiver-mon-commentaire_{id}", name="soft_delete_commentary", methods={"GET"})]
     */
    public function softDeletedCommentary(Commentary $commentary, Request $request, EntityManagerInterface $entityManager): Response
    {
        /* PARCE QUE nous allons rediriger vers 'show_article' qui attend 3 arguments, nous avons injecté l'objet Request ↑↑↑
        * Cela nous permettra d'accéder aux superglobales PHP ($_GET & $_SERVER => appelés dans l'ordre : query & server)
        * Nous allons voir 2 façons pour rediriger sur la route souhaitée. 
        */

        $commentary->setDeletedAt(new DateTime());

        #=================== 1ère FACON ===================#
        //  dd($request->query->get('article_alias'));
        #==================================================#

        #=================== 2ème FACON ===================#
        //  dd($request->server->get('HTTP_REFERER'));
        #==================================================#

        $entityManager->persist($commentary);
        $entityManager->flush();

        $this->addFlash('success', "Votre commentaire est archivé");

         #=================== 1ère FACON ===================#
         # Nous récupérons les valeurs des paramètres passés dans dans l'URL $_GET (query)
         # Cette étape à lieu dans le fichier 'show_article.html.twig' sur l'attibut HTML 'href' de la balise <a>
         # ===> VOIR 'show_article.html.twig pour la suite de la 1ère FACON

        //        return $this->redirectToRoute('show_article', [
        //            'cat_alias' => $request->query->get('cat_alias'),
        //            'article_alias' => $request->query->get('article_alias'),
        //            'id' => $request->query->get('article_id')
        //        ]);
        #==================================================#

        #=================== 2ème FACON ===================#
        # Pour cette façon, nous avons retirés les paramètres de l'URL dans le fichier 'show_article.html.twig' 
        # ===> VOIR 'show_article.html.twig pour la suite de la 2ème FACON 
        # Ici nous utilisons une clé de $_SERVER (server) qui s'appelle 'HTTP_REFERER'
        # Cette clé contient l'URL de provenance de la requête ($request) 
        return $this->redirect($request->server->get('HTTP_REFERER')."#link-commentary");

        return $this->redirectToRoute('show_article', [

        ]);
    } // END function softDeletedCommentary

    /**
     * @Route("/restaurer-un-commentaire/{id}", name="restore_commentary", methods={"GET"})
     */
    public function restoreCommentary(Commentary $commentary, EntityManagerInterface $entityManager): Response
    {
        $commentary->setDeletedAt(NULL);
        $entityManager->persist($commentary);
        $entityManager->flush();
        $this->addFlash('success', "Le commentaire ".$commentary->getComment(). " a bien été restaurée des archives !");

        return $this->redirectToRoute('show_user_commentaries');
    }// END function restoreCommentary
    
}// End class CommentaryController
