<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Role;


class ARoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            ['name' => 'ROLE_ADMIN', 'ref' => 'role-admin'],
            ['name' => 'ROLE_EMPLOYEE', 'ref' => 'role-employee'],
            ['name' => 'ROLE_USER', 'ref' => 'role-user'],
        ];

        foreach ($roles as $data) {
            $role = new Role();
            $role->setName($data['name']);

            $this->addReference($data['ref'], $role);

            $manager->persist($role);
        }

        $manager->flush();
    }
}
