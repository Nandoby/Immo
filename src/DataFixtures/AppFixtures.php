<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\Location;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $buildingConditions = [
            "Bon",
            "A restaurer",
            "A rafraichir",
            "Excellent état",
            "Fraîchement rénové",
            "A rénover"
        ];

        $types = [
            "Maison",
            "Appartement",
            "Maison et appartement",
            "Projet neuf - Maisons",
            "Projet neuf - Appartements",
            "Garage",
            "Bureau",
            "Commerce",
            "Industrie",
            "Terrain"
        ];
        $typesObjects = [];

        $locations = [];

        $faker = Factory::create("fr_FR");

        $admin = new User();

        for ($l = 0; $l < 10; $l ++) {
            $location = new Location();
            $location->setName($faker->city())
                ->setZipCode($faker->postcode());

            $manager->persist($location);

            $locations[] = $location;
        }

        foreach($types as $t) {
            $type = new Type();
            $type->setName($t);
            $manager->persist($type);

            $typesObjects[] = $type;
        }

        $admin->setEmail('nandobiaccalli@gmail.com')
            ->setFirstname('Nando')
            ->setLastname('Biaccalli')
            ->setPassword($this->hasher->hashPassword($admin, 'password'));

        $manager->persist($admin);

        for ($u = 0; $u < 10; $u++) {
            $user = new User();
            $user->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);

            for ($a = 0; $a < rand(1, 4); $a++) {
                $ad = new Ad();
                $ad->setPrice($faker->randomFloat(
                    nbMaxDecimals: 2,
                    min: 50000,
                    max: 400000
                ))->setDescription($faker->sentences(rand(1, 3), true))
                    ->setLivingSpace(rand(30, 300))
                    ->setRooms(rand(2, 6))
                    ->setYearOfConstruction($faker->year())
                    ->setbuildingCondition($faker->randomElement($buildingConditions))
                    ->setUser($user)
                    ->setType($faker->randomElement($typesObjects))
                    ->setLocation($faker->randomElement($locations))
                ;

                $manager->persist($ad);

                for ($i = 0; $i < rand(1, 6); $i++) {
                    $image = new Image();
                    $image->setAd($ad)
                        ->setPath("https://picsum.photos/id/".rand(1,100)."/800/800");

                    $manager->persist($image);
                }
            }
        }
        $manager->flush();
    }
}
