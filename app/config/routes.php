<?php

use app\controllers\AdminController;
use app\controllers\CategoryController;
use app\controllers\ObjetController;
use app\controllers\ImageController;
use app\controllers\UserController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

    // $router->get('/', [ UserController::class, 'getTrajets' ]);
    $router->get('/', function() use($app){
        $app->render('admin', []);
    });

    $router->post('/admin-verification', function() use($app){
        $email = $_POST["email"];
        $password = $_POST["password"];

        $server_response = AdminController::verificationAdmin($email, $password);
        if($server_response){
            session_start();
            $_SESSION["admin"] = AdminController::getId($email)[0];
            echo json_encode([
                "success"=>true,
                "error"=>false
            ]);
        }else{
            echo json_encode([
                "success"=>false,
                "error"=>true
            ]);
        }
    });

    $router->get('/admin-home', function() use($app){
        $app->render('admin-model', [
            "contentPage"=>"admin-dashboard",
            "titlePage"=>"Home"
        ]);
    });

    $router->get('/admin-objects', function() use($app){
        $objects = ObjetController::getAllObject();
        
        // Pour chaque objet, récupérer ses images
        foreach ($objects as &$object) {
            $images = ImageController::getById($object['idObjet'] ?? $object['id_category']);
            $object['images'] = $images;
            // Prendre la première image comme image principale
            $object['main_image'] = !empty($images) ? $images[0]['img'] : '/assets/images/placeholder.jpg';
        }
        
        $app->render('admin-model', [
            "contentPage"=>"admin-objects",
            "titlePage"=>"Object",
            "objects" => $objects
        ]);
    });

    $router->post('/admin-object-create', function() use($app){
        $id_category = $_POST["id_category"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $prix_estimatif = $_POST["prix_estimatif"];
        $id_user = $_SESSION["admin"] ?? 1; 
        
        ObjetController::addnewObject($id_category, $name, $description, $prix_estimatif, $id_user);
        
        $objet = ObjetController::getById($name);
        $id_objet = $objet[0]['idObjet'] ?? $objet[0]['id_category'];
        
        // Ajouter les images si présentes
        if (isset($_FILES['images'])) {
            $this->handleImageUploads($_FILES['images'], $id_objet);
        }
        
        $app->redirect('/admin-objects');
    });

    $router->get('/admin-objects-delete/@id', function($id) use($app){
        ObjetController::removeObject($id);
        $app->redirect('/admin-objects');
    });
    
    $router->post('/admin-object-update/@id', function($id) use($app){
        // ObjetController::updateObject($id, $_POST);
        $app->redirect('/admin-objects');
    });
   
    $router->get('/admin-categories', function() use($app){
        $categories = CategoryController::getAllCategory();
        foreach($categories as &$cat){
            $count = ObjetController::countObjectPerCategory($cat["id"]);
            $cat["countObject"] = $count;
        }
        $app->render('admin-model', [
            "contentPage"=>"admin-categories",
            "categories"=>$categories,
            "titlePage"=>"Category"
        ]);
    });
    $router->post('/admin-category-create',function() use($app){
        $name = $_POST["category_name"];
        CategoryController::addCategory($name);
        $app->redirect('/admin-categories');
    });
    $router->get('/admin-categories-delete/@id', function($id) use($app){
        CategoryController::removeCategory($id);
        $app->redirect('/admin-categories');
    });

    // Route pour récupérer les objets par catégorie (API)
    $router->get('/api/objects/by-category/@id_category', function($id_category) use($app){
        $objects = ObjetController::getByCategory($id_category);
        foreach ($objects as &$object) {
            $images = ImageController::getById($object['idObjet'] ?? $object['id_category']);
            $object['images'] = $images;
        }
        header('Content-Type: application/json');
        echo json_encode($objects);
    });


    $router->get('/api/object/@id', function($id) use($app){
        // $object = ObjetController::getById($id);
        // $images = ImageController::getById($id);
        header('Content-Type: application/json');
        echo json_encode(['object' => $object ?? [], 'images' => $images ?? []]);
    });


    $router->get('/logout', function() use($app){
        session_start();
        session_destroy();
        $app->redirect('/');
    });

    $router->get('/login-user', function() use($app){
        $app->render('login-user', []);
    });

    $router->get('/signUp', function() use($app){
        $app->render('signUp', []);
    });

    $router->post('/validate-signUp', function() use($app){
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        UserController::addUser($username, $email, $password);
            session_start();
            $_SESSION["user"] = UserController::getByEmail($email)[0]['email'];
            echo json_encode([
                "success"=>true,
                "error"=>false
            ]);
    });

    $router->get("/user-home", function() use($app){
        session_start();
        $user = UserController::getByEmail($_SESSION["user"] ?? "")[0] ?? null;
        $app->render('user-model', [
            "user" => $user,
            "contentPage" => "user-home",
            "titlePage" => "Home"
        ]);
    });

    $router->post('/user-verification', function() use($app){
        $email = $_POST["email"];
        $password = $_POST["password"];

        $server_response = UserController::verifyUser($email, $password);
        if($server_response){
            session_start();
            $_SESSION["user"] = UserController::getByEmail($email)[0]['email'];
            echo json_encode([
                "success"=>true,
                "error"=>false
            ]);
        }else{
            echo json_encode([
                "success"=>false,
                "error"=>true
            ]);
        }
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