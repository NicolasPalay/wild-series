<?php

namespace App\DataFixtures;
use App\Entity\Program;
use App\Entity\Actor;
use App\Repository\ProgramRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private ProgramRepository $programRepository)
    {
    }
    public function series(): ?array {
    $apiKey = '20f40f4e8301';
    $url = "https://api.betaseries.com/shows/list?key=$apiKey&v=3.0&order=popularity&limit=40";

    $client = HttpClient::create();
    $response = $client->request('GET', $url);

    if ($response->getStatusCode() === Response::HTTP_OK) {
        // Décodez les données de la réponse en tant que tableau associatif
    $data = json_decode($response->getContent(), true);

        // Récupérez la liste des séries
    $seasons = $data['shows'];
    return $seasons;
    }
    return null;
    }

    public function load(ObjectManager $manager): void
    {
        $series = $this->series();
        $actors = [];
        $faker = Factory::create();
        for ($i=0;$i<50;$i++){
            $actor = new Actor();
            $actor->setName($faker->name());

            $manager->persist($actor);


        }
        $manager->flush();

        $numActors = mt_rand(3, 9); // Sélectionner un nombre aléatoire entre 3 et 4
        $actors[] = $actor;
        $programs = $this->programRepository->findAll();
        for ($j = 0; $j < $numActors; $j++) {
            foreach ($programs as $program) {
                for ($l = 0; $l < $numActors; $l++) {
                    shuffle($actors); // Mélanger la liste des acteurs

                    $program->addActor($actors[mt_rand(0, count($actors) - 1)]);
                    $manager->flush();
                }
            }
        }

            }

        public function getDependencies()
    {
        return [
          ProgramFixtures::class,
        ];
    }
}
