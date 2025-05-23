<?php

namespace App\DataFixtures;

use App\Enum\JobStatus;
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
        $adminUser = UserFactory::createOne([
            'email' => 'benoit.souillat@gmail.com',
            'firstname' => 'Benoît',
            'lastname' => 'Souillat',
            'password' => 'password',
            'roles' => ['ROLE_ADMIN'],
        ]);
        EmployeeFactory::createOne([
            'firstname' => 'Benoît',
            'lastname' => 'Souillat',
            'email' => 'benoit.souillat@gmail.com',
            'user' => $adminUser,
        ]);
        //UserFactory::createMany(4);
        EmployeeFactory::createMany(10);
        ProjectFactory::createMany(6);
        TaskFactory::createMany(50);

        $manager->flush();
    }
}
