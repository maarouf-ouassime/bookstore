<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Genre;
use App\Entity\Auteur;
use App\Entity\Livre;
use App\Entity\User;

class AppFixtures extends Fixture
{
    /** @var Generator */
    public $faker;
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create('fr_FR');
        $Aut = $this->loadAuteur($manager);
        $Gen = $this->loadGenre($manager);
        $this->loadLivre($manager, $Aut, $Gen);
        $this->loadUser($manager);
    }
    public function loadGenre(ObjectManager $manager)
    {
        $i = 0;
        $Gen = array();
        $ar = array("Roman", "Biographie", "Fantasy", "Conte", "Témoignage", "Sport", "Science-fiction", "Horreur", "Fantastique", "Cuisine");
        while ($i < 10) {
            $v = $ar[$i];
            $product = new Genre();
            $product->setNom($v);
            array_push($Gen, $product);
            $manager->persist($product);
            $i++;
        }
        $manager->flush();
        return $Gen;
    }
    public function loadAuteur(ObjectManager $manager): array
    {
        $Aut = array();
        $i = 0;
        while ($i < 20) {
            $product = new Auteur();
            $product->setNomPrenom($this->faker->name);
            if ($i % 2 == 0) {
                $product->setSexe("M");
            } else {
                $product->setSexe("F");
            }
            $product->setNationalite($this->faker->country());

            $product->setDateDeNaissance($this->faker->dateTimeBetween('-80 year', '-18 year'));
            array_push($Aut, $product);
            $manager->persist($product);
            $i++;
        }
        $manager->flush();
        return $Aut;
    }
    public function loadLivre(ObjectManager $manager, array $Aut, array $Gen)
    {
        $i = 0;
        while ($i < 50) {
            $product = new Livre();
            $product->setIsbn($this->faker->isbn13());
            $product->setNombrePages($this->faker->randomNumber(3, false));
            $product->setNote($this->faker->numberBetween(0, 20));
            $product->setTitre($this->faker->sentence(3));
            $product->setDateDeParution($this->faker->dateTimeBetween('-121 year', '0 year'));
            $j = 0;
            foreach ($Aut as $a) {
                if ($j < 3 - ($i % 2)) {

                    $product->addAuteur($a);
                    shuffle($Aut);
                } else {
                    break;
                }
                $j++;
            }
            $j = 0;
            foreach ($Gen as $a) {
                if ($j < 3 - ($i % 2)) {
                    $product->addGenre($a);
                    shuffle($Gen);
                } else {
                    break;
                }
                $j++;
            }
            shuffle($Aut);
            shuffle($Gen);
            $manager->persist($product);
            $i++;
        }
        $manager->flush();
    }
    public function loadUser(ObjectManager $manager)
    {
        $product = new User();
        $product->setEmail($this->faker->email);
        $product->setPassword($this->faker->password);
        $arr = array("ROLE_ADMIN");
        $product->setRoles($arr);
        $manager->persist($product);
        $manager->flush();
    }
    function __construct()
    {
    }
}
