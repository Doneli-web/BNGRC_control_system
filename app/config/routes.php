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
        $totalVilles = VilleController::getTotalVilles();
        $totalDons = DonController::getTotalDons();

        $app->render('index', [
            'total_villes' => $totalVilles,
            'total_dons' => $totalDons
        ]);
    });

    // Route pour lancer la simulation côté serveur
    $router->post('/simulate', function() use($app){
        \app\controllers\DispatchController::simulate();
    });

    $router->get('/besoins', function() use($app){
        $typeDon = TypeDonController::findAll();
        $articles = ArticleController::findAll();
        $villes = VilleController::getVilles();
        $besoins = BesoinController::findAllBesoinInfo();
        $app->render('besoins', [
            "typeDon" => $typeDon,
            "articles" => $articles,
            "villes" => $villes,
            "besoins" => $besoins
        ]);
    });

    $router->get('/villes/stats', function() {
        $data = VilleController::getVilleStats();
        Flight::json($data);
    });

    $router->get('/villes', function() use($app){
    $villes = VilleController::getVilles();
    $stats = VilleController::getVilleStats();
    
    $ville_stats = [];
    
    if(isset($stats[0]) && is_array($stats[0])) {
        foreach($stats as $stat) {
            $ville_id = $stat['ville_id'] ?? $stat['id'] ?? null;
            if($ville_id) {
                $ville_stats[$ville_id] = $stat;
            }
        }
    } 
    else {
        $ville_stats = $stats;
    }
    
    $app->render('villes', [
        "villes" => $villes,
        "ville_stats" => $ville_stats,
        "total_villes" => count($villes)
    ]);

    
   
});

     $router->get('/villes/detail/@id', function($id) use($app){
        $data = VilleController::getDetailVille($id);
        
        if($data === null) {
            $app->render('404', []);
            return;
        }
        
        $app->render('ville_detail', [
            "ville" => $data['ville'],
            "besoins" => $data['besoins'],
            "dons" => $data['dons'],
            "stats" => [] 
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

    $router->get('/api/articles/by-type/@idType', function($idType) use ($app){
        $articles = ArticleController::findByType($idType);
        Flight::json($articles);
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