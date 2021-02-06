<?php

namespace App\Provider;

use Imdb\Title;
use Imdb\Config;
use Imdb\TitleSearch;

class ImdbProvider
{
    protected Config $config;
    protected $result;

    public function __construct()
    {
        $this->config = new Config();
        $this->config->language = 'fr-FR,fr,en-EN,en';
    }

    public function search(string $videoTitle): self
    {
        $search = new TitleSearch($this->config);
        $this->result = $this->getOneElement(
            $search->search($videoTitle, [TitleSearch::MOVIE, TitleSearch::TV_SERIES], 1)
        );

        return $this;
    }

    public function getData(): array
    {
        if (empty($this->result)) {
            return [
                'rate' => '',
                'storyline' => '',
                'mainPhoto' => '',
                'mainPictures' => '',
            ];
        }

        return [
            'rate' => $this->result->rating(),
            'storyline' => $this->result->storyLine(),
            'mainPhoto' => $this->result->photo(),
            'mainPictures' => $this->result->mainPictures(),
        ];
    }

    private function getOneElement(array $results)
    {
        if (empty($results[0])) {
            return [];
        }

        return $results[0];
    }
}
