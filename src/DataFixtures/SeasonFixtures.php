<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Group season
 */
class SeasonFixtures extends Fixture implements DependentFixtureInterface
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
            $seasons = $data['shows'];
            return $seasons;
        }
        return null;
    }

    public function load(ObjectManager $manager): void
    {

        $seasons = $this->series();
        foreach ($seasons as $numberSeason) {
            //if (!$this->hasReference('season_' . $numberSeason['seasons'])) {
                $season = new Season();
                $season->setProgram($this->getReference('program_' . $numberSeason['title']));
                $season->setnumber($numberSeason['seasons']);
                $season->setYear($numberSeason['creation']);
                $season->setDescription($numberSeason['description']);

                $manager->persist($season);
                $this->addReference('season_' . $numberSeason['seasons'].'-'.$numberSeason['title'], $season);

            }
       // }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }

}
