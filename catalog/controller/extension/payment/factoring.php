<?php
if (!defined('DIR_APPLICATION')) {
    die();
}
$controller = dirname(dirname(__DIR__)).'/payment/factoring.php';
require_once $controller;
class ControllerExtensionPaymentFactoring extends ControllerPaymentFactoring
{
}