<?php
if (!defined('DIR_APPLICATION')) {
    die();
}
$controller = dirname(dirname(__DIR__)).'/payment/bankdebit.php';
require_once $controller;
class ControllerExtensionPaymentBankdebit extends ControllerPaymentBankdebit
{
}