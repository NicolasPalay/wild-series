<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class EpisoleFixtures extends Fixture implements DependentFixtureInterface
{
    public function series(): ?array
    {
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

    const EPISODES = ['Forte', 'emotion', 'vitesse', 'arrété', 'police', 'accident', 'violence'];
    const SEASONS =[
        1,2,3,4
    ];
    public function load(ObjectManager $manager): void
    {
        $seasons = $this->series();
        foreach (self::SEASONS as $seas) {
            foreach ($seasons as $numberSeason) {
                foreach (self::EPISODES as $nameEpisode) {
                    $episode = new Episode();
                    $episode->setSeason($this->getReference('season_' . $seas . '-' . $numberSeason['title']));
                    $episode->setTitle($nameEpisode);
                    $manager->persist($episode);
                }

            }

        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}