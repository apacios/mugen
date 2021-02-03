<?php

namespace App\Provider;

use Imdb\Title;
use Imdb\Config;
use Imdb\TitleSearch;

class ImdbProvider
{
    protected Config $config;
    protected Title $result;

    public function __construct()
    {
        $this->config = new Config();
        $this->config->language = 'fr-FR,fr,en-EN,en';
    }

    public function search(string $videoTitle): self
    {
        $search = new TitleSearch($this->config);
        $this->result = $search->search($videoTitle, null, 1)[0];

        return $this;
    }

    public function getData(): array
    {
        if (empty($this->result)) {
            return [];
        }

        return [
            'storyline' => $this->result->storyLine(),
            'mainPhoto' => $this->result->photo(),
            'mainPictures' => $this->result->mainPictures(),
        ];
    }
}
