<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @Group program
 */
class ProgramFixtures extends Fixture implements DependentFixtureInterface
{

    public function series(): ?array {

        $apiKey = '20f40f4e8301';
        $url = "https://api.betaseries.com/shows/list?key=$apiKey&v=3.0&order=popularity&limit=40";

        $client = HttpClient::create();
        $response = $client->request('GET', $url);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            // Décodez les données de la réponse en tant que tableau associatif
            $data = json_decode($response->getContent(), true);

            // Récupérez la liste des séries
            $shows = $data['shows'];
            return $shows;
        }
        return null;
    }

    public function load(ObjectManager $manager): void
    {

        $shows = $this->series();
        foreach ($shows as $show){
            $program = new Program();
            $program->setTitle($show['title']);
            $program->setSynopsis($show['description']);
            $program->setPoster($show['images']['poster']);
            $genres = $show['genres'];

            foreach ( $genres as $key => $genre) {
                $program->setCategory($this->getReference('category_'.$genre));
                break;
             }
            $manager->persist($program);
            $this->addReference('program_' . $show['title'], $program);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}