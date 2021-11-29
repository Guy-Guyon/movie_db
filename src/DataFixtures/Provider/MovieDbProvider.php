<?php

namespace App\DataFixtures\Provider;

class MovieDbProvider extends \Faker\Provider\Base
{

    public function movieGenre()
    {
        $genres = [
            'Action',
            'Comédie',
            'Drame',
            'Science-Fiction',
            'Fantastique',
            'Horreur',
            'Policier',
        ];

        $genreToReturn = $genres[rand(0, count($genres) - 1)];
        return $genreToReturn;
    }
}
