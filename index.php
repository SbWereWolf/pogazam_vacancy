<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

spl_autoload_register(function ($class) {
    require("classes/" . $class . ".php");
});

$configuration['displayErrorDetails'] = true;
$configuration['addContentLengthHeader'] = false;

$container = new \Slim\Container(['settings' => $configuration]);
$container['view'] = new \Slim\Views\PhpRenderer("view/");

$app = new \Slim\App($container);

$app->get('/', function (Request $request, Response $response, array $arguments) {

    $calendar = new \Volkhin\Pogazam\Calendar\Calendar();

    $months = $calendar->getNextMonth();
    $days = $calendar->getDays();
    $pager = $calendar->getPagers();

    $response = $this->view->render($response, "calendar.php", [
        'months' => $months,
        'days' => $days,
        'pager' => $pager,]);

    return $response;
});
$app->run();
