<?php header('Access-Control-Allow-Origin: *');
# API Sample: GET http://your-site.com/api/updated

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require '../../config.php';

$app = new \Slim\App(["settings" => $config]);

# Logging Support
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

# Access database
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

# TESTFLIGHT: http://myhost/api/api.php/mirror/{name}
$app->get('/mirror/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

# Provision: Database API Connection

# Get all articles, unsorted
$app->get('/all_chapters', function(Request $request, Response $response){
    $sql = "SELECT id, TermID, Title, TimeUpdated FROM siren.siren_posts";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Get all works, unsorted
$app->get('/all_works', function(Request $request, Response $response){
    $sql = "SELECT * FROM siren.siren_terms";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Get a specific article
$app->get('/chapter/{id}', function(Request $request, Response $response){
    $sql = "SELECT * FROM siren.siren_posts WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Get a specific work
$app->get('/work/{id}', function(Request $request, Response $response){
    $sql = "SELECT * FROM siren.siren_terms" WHERE id = $id;
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->run();

