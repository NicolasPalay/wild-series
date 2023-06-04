<?php

namespace App\Services;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Repository\EpisodeRepository;

class ProgramDuration
{
    private $episodeRepository;

    public function __construct(EpisodeRepository $episodeRepository)
    {
        $this->episodeRepository = $episodeRepository;
    }

    public function calculate(Program $program, Season $season): int
    {

        $seasons = $program->getSeasons();
        $totalDuration = 0;

        foreach ($seasons as $season) {
            $episodes = $this->episodeRepository->findBy(['season' => $season]);

            foreach ($episodes as $episode) {
                $totalDuration += $episode->getDuration();

        }

    }$totalDuration = $totalDuration/60;
        return $totalDuration;
    }
}