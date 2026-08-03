<?php
namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Edition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Création d'une Édition de test
        $edition = new Edition();
        $edition->setName('Édition 2026');
        $edition->setStartDate(new \DateTime('2026-06-01'));
        $edition->setEndDate(new \DateTime('2026-06-03'));
        $edition->setIsCurrent(true);
        $manager->persist($edition);

        // 2. Création d'un Groupe de test
        $group = new Group();
        $group->setName('Samba Rennes');
        $group->setEmail('contact@sambarennes.fr');
        $group->setCity('Rennes');
        
        // Hachage du mot de passe "password123"
        $password = $this->hasher->hashPassword($group, 'password123');
        $group->setPassword($password);

        $manager->persist($group);

        $manager->flush();
    }
}