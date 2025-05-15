<?php

namespace App\DataFixtures;

use App\Factory\EmployeeFactory;
use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use App\Factory\ProjectFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        UserFactory::createMany(4);
        EmployeeFactory::createMany(10);
        ProjectFactory::createMany(6);
        TaskFactory::createMany(50);

        $manager->flush();
    }
}
