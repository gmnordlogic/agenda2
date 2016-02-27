<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// db
$container['db'] = function ($c) use($app){
    $dbSettings = $c->get('settings')['db'];
    $dbhost = $dbSettings['dbhost'];
    $dbuser = $dbSettings['dbuser'];
    $dbpass = $dbSettings['dbpass'];
    $dbname = $dbSettings['dbname'];
    $dbh = new DbHelper("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setApp($app);
    return $dbh;
};

//errorHandler
$container['errorHandler'] = function($c) {
  return function($request, $response, $exception) use($c) {
    $errorResponse = [
        'error_type'=> (isset($exception->customErrorType)?$exception->customErrorType:'error'),
        'errorMessage'=>$exception->getMessage()
      ];
    $errorResponse = json_encode($errorResponse);
    return $c['response']->withStatus(500)
                        ->withHeader('Content-Type','text/html')
                        ->write($errorResponse);
  };
};
