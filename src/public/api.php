<?php header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: text/html; charset=utf-8');
# API Sample: GET http://your-site.com/api/updated

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require '../../config.php';

$app = new \Slim\App(["settings" => $config]);

# Logging Support
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("app.log");
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

###### HELPER FUNCTIONS ######
function get_words($text, $length = 200, $dots = true) {
    $text = trim(preg_replace('#[\s\n\r\t]{2,}#', ' ', $text));
    $text_temp = $text;
    while (substr($text, $length, 1) != " ") { $length++; if ($length > strlen($text)) { break; } }
    $text = substr($text, 0, $length);
    return $text . ( ( $dots == true && $text != '' && strlen($text_temp) > $length ) ? '...' : '');
}
###### ###### ###### ######

# TESTFLIGHT: http://myhost/api/api.php/mirror/{name}
$app->get('/mirror/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("我是你爸爸");

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
        $db -> exec("set names utf8mb4");
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Get all articles with summary, used in the homapage Feed card
$app->get('/all_chapters_summary', function(Request $request, Response $response){
    $sql = "SELECT a.id, a.TermID, a.Title, a.Content, a.TimeUpdated, b.AuthorID, b.CoverImg FROM siren.siren_posts a, siren.siren_terms b WHERE a.TermID = b.id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $db -> exec("set names utf8mb4");
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach ($return as &$article_obj) {
          $article_obj->Content = strip_tags($article_obj->Content);
          $article_obj->Content = get_words($article_obj->Content);
        }
        $db = null;
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
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
        $db -> exec("set names utf8mb4");
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Get a specific article
$app->get('/chapter/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM siren.siren_posts WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $db -> exec("set names utf8mb4");
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        //$return = "hello"
        $db = null;
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
        //echo var_dump($return);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Get a specific work
$app->get('/work/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT id, AuthorID, Name, CoverImg, Tags, TimeCreated FROM siren.siren_terms WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $db -> exec("set names utf8mb4");
        $stmt = $db->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

# Create a chapter in a selected work -- POST
$app->post('/create_new_chapter', function(Request $request, Response $response){
    $data = $request->getParsedBody();

    $token = $data['token'];
    $termid = $data['termid'];
    $title = $data['title'];
    $content = $data['content'];
    $sql_check = "SELECT AccessToken FROM siren.siren_terms WHERE id = :id";
    $sql_insert = "INSERT INTO siren.siren_posts (`id`, `TermID`, `Title`, `TimeUpdated`,
      `Content`) VALUES (NULL, :termid, :title, NOW(), :content);";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $db -> exec("set names utf8mb4");

        $stmt = $db->prepare($sql_check);
        $stmt->execute(array(':id' => $termid));

        $return = $stmt->fetchAll(PDO::FETCH_OBJ)[0]->AccessToken;
        if ($return == $token) {

          $stmt_insert = $db->prepare($sql_insert);
          $stmt_insert->execute(array(
            ':termid' => $termid,
            ':title' => $title,
            ':content' => $content
          ));


          # Success
          echo '{"success"}';
        } else {
          # Not Authenticated
          echo '{"error": {"text": "Error Authenticating"}';
        }

        $db = null;

    } catch(Exception $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->run();
