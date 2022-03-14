<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class DataFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    // Cette fonction load() sera exécutée en ligne de commande, avec : php bin/console doctrine:fixtures:load --append
    // => le drapeau --append permet de ne pas purger la BDD.
    public function load(ObjectManager $manager): void
    {
        // Déclaration d'une variable de type array, avec le nom des différentes catégories de NewsActu.
        $categories = [
            'Politique',
            'Société',
            'People',
            'Économie',
            'Santé',
            'Sport',
            'Espace',
            'Sciences',
            'Mode',
            'Informatique',
            'Écologie',
            'Cinéma',
            'Hi Tech',
        ];

        // La boucle foreach est optimisée pour les array.
        // La syntaxe complète à l'intérieur des parenthèses est : ($key => $value)
        foreach($categories as $cat){

            // Instanciation d'un objet Categorie()
            $categorie = new Categorie();
            // Appel des setters de notre Objet $categorie
            $categorie->setName($cat);
            $categorie->setAlias($this->slugger->slug($cat));
            $categorie->setCreatedAt(new DateTime());
            $categorie->setUpdatedAt(new DateTime());

            // EntityManager, on appel sa méthode persist() pour onsérer en BDD l'Objet $categorie
            $manager->persist($categorie);
        }

        // On vide l'EntityManager pour la suite.
        $manager->flush();
    }
}