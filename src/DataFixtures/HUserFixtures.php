<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $users = [
            // =========================
            // ADMIN
            // =========================
            [
                'email' => 'admin@test.com',
                'firstName' => 'Martin',
                'lastName' => 'Dubois',
                'phone' => '0600000000',
                'address' => '10 Rue Sainte-Catherine',
                'zip' => '33000',
                'city' => 'Bordeaux',
                'role' => 'role-admin',
            ],

            // =========================
            // EMPLOYEES
            // =========================
            [
                'email' => 'lucas.bernard@test.com',
                'firstName' => 'Lucas',
                'lastName' => 'Bernard',
                'phone' => '0611111111',
                'address' => '45 Avenue de la Libération',
                'zip' => '33700',
                'city' => 'Mérignac',
                'role' => 'role-employee',
            ],
            [
                'email' => 'camille.moreau@test.com',
                'firstName' => 'Camille',
                'lastName' => 'Moreau',
                'phone' => '0622222222',
                'address' => '18 Rue du Pas-Saint-Georges',
                'zip' => '33000',
                'city' => 'Bordeaux',
                'role' => 'role-employee',
            ],
            [
                'email' => 'antoine.robert@test.com',
                'firstName' => 'Antoine',
                'lastName' => 'Robert',
                'phone' => '0633333333',
                'address' => '15 Rue Victor Hugo',
                'zip' => '33110',
                'city' => 'Le Bouscat',
                'role' => 'role-employee',
            ],

            // =========================
            // USERS (clients)
            // =========================
            [
                'email' => 'sophie.lambert@test.com',
                'firstName' => 'Sophie',
                'lastName' => 'Lambert',
                'phone' => '0644444444',
                'address' => '25 Rue des Remparts',
                'zip' => '33000',
                'city' => 'Bordeaux',
                'role' => 'role-user',
            ],
            [
                'email' => 'thomas.petit@test.com',
                'firstName' => 'Thomas',
                'lastName' => 'Petit',
                'phone' => '0655555555',
                'address' => '12 Avenue Thiers',
                'zip' => '33100',
                'city' => 'Bordeaux',
                'role' => 'role-user',
            ],
            [
                'email' => 'julie.garnier@test.com',
                'firstName' => 'Julie',
                'lastName' => 'Garnier',
                'phone' => '0666666666',
                'address' => '8 Rue Fondaudège',
                'zip' => '33000',
                'city' => 'Bordeaux',
                'role' => 'role-user',
            ],
            [
                'email' => 'nicolas.faure@test.com',
                'firstName' => 'Nicolas',
                'lastName' => 'Faure',
                'phone' => '0677777777',
                'address' => '60 Boulevard George V',
                'zip' => '33000',
                'city' => 'Bordeaux',
                'role' => 'role-user',
            ],
            [
                'email' => 'emma.leroy@test.com',
                'firstName' => 'Emma',
                'lastName' => 'Leroy',
                'phone' => '0688888888',
                'address' => '90 Avenue de la Somme',
                'zip' => '33700',
                'city' => 'Mérignac',
                'role' => 'role-user',
            ],
            [
                'email' => 'claire.dubois@test.com',
                'firstName' => 'Claire',
                'lastName' => 'Dubois',
                'phone' => '0699999999',
                'address' => '5 Cours de l’Intendance',
                'zip' => '33000',
                'city' => 'Bordeaux',
                'role' => 'role-user',
            ],
        ];

        foreach ($users as $data) {
            $user = new User();

            $user->setEmail($data['email']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setPhoneNumber($data['phone']);
            $user->setAddress($data['address']);
            $user->setZipCode($data['zip']);
            $user->setCity($data['city']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $user->setRole(
                $this->getReference($data['role'], \App\Entity\Role::class)
            );

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ARoleFixtures::class,
        ];
    }
}