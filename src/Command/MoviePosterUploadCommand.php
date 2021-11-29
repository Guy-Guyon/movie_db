<?php

namespace App\Command;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoviePosterUploadCommand extends Command
{
    protected static $defaultName = 'app:movie:poster-upload';

    private $movieRepository;
    private $em;

    public function __construct(MovieRepository $movieRepo, EntityManagerInterface $em)
    {
        $this->movieRepository = $movieRepo;
        $this->em = $em;
        // attention il faut lancer le constructeur du parent
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('movieId', InputArgument::OPTIONAL, 'movie Id')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $movieId = $input->getArgument('movieId');

        if ($movieId) {
            $movie = $this->movieRepository->find($movieId);
            $movies = [$movie];
        }
        else 
        {
            // récupérer la liste de tous les movies
            $movies = $this->movieRepository->findAll();
        }


        // ici on pourrait stocker l'api key en tant que paramètre (cad dans services.yaml)
        $omdbApiUrl = "http://www.omdbapi.com/?apikey=e2601269&t=";
        foreach( $movies as $movie)
        {
            if (empty($movie->getPoster()))
            {
                echo 'updating movie ' . $movie->getId() . "\r"; 
                // demander à omdbapi les informations à propos du film
                $titleForSearch = str_replace(' ', '+', $movie->getTitle());
                $omdbApiResultJson = file_get_contents($omdbApiUrl . $titleForSearch);
                $omdbApiResultObj = json_decode($omdbApiResultJson);
    
                if ($omdbApiResultObj->Response === "True")
                {
                    // il n'y a pas d'affiche référencée
                    if ($omdbApiResultObj->Poster != "N/A")
                    {
                        // stocker l'url de l'affiche dans la propriété Poster du Movie
                        dump($omdbApiResultObj);
                        echo ' --  ' . $omdbApiResultObj->Poster . "\r"; 
                        $movie->setPoster($omdbApiResultObj->Poster);
                        $this->em->flush();
                    }
                }
            }
        }

        // if ($input->getOption('option1')) {
        //     // ...
        // }


        $io->success('Les affiches ont été mises à jour');


        return Command::SUCCESS;
    }
}
