<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # Mise en place de nos fixtures
        # https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html#writing-fixtures
        # php bin/console doctrine:fixtures:load

        # Création d'un utilisateur
        $user = new User();
        $user->setEmail('demo@evently.fr')
            ->setPassword('demo')
            ->setRoles(['ROLE_ADMIN']);

        # Demande de sauvegarde de mon user.
        $manager->persist($user);

        # Initialisation de Faker
        # https://github.com/FakerPHP/Faker
        $faker = Factory::create('fr');

        # Création de 20 évènements
        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $event->setTitle($faker->text())
                ->setAddress($faker->address())
                ->setDescription($faker->text())
                ->setImage($faker->imageUrl(300, 380))
                ->setEventDate($faker->dateTimeThisYear())
                ->setUser($user);

            $manager->persist($event);
        }

        $manager->flush();
    }
}
