<?php
require_once '../App/Infrastructure/sdbh.php';
require_once '../App/Services/CalculateService.php';
require_once '../App/Controllers/CalculateController.php';

use App\Infrastructure\sdbh;
use App\Services\CalculateService;
use App\Controllers\CalculateController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbh = new sdbh();
    $calculateService = new CalculateService($dbh);
    $controller = new CalculateController($calculateService);
    $controller->handleRequest();
}
