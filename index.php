<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Volkhin\Pogazam\Calendar;

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

    $calendar = new Calendar\Calendar();

    $months = $calendar->getNextMonth();
    $days = $calendar->getDays();
    $pager = $calendar->getPagers();

    $response = $this->view->render($response, "calendar.php", [
        'months' => $months,
        'days' => $days,
        'pager' => $pager,]);

    return $response;
});

$app->post('/calendar/previous/{previous}', function (Request $request, Response $response, array $arguments) {

    $parsedBody = $request->getParsedBody();

    $isExists = array_key_exists(Calendar\Calendar::CURRENT_DAY, $parsedBody);
    $currentMonth = null;
    if ($isExists) {
        $currentMonth = $parsedBody[Calendar\Calendar::CURRENT_DAY];
    }

    $calendar = new \Volkhin\Pogazam\Calendar\Calendar($currentMonth);

    $isExists = array_key_exists(Calendar\Calendar::PREVIOUS_MONTH, $arguments);
    $previous = null;
    if ($isExists) {
        $previous = $arguments[Calendar\Calendar::PREVIOUS_MONTH];
    }

    $months = $calendar->getPreviousMonth($previous);
    $monthMenu = $calendar->getMonthMenu($months);
    $pager = $calendar->getPagers();

    $response = $response->withJson(array(
        Calendar\Calendar::PREVIOUS_MONTH => $pager[Calendar\Calendar::PREVIOUS_MONTH],
        Calendar\Calendar::NEXT_MONTH => $pager[Calendar\Calendar::NEXT_MONTH],
        Calendar\Calendar::MONTH_MENU => $monthMenu,
    ));

    return $response;
});

$app->post('/calendar/next/{next}', function (Request $request, Response $response, array $arguments) {

    $parsedBody = $request->getParsedBody();

    $isExists = array_key_exists(Calendar\Calendar::CURRENT_DAY, $parsedBody);
    $currentMonth = null;
    if ($isExists) {
        $currentMonth = $parsedBody[Calendar\Calendar::CURRENT_DAY];
    }

    $calendar = new \Volkhin\Pogazam\Calendar\Calendar($currentMonth);

    $isExists = array_key_exists(Calendar\Calendar::NEXT_MONTH, $arguments);
    $next = null;
    if ($isExists) {
        $next = $arguments[Calendar\Calendar::NEXT_MONTH];
    }

    $months = $calendar->getNextMonth($next);
    $monthMenu = $calendar->getMonthMenu($months);
    $pager = $calendar->getPagers();

    $response = $response->withJson(array(
        Calendar\Calendar::PREVIOUS_MONTH => $pager[Calendar\Calendar::PREVIOUS_MONTH],
        Calendar\Calendar::NEXT_MONTH => $pager[Calendar\Calendar::NEXT_MONTH],
        Calendar\Calendar::MONTH_MENU => $monthMenu,
    ));

    return $response;
});

$app->post('/calendar/current/{day}', function (Request $request, Response $response, array $arguments) {

    $parsedBody = $request->getParsedBody();

    $indexDay = 'day';

    $isExists = array_key_exists($indexDay, $arguments);
    $currentMonth = null;
    if ($isExists) {
        $currentMonth = $arguments[$indexDay];
    }

    $calendar = new \Volkhin\Pogazam\Calendar\Calendar($currentMonth);

    $isExists = array_key_exists(Calendar\Calendar::PREVIOUS_MONTH, $parsedBody);
    $previous = null;
    if ($isExists) {
        $previous = $parsedBody[Calendar\Calendar::PREVIOUS_MONTH];
    }

    $isExists = array_key_exists(Calendar\Calendar::NEXT_MONTH, $parsedBody);
    $next = null;
    if ($isExists) {
        $next = $parsedBody[Calendar\Calendar::NEXT_MONTH];
    }

    $months = $calendar->setCurrentMonth($previous, $next);
    $monthMenu = $calendar->getMonthMenu($months);
    $days = $calendar->getDays();
    $dayMenu = $calendar->getDayMenu($days);
    $pager = $calendar->getPagers();

    $response = $response->withJson(array(
        Calendar\Calendar::PREVIOUS_MONTH => $pager[Calendar\Calendar::PREVIOUS_MONTH],
        Calendar\Calendar::NEXT_MONTH => $pager[Calendar\Calendar::NEXT_MONTH],
        Calendar\Calendar::MONTH_MENU => $monthMenu,
        Calendar\Calendar::CURRENT_DAY => $pager[Calendar\Calendar::CURRENT_DAY],
        Calendar\Calendar::DAY_MENU => $dayMenu,

    ));

    return $response;
});

$app->run();
