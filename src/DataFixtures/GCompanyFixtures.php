<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Company;

class GCompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $company = new Company();

        $company->setAddress('28 rue du bocage');
        $company->setZipCode('33800');
        $company->setCity('Bordeaux');
        $company->setLatitude(null);
        $company->setLongitude(null);

        $manager->persist($company);

        $manager->flush();
    }
}
