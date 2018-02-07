<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require __DIR__ . '/../vendor/autoload.php';
//$client = new \GuzzleHttp\Client();
//$client->request('GET', 'http://127.0.0.1:8000/api/v1/users');
//$client = new GuzzleHttp\Client();
//$res = $client->request('GET', 'http://127.0.0.1:8000/api/v1/users');


$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://kidlou-shop.local',
    'defaults' => [
        'exceptions' => false
    ]
        ]);

$res = $client->post('/api/v1/users');

echo $res->getStatusCode();
echo $res->getBody();

//echo $response;


