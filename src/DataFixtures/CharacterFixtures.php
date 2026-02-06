<?php

namespace App\DataFixtures;

use App\Entity\Character;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class CharacterFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = require __DIR__.'/RickAndMorty.php';

        $faker = Factory::create('en_US');
        $faker->seed(1234);

        $this->createCharacter($manager, 'Rick Sanchez', 'Alive', 'Human', 'Male', 'Earth (C-137)');
        $this->createCharacter($manager, 'Morty Smith', 'Alive', 'Human', 'Male', 'unknown');
        $this->createCharacter($manager, 'Summer Smith', 'Alive', 'Human', 'Female', 'Earth (Replacement Dimension)');

        for ($i = 0; $i < 60; $i++) {
            $character = new Character();

            $name = $faker->boolean(70)
                ? $faker->randomElement($data['firstNames']).' '.$faker->randomElement($data['lastNames'])
                : $faker->randomElement($data['nicknames']);

            $character->setName($name);
            $character->setStatus($faker->randomElement($data['statuses']));
            $character->setSpecies($faker->randomElement($data['species']));
            $character->setGender($faker->randomElement($data['genders']));
            $character->setOrigin($faker->randomElement($data['origins']));

            if (method_exists($character, 'setCreatedAt')) {
                $character->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'));
            }

            $manager->persist($character);
        }

        $manager->flush();
    }

    private function createCharacter(
        ObjectManager $manager,
        string $name,
        ?string $status,
        ?string $species,
        ?string $gender,
        ?string $origin,
    ): void {
        $character = new Character();
        $character->setName($name);
        $character->setStatus($status);
        $character->setSpecies($species);
        $character->setGender($gender);
        $character->setOrigin($origin);

        if (method_exists($character, 'setCreatedAt')) {
            $character->setCreatedAt(new \DateTimeImmutable());
        }

        $manager->persist($character);
    }
}
