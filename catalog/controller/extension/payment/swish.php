<?php
if (!defined('DIR_APPLICATION')) {
    die();
}

$controller = dirname(dirname(__DIR__)).'/payment/'.basename(__FILE__);
require_once $controller;

class ControllerExtensionPaymentSwish extends ControllerPaymentSwish
{
}