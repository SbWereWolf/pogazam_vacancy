<?php
require 'class-loader.php';


use Volkhin\Pogazam\BusinessLogic;

$businessLogic = new BusinessLogic($_POST);
$result = $businessLogic->Process();

echo $result;

