<?php

namespace App\Provider;

use Imdb\Title;
use Imdb\Config;
use Imdb\TitleSearch;

class ImdbProvider
{
    protected Config $config;
    protected ?Title $result;

    public function __construct()
    {
        $this->config = new Config();
        $this->config->language = 'fr-FR,fr,en-EN,en';
    }

    /**
     * Search video title on IMDB
     *
     * @param string $videoTitle
     * @return self
     */
    public function search(string $videoTitle): self
    {
        $search = new TitleSearch($this->config);
        $this->result = $this->getOneElement(
            $search->search($videoTitle, [TitleSearch::MOVIE, TitleSearch::TV_SERIES], 1)
        );

        return $this;
    }

    /**
     * Get data from IMDB
     *
     * @return array
     */
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

    public function saveMainPhoto(int $videoId): bool
    {
        return $this->result->savephoto('/var/www/html/public/thumbnails/' . $videoId . '.png', false);
    }

    /**
     * Get only one element
     *
     * @param array $results
     * @return null|Title
     */
    private function getOneElement(array $results): ?Title
    {
        if (empty($results[0])) {
            return null;
        }

        return $results[0];
    }
}
