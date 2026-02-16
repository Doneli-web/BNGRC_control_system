<?php

use app\controllers\VilleController;
use app\controllers\RegionController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('/api', function(Router $router) use ($app) {

    $router->post('/regions', function() use($app){
        $regions = RegionController::getRegions();
        $app->render('villes', [
            "regions"=>$regions
        ]);
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