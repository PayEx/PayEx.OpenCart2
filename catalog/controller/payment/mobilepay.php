<?php
if (!defined('DIR_APPLICATION')) {
    die();
}
require_once DIR_SYSTEM . '../vendors/payex/php-api/src/PayEx/Px.php';
require_once DIR_SYSTEM . 'Payex/Payex.php';
require_once DIR_SYSTEM . 'Payex/OcRoute.php';

class ControllerPaymentMobilepay extends Controller
{
    protected $_module_name = 'mobilepay';

    protected static $_px;

    /**
     * Index Action
     */
   public function index()
    {
        $this->language->load( OcRoute::getPaymentRoute('payment/') . 'mobilepay');

        $data['text_title'] = $this->language->get('text_title');
        $data['text_description'] = $this->language->get('text_description');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success');
        $data['action'] = $this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/confirm');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mobilepay.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/mobilepay.tpl', $data);
        } else {
           return $this->load->view(OcRoute::getTemplate('payment/mobilepay.tpl'), $data);
        }
    }

    /**
     * Confirm Action
     */
    public function confirm()
    {
        $this->language->load( OcRoute::getPaymentRoute('payment/') . 'payex_error');
        $this->load->model('checkout/order');
        $this->load->model('module/payex');

        $order_id = $this->session->data['order_id'];
        if (empty($order_id)) {
            $this->session->data['payex_error'] = $this->language->get('error_invalid_order');
            $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }

        $order = $this->model_checkout_order->getOrder($order_id);

        // Client language
	    $language = $this->config->get('mobilepay_client_language');
        if (empty($language)) {
	        $language = $this->getLocale($this->language->get('code'));
        }

        //$amount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);

	    // Get products of order
	    $items = $this->model_module_payex->getProductItems($order_id, $this->cart->getProducts(), $this->session->data['shipping_method']);

	    // Calculate order amount
	    if (function_exists('array_column')) {
		    $amount = array_sum(array_column($items, 'price_with_tax'));
	    } else {
		    // For older PHP versions (< 5.5.0)
		    $amount = 0;
		    foreach ($items as $item) {
			    $amount += $item['price_with_tax'];
		    }
	    }

        // Call PxOrder.Initialize8
        $params = array(
            'accountNumber' => '',
            'purchaseOperation' => $this->config->get('mobilepay_transactiontype'),
            'price' => round($amount * 100),
            'priceArgList' => '',
            'currency' => strtoupper($order['currency_code']),
            'vat' => 0,
            'orderID' => $order['order_id'],
            'productNumber' => $order['customer_id'],
            'description' => html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'),
            'clientIPAddress' => $order['ip'],
            'clientIdentifier' => 'USERAGENT=' . $order['user_agent'],
            'additionalValues' => 'RESPONSIVE=1&USEMOBILEPAY=True',
            'externalID' => '',
            'returnUrl' => $this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/success', '', 'SSL'),
            'view' => 'CREDITCARD',
            'agreementRef' => '',
            'cancelUrl' => $this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/cancel', '', 'SSL'),
            'clientLanguage' => $language
        );
        $result = $this->getPx()->Initialize8($params);
        if ($result['code'] !== 'OK' || $result['description'] !== 'OK' || $result['errorCode'] !== 'OK') {
            $this->session->data['payex_error'] = $result['errorCode'] . ' (' . $result['description'] . ')';
            $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }
        $redirectUrl = $result['redirectUrl'];
        //$orderRef = $result['orderRef'];

        $this->response->redirect($redirectUrl);
    }

    /**
     * Success Action
     */
    public function success()
    {
        $this->load->language( OcRoute::getPaymentRoute('payment/') . 'payex_error');
        $this->load->model('checkout/order');
        $this->load->model('module/mobilepay');

        $order_id = $this->session->data['order_id'];
        if (empty($order_id)) {
            $this->session->data['payex_error'] = $this->language->get('error_invalid_order');
            $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }

        $orderRef = $this->request->get['orderRef'];
        if (empty($orderRef)) {
            $this->session->data['payex_error'] = $this->language->get('error_invalid_order_reference');
            $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }

        // Call PxOrder.Complete
        $params = array(
            'accountNumber' => '',
            'orderRef' => $orderRef
        );
        $result = $this->getPx()->Complete($params);
        if ($result['errorCodeSimple'] !== 'OK') {
            $message = $result['errorCode'] . ' (' . $result['description'] . ')';
            if (isset($result['orderId'])) {
                $this->model_checkout_order->addOrderHistory($result['orderId'], $this->config->get('mobilepay_failed_status_id'), $message, true);
            }

            $this->session->data['payex_error'] = $message;
            $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }

        if (!isset($result['transactionNumber'])) {
            $result['transactionNumber'] = '';
        }

        // Get Transaction status
        $transaction_status = (int)$result['transactionStatus'];

        // Save Transaction
        $this->model_module_mobilepay->addTransaction($order_id, $result['transactionNumber'], $transaction_status, $result, isset($result['date']) ? strtotime($result['date']) : time());

        /* Transaction statuses:
        0=Sale, 1=Initialize, 2=Credit, 3=Authorize, 4=Cancel, 5=Failure, 6=Capture */
        switch ($transaction_status) {
            case 0:
            case 6:
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('mobilepay_completed_status_id'), '', true);
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
                break;
            case 1:
            case 3:
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('mobilepay_pending_status_id'), '', true);
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
                break;
            case 4:
                // Cancel
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('mobilepay_canceled_status_id'), '', true);
                $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
                break;
            case 5:
            default:
                // Error
                $error_code = $result['transactionErrorCode'];
                $error_description = $result['transactionThirdPartyError'];
                if (empty($error_code) && empty($error_description)) {
                    $error_code = $result['code'];
                    $error_description = $result['description'];
                }
                $message = $error_code . ' (' . $error_description . ')';

                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('mobilepay_failed_status_id'), $message, true);
                $this->session->data['payex_error'] = $this->language->get('error_payment_declined');
                $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }
    }

    /**
     * Cancel Action
     */
    public function cancel()
    {
        $this->response->redirect($this->url->link('checkout/cart', '', 'SSL'));
    }

    /**
     * Error Action
     */
    public function error()
    {
        $this->load->language( OcRoute::getPaymentRoute('payment/') . 'payex_error');

        $data['heading_title'] = $this->language->get('heading_title');
        if (!empty($this->session->data['payex_error'])) {
            $data['description'] = $this->session->data['payex_error'];
        } else {
            $data['description'] = $this->language->get('text_error');
        }
        $data['link_text'] = $this->language->get('link_text');
        $data['link'] = $this->url->link('checkout/checkout', '', 'SSL');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');

	    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payex_error.tpl')) {
		    $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/payex_error.tpl', $data));
	    } else {
		    $this->response->setOutput($this->load->view(OcRoute::getTemplate('payment/') . 'payex_error.tpl', $data));
	    }
    }

    /**
     * Get PayEx Handler
     * @return \PayEx\Px
     */
    protected function getPx()
    {
        if (is_null(self::$_px)) {
            $account_number = $this->config->get('mobilepay_account_number');
            $encryption_key = $this->config->get('mobilepay_encryption_key');
            $mode = $this->config->get('mobilepay_mode');
            self::$_px = new \PayEx\Px();
            self::$_px->setEnvironment($account_number, $encryption_key, ($mode !== 'LIVE'));
            self::$_px->setUserAgent(sprintf("PayEx.Ecommerce.Php/%s PHP/%s OpenCart/%s PayEx.OpenCart/%s",
                \PayEx\Px::VERSION,
                phpversion(),
                VERSION,
                Payex::getVersion()
            ));
        }

        return self::$_px;
    }

    /**
     * Get Locale for PayEx
     * @param $lang
     * @return string
     */
    protected function getLocale($lang)
    {
        $allowedLangs = array(
            'en' => 'en-US',
            'sv' => 'sv-SE',
            'nb' => 'nb-NO',
            'da' => 'da-DK',
            'es' => 'es-ES',
            'de' => 'de-DE',
            'fi' => 'fi-FI',
            'fr' => 'fr-FR',
            'pl' => 'pl-PL',
            'cs' => 'cs-CZ',
            'hu' => 'hu-HU'
        );

        if (isset($allowedLangs[$lang])) {
            return $allowedLangs[$lang];
        }

        return 'en-US';
    }

    /**
     * Add Message to Log
     * @param $message
     */
    protected function log($message)
    {
        // @todo Debug log
    }
}