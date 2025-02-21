<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Bestiary;
use App\Entity\Creature;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var Generator
     */
    private Generator $faker;

    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->userPasswordHasher = $userPasswordHasher;
    }


    public function load(ObjectManager $manager): void
    {

        // Public
        $publicUser = new User();
        $publicUser->setUsername("public");
        $publicUser->setRoles(["ROLE_PUBLIC"]);
        $publicUser->setPassword($this->userPasswordHasher->hashPassword($publicUser, plainPassword: "public"));
        $manager->persist($publicUser);


        // Authentifiés
        for ($i = 0; $i < 5; $i++) {
            $userUser = new User();
            $password = $this->faker->password(2, 6);
            $userUser->setUsername($this->faker->userName() . "@" . $password);
            $userUser->setRoles(["ROLE_USER"]);
            $userUser->setPassword($this->userPasswordHasher->hashPassword($userUser, $password));
            $manager->persist($userUser);
        }


        // Admins
        $adminUser = new User();
        $adminUser->setUsername("admin");
        $adminUser->setRoles(["ROLE_ADMIN"]);
        $adminUser->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"));
        $manager->persist($adminUser);

        $totalBestiaries = 50;
        $totalCreatures = 19;
        $bestiaries = [];
        for ($i = 0; $i < $totalBestiaries; $i++) {
            $bestiary = new Bestiary();
            $min = $this->faker->numberBetween(1, 254);
            $max = $this->faker->numberBetween($min, 255);
            $bestiary
                ->setName($this->faker->word())
                ->setMaxLifePoint($max)
                ->setMinLifePoint($min)
                ->setCreatedBy($adminUser);

            $manager->persist($bestiary);
            $bestiaries[] = $bestiary;
        }
        $manager->flush();

        // $creatures = [];
        for ($i = 0; $i < $totalCreatures; $i++) {
            $myBestiary = $bestiaries[array_rand($bestiaries, 1)];
            $creature = new Creature();
            $maxLp = $this->faker->numberBetween($myBestiary->getMinLifePoint(), $myBestiary->getMaxLifePoint());
            $lp = $this->faker->numberBetween(($maxLp / 2), $maxLp);
            $creature
                ->setName($myBestiary->getName())
                ->setMaxLifePoint($maxLp)
                ->setLifePoint($lp)
                ->setBestiary($myBestiary)
                ->setCreatedBy($adminUser);
            $manager->persist($creature);
        }

        $manager->flush();
    }
}
