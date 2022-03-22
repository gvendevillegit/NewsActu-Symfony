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
            $commentary->setAuthor($this->getUser);

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
    
}// End class CommentaryController
