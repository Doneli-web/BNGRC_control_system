<?php

use app\controllers\VilleController;
use app\controllers\RegionController;
use app\controllers\DonController;
use app\controllers\TypeDonController;
use app\controllers\ArticleController;
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
        $articles = ArticleController::findAll();
        $app->render('besoins', [
            "typeDon" => $typeDon,
            "articles" => $articles
        ]);
    });

    $router->get('/villes/stats', function() {
        $data = VilleController::getVilleStats();
        Flight::json($data);
    });

    // API pour dashboard complet
    $router->get('/dashboard', function() {
        $data = VilleController::getDashboard();
        Flight::json($data);
    });


    $router->get('/api/article/@id', function($id) use ($app){
        $article = ArticleController::findById($id);
        echo json_encode(["prix_unitaire"=>$article["prix_unitaire"]]);
    });

       $router->get('/dons', function() use($app){
        DonController::showDonsPage($app);
    });

   

    $router->post('/dons/add', function() use($app){
        DonController::addDonFromForm();
    });

    $router->get('/dons/delete/@id', function($id) use($app){
        DonController::deleteDon($id);
        $_SESSION['success'] = "Don supprimé avec succès";
        Flight::redirect('/dons');
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