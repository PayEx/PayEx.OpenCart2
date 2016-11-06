<?php
if (!defined('DIR_APPLICATION')) {
    die();
}

$controller = dirname(dirname(__DIR__)).'/payment/wywallet.php';
require_once $controller;

class ControllerExtensionPaymentWywallet extends ControllerPaymentWywallet
{
}