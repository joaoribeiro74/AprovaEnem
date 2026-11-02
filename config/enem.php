<?php

return [
    'dataset_path' => env('ENEM_DATASET_PATH', database_path('data/enem-api/public')),

    'layouts' => [
        'natureza-humanas-linguagens-matematica' => [
            [1, 45, 'ciencias-natureza'],
            [46, 90, 'ciencias-humanas'],
            [91, 135, 'linguagens'],
            [136, 180, 'matematica'],
        ],
        'humanas-natureza-linguagens-matematica' => [
            [1, 45, 'ciencias-humanas'],
            [46, 90, 'ciencias-natureza'],
            [91, 135, 'linguagens'],
            [136, 180, 'matematica'],
        ],
        'linguagens-humanas-natureza-matematica' => [
            [1, 45, 'linguagens'],
            [46, 90, 'ciencias-humanas'],
            [91, 135, 'ciencias-natureza'],
            [136, 180, 'matematica'],
        ],
    ],

    'years' => [
        2009 => 'natureza-humanas-linguagens-matematica',
        2010 => 'humanas-natureza-linguagens-matematica',
        2011 => 'humanas-natureza-linguagens-matematica',
        2012 => 'humanas-natureza-linguagens-matematica',
        2013 => 'humanas-natureza-linguagens-matematica',
        2014 => 'humanas-natureza-linguagens-matematica',
        2015 => 'humanas-natureza-linguagens-matematica',
        2016 => 'humanas-natureza-linguagens-matematica',
        2017 => 'linguagens-humanas-natureza-matematica',
        2018 => 'linguagens-humanas-natureza-matematica',
        2019 => 'linguagens-humanas-natureza-matematica',
        2020 => 'linguagens-humanas-natureza-matematica',
        2021 => 'linguagens-humanas-natureza-matematica',
        2022 => 'linguagens-humanas-natureza-matematica',
        2023 => 'linguagens-humanas-natureza-matematica',
    ],

    'annulled' => [
        2009 => [101],
        2020 => [135, 157],
        2021 => [138],
        2022 => [157],
    ],
];
