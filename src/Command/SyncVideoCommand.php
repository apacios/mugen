<?php

namespace App\Command;

use App\Entity\Serie;
use App\Entity\Video;
use DateTimeImmutable;
use App\Entity\Category;
use App\Handler\CategoryHandler;
use App\Handler\VideoHandler;
use App\Provider\VideoProvider;
use App\Provider\CategoryProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncVideoCommand extends Command
{
    protected static $defaultName = 'app:sync-videos';
    protected CategoryProvider $categoryProvider;
    protected VideoProvider $videoProvider;
    protected CategoryHandler $categoryHandler;
    protected VideoHandler $videoHandler;

    public function __construct(CategoryProvider $categoryProvider, VideoProvider $videoProvider, CategoryHandler $categoryHandler, VideoHandler $videoHandler)
    {
        $this->categoryProvider = $categoryProvider;
        $this->videoProvider = $videoProvider;
        $this->categoryHandler = $categoryHandler;
        $this->videoHandler = $videoHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Sync videos');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->categoryProvider->getCategoryList() as $categoryName) {
            $videoList = $this->videoProvider->getVideoListFromCategory($categoryName);
            $this->categoryHandler->saveCategory($categoryName);
            $this->videoHandler->saveVideoList($categoryName, $videoList);
        }

        $io->success('Video synchronized');

        return Command::SUCCESS;
    }
}
