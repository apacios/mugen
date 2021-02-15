<?php

namespace App\Command;

use App\Entity\Category;
use App\Handler\LibrarySyncHandler;
use App\Provider\VideoProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncVideoCommand extends Command
{
    protected static $defaultName = 'app:sync-videos';
    protected VideoProvider $videoProvider;
    protected LibrarySyncHandler $libraryHandler;
    protected EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        VideoProvider $videoProvider,
        LibrarySyncHandler $libraryHandler
    )
    {
        $this->em = $em;
        $this->videoProvider = $videoProvider;
        $this->libraryHandler = $libraryHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Sync videos');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->em->getRepository(Category::class)->findAll() as $category) {
            $videoList = $this->videoProvider->getVideoListFromCategory($category);
            $this->libraryHandler->saveVideoList($category, $videoList);
        }

        $io->success('Video synchronized');

        return Command::SUCCESS;
    }
}
