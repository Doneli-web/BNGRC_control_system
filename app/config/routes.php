<?php

use app\controllers\VilleController;
use app\controllers\RegionController;
use app\controllers\DonController;
use app\controllers\TypeDonController;
use app\controllers\ArticleController;
use app\controllers\BesoinController;
use app\controllers\AchatController;
use app\controllers\ConfigController;
use app\models\DispatchModel;
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

    $router->get('/simulation', function() use($app){
        $app->render('simulation', []);
    });
        // API pour simulation par page : besoins, dons, dispatchs
    $router->get('/api/dispatch/besoins', function() {
        $besoins = BesoinController::findAll();
        Flight::json($besoins);
    });

    $router->get('/api/dispatch/villes', function() {
        $villes = VilleController::getVilles();
        Flight::json(['villes' => $villes]);
});

    $router->get('/api/dispatch/dons', function() {
        $dons = DonController::getAll();
        Flight::json($dons);
    });

    $router->get('/api/dispatch/dispatchs', function() {
        $db = Flight::db();
        $dispatchs = $db->query("SELECT * FROM BNGRC_dispatch ORDER BY date_dispatch ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
        Flight::json($dispatchs);
    });

    $router->post('/api/dispatch/simulate', function() {
        $db = Flight::db();
        $dispatchModel = new DispatchModel($db);
        $dispatchModel = new DispatchModel($db);
        $count = $db->query("SELECT COUNT(*) FROM BNGRC_dispatch")->fetchColumn();
        if($count > 0){
            Flight::json(['status'=>'error','message'=>'Simulation déjà effectuée'], 400);
            return;
        }

        try {
            $db->beginTransaction();
            $inserted = $dispatchModel->simulateDispatch();
            $db->commit();

            // Retourner un résumé
            Flight::json([
                'status' => 'ok',
                'data' => [],
                'statistics' => [
                    'attributions_creees' => $inserted
                ]
            ]);
        } catch(\Exception $e) {
            $db->rollBack();
            Flight::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    $router->post('/api/dispatch/simulatePreview', function() {
        $db = Flight::db();
        $dispatchModel = new DispatchModel($db);

        try {
            $besoins = $dispatchModel->getBesoinNonComble();
            $dons = $dispatchModel->getDonDisponible();

            $besoinRemaining = [];
            foreach($besoins as $b) {
                $besoinRemaining[$b['id']] = (int)$b['quantite'];
            }

            $previewDispatches = [];
            $totalAttributions = 0;
            $donsUtilises = [];
            $villesServies = [];

            foreach($dons as $don) {
                $donQty = (int)$don['quantite'];
                if($donQty <= 0) continue;

                foreach($besoins as $b) {
                    if((int)$b['idArticle'] !== (int)$don['idArticle']) continue;
                    $needId = $b['id'];
                    $needRem = $besoinRemaining[$needId] ?? 0;
                    if($needRem <= 0) continue;

                    $alloc = min($donQty, $needRem);
                    if($alloc <= 0) continue;

                    $previewDispatches[] = [
                        'idDon' => $don['id'],
                        'idBesoin' => $needId,
                        'quantite' => $alloc,
                        'date_dispatch' => date('Y-m-d H:i:s')
                    ];
                    $totalAttributions++;

                    $donQty -= $alloc;
                    $besoinRemaining[$needId] -= $alloc;

                    if(!in_array($b['idVille'], $villesServies)) {
                        $villesServies[] = $b['idVille'];
                    }

                    if($donQty <= 0) break;
                }
            }

            Flight::json([
                'status' => 'ok',
                'data' => $previewDispatches,
                'statistics' => [
                    'attributions_creees' => $totalAttributions,
                    'villes_servies' => count($villesServies),
                    'total_besoins' => count($besoins),
                    'total_dons' => count($dons)
                ]
            ]);
        } catch(\Exception $e) {
            Flight::json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
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


    $router->get('/achats', function() use($app){
        AchatController::showAchatsPage($app);
    });

    $router->post('/achats/frais/update', function() use($app){
        ConfigController::updateFraisAchat();
        $_SESSION['success'] = "Frais mis à jour avec succès";
        Flight::redirect('/achats');
    });

    $router->post('/achats/calculer', function() {
        $idDon = $_POST["idDon"] ?? null;
        $idBesoin = $_POST["idBesoin"] ?? null;
        $montant = $_POST["montant"] ?? null;
        
        if(!$idDon || !$idBesoin || !$montant){
            echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
            return;
        }
        
        $resultat = AchatController::calculerAchat($idDon, $idBesoin, $montant);
    
        if($resultat){
            echo json_encode(["success" => true, "data" => $resultat]);
        }else{
            echo json_encode(["success" => false, "message" => "Calcul impossible"]);
        }
    });

    $router->post('/achats/effectuer', function() use($app) {
        $idDon = $_POST["idDon"] ?? null;
        $idBesoin = $_POST["idBesoin"] ?? null;
        $montant_utilise = $_POST["montant_utilise"] ?? null;
        $frais_pourcentage = $_POST["frais_pourcentage"] ?? null;
        $frais_montant = $_POST["frais_montant"] ?? null;
        $montant_total = $_POST["montant_total"] ?? null;
        
        error_log("Tentative achat: don=" . $idDon . ", besoin=" . $idBesoin . ", montant=" . $montant_utilise);
        
        if(!$idDon || !$idBesoin || !$montant_utilise){
            $_SESSION['error'] = "Paramètres manquants";
            Flight::redirect('/achats');
            return;
        }
        
        $resultat = AchatController::validerAchat($idDon, $idBesoin, $montant_utilise, $frais_pourcentage, $frais_montant, $montant_total);
        
        if($resultat){
            $_SESSION['success'] = "Achat effectué avec succès";
            error_log("Achat réussi");
        } else {
            $_SESSION['error'] = "Erreur lors de l'achat";
            error_log("Achat échoué");
        }
        
        Flight::redirect('/achats');
    });

    $router->get('/achats/delete/@id', function($id) use($app){
        AchatController::deleteAchat($id);
        $_SESSION['success'] = "Achat supprimé avec succès";
        Flight::redirect('/achats');
    });

    $router->get('/achats/ville/@id', function($id) use($app){
        
        $app->render('achats', [
            "ville_id" => $id
        ]);
    });

    $router->get('/config/frais', function() {
        $frais = ConfigController::getFraisAchat();
        echo json_encode(["success" => true, "data" => $frais]);
    });

    $router->post('/config/frais/update', function() use($app) {
        $frais = $_POST["frais"] ?? null;
        
        if($frais && is_numeric($frais) && $frais >= 0 && $frais <= 100) {
            ConfigController::updateFraisAchat($frais);
            $_SESSION['success'] = "Frais mis à jour avec succès";
        } else {
            $_SESSION['error'] = "Frais invalide";
        }
        
        Flight::redirect('/achats');
    });

    $router->get('/recapitulatif', function() use ($app){
        $totalBesoin = floatval(BesoinController::getTotalBesoinPrice());
        $totalSatisfait = BesoinController::getMontantSatisfait();
        $app->render('recapitulatif',[
            "totalBesoin" => $totalBesoin,
            "totalMontantSatisfait" => $totalSatisfait
        ]);
    });

    $router->get('/api/recap', function() use($app){
        $db = Flight::db();
        $besoinModel = new \app\models\BesoinModel($db);
        $totalBesoin = (float)$besoinModel->getTotalPrice();
        $totalSatisfait = (float)$besoinModel->getMontantSatisfait();
        Flight::json([
            'totalBesoin' => $totalBesoin,
            'totalSatisfait' => $totalSatisfait
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