<?php

namespace App\Controller;

use App\Entity\Commentary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    /**
    * @Route("/profile", name="show_profile", methods={"GET"})
    */
    public function showProfile(): Response
    {
        return $this->render('profile/show_profile.html.twig');
    } // END function showProfile

    /**
    * @Route("/profile/tous-mes-commentaires", name="show_user_commentaries", methods={"GET"})
    */
    public function showUserCommentaries(EntityManagerInterface $entityManager): Response
    {
        $commentaries = $entityManager->getRepository(Commentary::class)->findBy(['author' => $this->getUser()]);

        // Statitiques depuis le Controller (voir la vue show_user_commentaries.html.twig)
        $total = count($commentaries);
        $totalInline = 0;
        $totalOutline = 0;

        $totalInline = count($entityManager->getRepository(Commentary::class)->findBy(['deletedAt' => null, 'author' =>$this->getUser()]));
        $totalOutline = $total - $totalInline;

        //dd($total);

        return $this->render('profile/show_user_commentaries.html.twig', [
            'commentaries' => $commentaries,
            'total' => $total,
            'totalInline' => $totalInline,
            'totalOutline' => $totalOutline
        ]);
    }
}