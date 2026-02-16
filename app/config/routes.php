<?php

use app\controllers\VilleController;
use app\controllers\RegionController;
use app\controllers\DonController;
use app\controllers\TypeDonController;
use app\controllers\ArticleController;
use app\controllers\BesoinController;
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
        $villes = VilleController::getVilles();
        $app->render('besoins', [
            "typeDon" => $typeDon,
            "articles" => $articles,
            "villes" => $villes
        ]);
    });

    $router->get('/villes/stats', function() {
        $data = VilleController::getVilleStats();
        Flight::json($data);
    });

    $router->get('/villes', function() use($app){
    $villes = VilleController::getVilles();
    $stats = VilleController::getVilleStats();
    
    // Organiser les stats par ville
    $ville_stats = [];
    
    // Si $stats est un tableau indexé
    if(isset($stats[0]) && is_array($stats[0])) {
        foreach($stats as $stat) {
            $ville_id = $stat['ville_id'] ?? $stat['id'] ?? null;
            if($ville_id) {
                $ville_stats[$ville_id] = $stat;
            }
        }
    } 
    // Si $stats est déjà un tableau associatif
    else {
        $ville_stats = $stats;
    }
    
    $app->render('villes', [
        "villes" => $villes,
        "ville_stats" => $ville_stats,
        "total_villes" => count($villes)
    ]);
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

    $router->post('/besoins/add', function() use($app){
        if(!isset($_POST["ville"]) || !isset($_POST["article"]) || !isset($_POST["quantite"])){
            $_SESSION['error'] = "Tous les champs sont requis";
            $app->redirect('/besoins');
            return;
        }
        BesoinController::addBesoin($_POST["ville"], $_POST["article"], $_POST["quantite"]);
        $_SESSION['success'] = "Besoin ajouté avec succès";
        $app->redirect('/besoins');
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