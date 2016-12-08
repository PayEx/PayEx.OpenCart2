<?php
if (!defined('DIR_APPLICATION')) {
    die();
}
require_once DIR_SYSTEM . '../vendor/payex/php-api/src/PayEx/Px.php';
require_once DIR_SYSTEM . 'Payex/Payex.php';
require_once DIR_SYSTEM . 'Payex/OcRoute.php';

class ControllerPaymentPayex extends Controller
{
    protected $_module_name = 'payex';

    protected static $_px;

    /** @var array PayEx TC Spider IPs */
    static protected $_allowed_ips = array(
        '82.115.146.170', // Production
        '82.115.146.10' // Test
    );

    /**
     * Index Action
     */
   public function index()
    {
        $this->language->load( OcRoute::getPaymentRoute('payment/') . 'payex');

        $data['text_title'] = $this->language->get('text_title');
        $data['text_description'] = $this->language->get('text_description');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success');
        $data['action'] = $this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/confirm');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payex.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/payex.tpl', $data);
        } else {
           return $this->load->view(OcRoute::getTemplate('payment/payex.tpl'), $data);
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

        $additional = $this->config->get('payex_paymentview') === 'PX' ? 'PAYMENTMENU=TRUE' : '';
        if ($this->config->get('payex_responsive')) {
            $separator = (!empty($additional) && mb_substr($additional, -1) !== '&') ? '&' : '';

            // PayEx Payment Page 2.0  works only for View 'Credit Card' and 'Direct Debit' at the moment
            if (in_array($this->config->get('payex_paymentview'), array('CREDITCARD', 'DIRECTDEBIT'))) {
                $additional .= $separator . 'RESPONSIVE=1';
            } else {
                $additional .= $separator . 'USECSS=RESPONSIVEDESIGN';
            }
        }

        $amount = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);

        // Call PxOrder.Initialize8
        $params = array(
            'accountNumber' => '',
            'purchaseOperation' => $this->config->get('payex_transactiontype'),
            'price' => round($amount * 100),
            'priceArgList' => '',
            'currency' => strtoupper($order['currency_code']),
            'vat' => 0,
            'orderID' => $order['order_id'],
            'productNumber' => $order['customer_id'],
            'description' => html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'),
            'clientIPAddress' => $order['ip'],
            'clientIdentifier' => 'USERAGENT=' . $order['user_agent'],
            'additionalValues' => $additional,
            'externalID' => '',
            'returnUrl' => $this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/success', '', 'SSL'),
            'view' => $this->config->get('payex_paymentview'),
            'agreementRef' => '',
            'cancelUrl' => $this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/cancel', '', 'SSL'),
            'clientLanguage' => $this->getLocale($this->language->get('code'))
        );
        $result = $this->getPx()->Initialize8($params);
        if ($result['code'] !== 'OK' || $result['description'] !== 'OK' || $result['errorCode'] !== 'OK') {
            $this->session->data['payex_error'] = $result['errorCode'] . ' (' . $result['description'] . ')';
            $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
        }
        $redirectUrl = $result['redirectUrl'];
        $orderRef = $result['orderRef'];

        if ($this->config->get('payex_checkout_info')) {
            // add Order Lines
            $i = 1;
            foreach ($this->cart->getProducts() as $product) {
                $qty = $product['quantity'];
                $price = $product['price'] * $qty;
                $priceWithTax = $this->tax->calculate($price, $product['tax_class_id'], 1);
                $taxPrice = $priceWithTax - $price;
                $taxPercent = ($taxPrice > 0) ? round(100 / (($priceWithTax - $taxPrice) / $taxPrice)) : 0;

                // Call PxOrder.AddSingleOrderLine2
                $params = array(
                    'accountNumber' => '',
                    'orderRef' => $orderRef,
                    'itemNumber' => $i,
                    'itemDescription1' => $product['name'],
                    'itemDescription2' => '',
                    'itemDescription3' => '',
                    'itemDescription4' => '',
                    'itemDescription5' => '',
                    'quantity' => $qty,
                    'amount' => (int)(100 * $priceWithTax), //must include tax
                    'vatPrice' => (int)(100 * round($taxPrice, 2)),
                    'vatPercent' => (int)(100 * $taxPercent)
                );
                $result = $this->getPx()->AddSingleOrderLine2($params);
                if ($result['code'] !== 'OK' || $result['description'] !== 'OK' || $result['errorCode'] !== 'OK') {
                    $this->session->data['payex_error'] = $result['errorCode'] . ' (' . $result['description'] . ')';
                    $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
                }

                $i++;
            }

            // Add Shipping Line
            $shipping_method = $this->session->data['shipping_method'];
            if (isset($shipping_method['cost']) && $shipping_method['cost'] > 0) {
                $shipping = $shipping_method['cost'];
                $shippingWithTax = $this->tax->calculate($shipping, $shipping_method['tax_class_id'], 1);
                $shippingTax = $shippingWithTax - $shipping;
                $shippingTaxPercent = $shipping != 0 ? (int)((100 * ($shippingTax) / $shipping)) : 0;

                // Call PxOrder.AddSingleOrderLine2
                $params = array(
                    'accountNumber' => '',
                    'orderRef' => $orderRef,
                    'itemNumber' => $i,
                    'itemDescription1' => $shipping_method['title'],
                    'itemDescription2' => '',
                    'itemDescription3' => '',
                    'itemDescription4' => '',
                    'itemDescription5' => '',
                    'quantity' => 1,
                    'amount' => (int)(100 * $shippingWithTax), //must include tax
                    'vatPrice' => (int)(100 * round($shippingTax, 2)),
                    'vatPercent' => (int)(100 * $shippingTaxPercent)
                );
                $result = $this->getPx()->AddSingleOrderLine2($params);
                if ($result['code'] !== 'OK' || $result['description'] !== 'OK' || $result['errorCode'] !== 'OK') {
                    $this->session->data['payex_error'] = $result['errorCode'] . ' (' . $result['description'] . ')';
                    $this->response->redirect($this->url->link( OcRoute::getPaymentRoute('payment/') . '' . $this->_module_name . '/error', '', 'SSL'));
                }

                $i++;
            }

            // Add Order Address
            // Call PxOrder.AddOrderAddress2
            $params = array(
                'accountNumber' => '',
                'orderRef' => $orderRef,
                'billingFirstName' => $order['payment_firstname'],
                'billingLastName' => $order['payment_lastname'],
                'billingAddress1' => $order['payment_address_1'],
                'billingAddress2' => $order['payment_address_2'],
                'billingAddress3' => '',
                'billingPostNumber' => $order['payment_postcode'],
                'billingCity' => $order['payment_city'],
                'billingState' => $order['payment_zone'],
                'billingCountry' => $order['payment_country'],
                'billingCountryCode' => $order['payment_iso_code_2'],
                'billingEmail' => $order['email'],
                'billingPhone' => $order['telephone'],
                'billingGsm' => '',
            );

            $shipping_params = array(
                'deliveryFirstName' => '',
                'deliveryLastName' => '',
                'deliveryAddress1' => '',
                'deliveryAddress2' => '',
                'deliveryAddress3' => '',
                'deliveryPostNumber' => '',
                'deliveryCity' => '',
                'deliveryState' => '',
                'deliveryCountry' => '',
                'deliveryCountryCode' => '',
                'deliveryEmail' => '',
                'deliveryPhone' => '',
                'deliveryGsm' => '',
            );

            if (isset($shipping_method['cost']) && $shipping_method['cost'] > 0) {
                $shipping_params = array(
                    'deliveryFirstName' => $order['shipping_firstname'],
                    'deliveryLastName' => $order['shipping_lastname'],
                    'deliveryAddress1' => $order['shipping_address_1'],
                    'deliveryAddress2' => $order['shipping_address_2'],
                    'deliveryAddress3' => '',
                    'deliveryPostNumber' => $order['shipping_postcode'],
                    'deliveryCity' => $order['shipping_city'],
                    'deliveryState' => $order['shipping_zone'],
                    'deliveryCountry' => $order['shipping_country'],
                    'deliveryCountryCode' => $order['shipping_iso_code_2'],
                    'deliveryEmail' => $order['email'],
                    'deliveryPhone' => $order['telephone'],
                    'deliveryGsm' => '',
                );
            }

            $params += $shipping_params;

            $result = $this->getPx()->AddOrderAddress2($params);
            if ($result['code'] !== 'OK' || $result['description'] !== 'OK' || $result['errorCode'] !== 'OK') {
                // @todo Error $result['errorCode'] . ' (' . $result['description'] . ')'
                exit('Error: ' . $result['errorCode'] . ' (' . $result['description'] . ')');
            }
        }

        $this->response->redirect($redirectUrl);
    }

    /**
     * Success Action
     */
    public function success()
    {
        $this->load->language( OcRoute::getPaymentRoute('payment/') . 'payex_error');
        $this->load->model('checkout/order');
        $this->load->model('module/payex');

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
                $this->model_checkout_order->addOrderHistory($result['orderId'], $this->config->get('payex_failed_status_id'), $message, true);
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
        $this->model_module_payex->addTransaction($order_id, $result['transactionNumber'], $transaction_status, $result, isset($result['date']) ? strtotime($result['date']) : time());

        /* Transaction statuses:
        0=Sale, 1=Initialize, 2=Credit, 3=Authorize, 4=Cancel, 5=Failure, 6=Capture */
        switch ($transaction_status) {
            case 0:
            case 6:
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_completed_status_id'), '', true);
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
                break;
            case 1:
            case 3:
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_pending_status_id'), '', true);
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
                break;
            case 4:
                // Cancel
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_canceled_status_id'), '', true);
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

                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_failed_status_id'), $message, true);
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
     * Transaction Action
     * Used for Transaction Callback support
     */
    public function transaction()
    {
        $this->load->model('checkout/order');
        $this->load->model('module/payex');

        // Check is PayEx Request
        if (!in_array($this->request->server['REMOTE_ADDR'], self::$_allowed_ips)) {
            $this->log('TC: Access denied for this request. It\'s not PayEx Spider.');
            header(sprintf('%s %s %s', 'HTTP/1.1', '403', 'Access denied. Accept PayEx Transaction Callback only.'), true, '403');
            header(sprintf('Status: %s %s', '403', 'Access denied. Accept PayEx Transaction Callback only.'), true, '403');
            exit('Error: Access denied. Accept PayEx Transaction Callback only. ');
        }

        // Check Post Fields
        $this->log('TC: Requested Params: ' . var_export($_POST, true));
        if (count($_POST) == 0 || empty($_POST['transactionNumber'])) {
            $this->log('TC: Error: Empty request received.');
            header(sprintf('%s %s %s', 'HTTP/1.1', '500', 'FAILURE'), true, '500');
            header(sprintf('Status: %s %s', '500', 'FAILURE'), true, '500');
            exit('FAILURE');
        }

        // Get Transaction Details
        $transactionId = $this->request->post['transactionNumber'];

        // Call PxOrder.GetTransactionDetails2
        $params = array(
            'accountNumber' => '',
            'transactionNumber' => $transactionId
        );
        $details = $this->getPx()->GetTransactionDetails2($params);
        if ($details['code'] !== 'OK' || $details['description'] !== 'OK' || $details['errorCode'] !== 'OK') {
            exit('Error: ' . $details['errorCode'] . ' (' . $details['description'] . ')');
        }

        $order_id = $details['orderId'];
        $transactionStatus = (int)$details['transactionStatus'];

        $this->log('TC: Incoming transaction: ' . $transactionId);
        $this->log('TC: Transaction Status: ' . $transactionStatus);
        $this->log('TC: OrderId: ' . $order_id);

        // @todo Check orderID in Store

        /* 0=Sale, 1=Initialize, 2=Credit, 3=Authorize, 4=Cancel, 5=Failure, 6=Capture */
        switch ($transactionStatus) {
            case 0;
            case 1;
            case 3:
                // Complete order
                $params = array(
                    'accountNumber' => '',
                    'orderRef' => $this->request->post['orderRef']
                );
                $result = $this->getPx()->Complete($params);
                if ($result['errorCodeSimple'] !== 'OK') {
                    exit('Error: ' . $details['errorCode'] . ' (' . $details['description'] . ')');
                }

                // Save Transaction
                $this->model_module_payex->addTransaction($order_id, $result['transactionNumber'], $result['transactionStatus'], $result, isset($result['date']) ? strtotime($result['date']) : time());

                switch ((int)$result['transactionStatus']) {
                    case 0:
                    case 6:
                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_completed_status_id'), '', false);
                        break;
                    case 1:
                    case 3:
                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_pending_status_id'), '', false);
                        break;
                    case 4:
                        // Cancel
                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_canceled_status_id'), '', false);
                        break;
                    case 5:
                    default:
                        // Cancel when Errors
                        $error_code = $result['transactionErrorCode'];
                        $error_description = $result['transactionThirdPartyError'];
                        if (empty($error_code) && empty($error_description)) {
                            $error_code = $result['code'];
                            $error_description = $result['description'];
                        }
                        $message = $error_code . ' (' . $error_description . ')';
                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_failed_status_id'), $message, true);
                        break;
                }

                $this->log('TC: OrderId ' . $order_id . ' Complete with TransactionStatus ' . $result['transactionStatus'], $order_id);
                break;
            case 2:
                // Refund
                // Save Transaction
                $this->model_module_payex->addTransaction($order_id, $transactionId, $details['transactionStatus'], $details, isset($details['orderCreated']) ? strtotime($details['orderCreated']) : time());

                // Set Order Status
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_completed_status_id'), '', false);

                //@todo Re-stock Items when Refund?
                $this->log('TC: OrderId ' . $order_id . ' refunded', $order_id);
                break;
            case 4;
                // Cancel
                // Save Transaction
                $this->model_module_payex->addTransaction($order_id, $transactionId, $details['transactionStatus'], $details, isset($details['orderCreated']) ? strtotime($details['orderCreated']) : time());

                // Set Order Status
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_canceled_status_id'), '', false);

                $this->log('TC: OrderId ' . $order_id . ' canceled', $order_id);
                break;
            case 5:
                 // Cancel when Errors
                // Save Transaction
                $this->model_module_payex->addTransaction($order_id, $transactionId, $details['transactionStatus'], $details, isset($details['orderCreated']) ? strtotime($details['orderCreated']) : time());

                // Set Order Status
                $error_code = $details['transactionErrorCode'];
                $error_description = $details['transactionThirdPartyError'];
                if (empty($error_code) && empty($error_description)) {
                    $error_code = $details['code'];
                    $error_description = $details['description'];
                }
                $message = $error_code . ' (' . $error_description . ')';
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_failed_status_id'), $message, true);

                $this->log('TC: OrderId ' . $order_id . ' canceled', $order_id);
                break;
            case 6:
                // Set Order Status to captured
                // Save Transaction
                $this->model_module_payex->addTransaction($order_id, $transactionId, $details['transactionStatus'], $details, isset($details['orderCreated']) ? strtotime($details['orderCreated']) : time());

                // Set Order Status
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payex_completed_status_id'), '', false);

                $this->log('TC: OrderId ' . $order_id . ' captured', $order_id);
                break;
            default:
                $this->log('TC: Unknown Transaction Status', $order_id);
                header(sprintf('%s %s %s', 'HTTP/1.1', '500', 'FAILURE'), true, '500');
                header(sprintf('Status: %s %s', '500', 'FAILURE'), true, '500');
                exit('FAILURE');
        }

        // Show "OK"
        $this->log('TC: Done.');
        header(sprintf('%s %s %s', 'HTTP/1.1', '200', 'OK'), true, '200');
        header(sprintf('Status: %s %s', '200', 'OK'), true, '200');
        exit('OK');
    }

    /**
     * Get PayEx Handler
     * @return \PayEx\Px
     */
    protected function getPx()
    {
        if (is_null(self::$_px)) {
            $account_number = $this->config->get('payex_account_number');
            $encryption_key = $this->config->get('payex_encryption_key');
            $mode = $this->config->get('payex_mode');
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