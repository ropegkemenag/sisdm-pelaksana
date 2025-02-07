<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('auth', 'Auth::index');
$routes->get('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('auth/callback', 'Auth::callback');

$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard',['filter' => 'auth']);
$routes->get('pengaturan', 'Pengaturan::index',['filter' => 'auth']);
$routes->post('pengaturan/save', 'Pengaturan::save',['filter' => 'auth']);

$routes->group("pertama",['filter' => 'auth'], function ($routes) {
    $routes->get('', 'Pertama::index');
    $routes->get('getdata', 'Pertama::getdata');
    $routes->get('getdataproses', 'Pertama::getdataproses');
    $routes->get('Pertama/(:any)', 'Pertama::getdata/$1');
    $routes->get('add/(:any)', 'Pertama::add/$1/$2');
    $routes->post('proses', 'Pertama::proses');
    $routes->post('generate', 'Pertama::generateDoc');
});