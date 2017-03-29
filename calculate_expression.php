<?php
require 'class-loader.php';


use Volkhin\Pogazam\Calculator;

$businessLogic = new Calculator\BusinessLogic($_POST);
$result = $businessLogic->Process();

echo $result;

