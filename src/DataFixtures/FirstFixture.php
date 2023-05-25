<?php
namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FirstFixture extends Fixture
{
public function load(ObjectManager $manager)
{
// Contenu de la première fixture
}

public function getOrder()
{
return 1; // L'ordre de cette fixture est 1
}
}

class SecondFixture extends Fixture
{
public function load(ObjectManager $manager)
{
// Contenu de la deuxième fixture
}

public function getOrder()
{
return 2; // L'ordre de cette fixture est 2
}
}