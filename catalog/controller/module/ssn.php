<?php
if (!defined('DIR_APPLICATION')) {
    die();
}

require_once DIR_SYSTEM . 'library/Px/Px.php';

class ControllerModuleSsn extends Controller {
    protected static $_px;

    public function index() {
        $ssn = $_POST['ssn'];

        // Call PxVerification.GetConsumerLegalAddress
        $params = array(
            'accountNumber' => '',
            'countryCode' => 'SE', // Supported only "SE"
            'socialSecurityNumber' => $ssn
        );
        $result = $this->getPx()->GetConsumerLegalAddress($params);
        if ($result['code'] !== 'OK' || $result['description'] !== 'OK' || $result['errorCode'] !== 'OK') {
            if (preg_match('/\bInvalid parameter:SocialSecurityNumber\b/i', $result['description'])) {
                $json = array(
                    'success' => 'false',
                    'message' => 'Invalid Social Security Number'
                );
                $this->response->setOutput(json_encode($json));
                return;
            }

            $json = array(
                'status' => 'error',
                'message' => $result['errorCode'] . ' (' . $result['description'] . ')'
            );
            $this->response->setOutput(json_encode($json));
            return;
        }

        $json = array(
            'success' => true,
            'first_name' => $result['firstName'],
            'last_name' => $result['lastName'],
            'address_1' => $result['address1'],
            'address_2' => $result['address2'],
            'postcode' => $result['postNumber'],
            'city' => $result['city'],
            'country' => $result['country']
        );

        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get PayEx Handler
     * @return Px_Px
     */
    protected function getPx()
    {
        if (is_null(self::$_px)) {
            $account_number = $this->config->get('ssn_payex_account_number');
            $encryption_key = $this->config->get('ssn_payex_encryption_key');
            $mode = $this->config->get('ssn_payex_mode');
            self::$_px = new Px_Px();
            self::$_px->setEnvironment($account_number, $encryption_key, ($mode !== 'LIVE'));
        }

        return self::$_px;
    }
}