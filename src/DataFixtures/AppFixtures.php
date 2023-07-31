<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        $rh = new User();
        $rh->setEmail("rh@humanbooster.com");
        $rh->setName('rh');
        $rh->setFirstName('rh');
        $rh->setPassword($this->hasher->hashPassword($rh,"rh123@"));
        $rh->setRoles(['ROLE_RH']);
        $rh->setArea('RH');
        $rh->setContractType('CDI');
        $manager->persist($rh);
        $manager->flush();
    }
}
