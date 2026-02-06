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
        $faker = Factory::create('en_US');
        $faker->seed(1234);

        $this->createCharacter($manager, 'Rick Sanchez', 'Alive', 'Human', 'Male');
        $this->createCharacter($manager, 'Morty Smith', 'Alive', 'Human', 'Male');

        $statuses = ['Alive', 'Dead', 'unknown'];
        $species = ['Human', 'Alien', 'Robot', 'Mythological Creature', 'Cronenberg'];
        $genders = ['Female', 'Male', 'Genderless', 'unknown'];

        for ($i = 0; $i < 60; $i++) {
            $character = new Character();
            $character->setName($faker->name());
            $character->setStatus($faker->randomElement($statuses));
            $character->setSpecies($faker->randomElement($species));
            $character->setGender($faker->randomElement($genders));
            $character->setImage($faker->imageUrl(300, 300, 'people', true));

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
        ?string $gender
    ): void {
        $character = new Character();
        $character->setName($name);
        $character->setStatus($status);
        $character->setSpecies($species);
        $character->setGender($gender);
        $character->setImage(null);

        if (method_exists($character, 'setCreatedAt')) {
            $character->setCreatedAt(new \DateTimeImmutable());
        }

        $manager->persist($character);
    }
}

