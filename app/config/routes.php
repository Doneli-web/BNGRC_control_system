<?php

use app\controllers\VilleController;
use app\controllers\RegionController;
use app\controllers\TypeDonController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('/', function(Router $router) use ($app) {

    // $router->get('/', [ UserController::class, 'getTrajets' ]);
    $router->get('/', function() use($app){
        $app->render('index', []);
    });
    $router->get('/regions/@id', function($id) use($app){
        $region = RegionController::getRegionById($id);
        $app->render('villes', [
            "region"=>$region
        ]);
    });

    $router->post('/regions', function($id) use($app){
        $RegionName = $_POST["name"];
        RegionController::addRegion($RegionName);
        $app->render('villes', [
            "message"=>"Region added successfully"
        ]);
    });

    $router->get('/besoins', function() use($app){
        $typeDon = TypeDonController::findAll();
        $app->render('besoins', [
            "typeDon" => $typeDon
        ]);
    });

    $router->get('/*', function() use($app){
        $app->render('404', []);
    });



   
    // $router->post('/register', function() use($app){
    //     $username = $_POST["username"];
    //     $server_response = UserController::addUser($username);
    //     echo $server_response;
    // });
    


    $router->group('/api', function() use ($router) {

    });

})/*, [ SecurityHeadersMiddleware::class ])*/;