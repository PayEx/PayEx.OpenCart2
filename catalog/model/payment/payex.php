<?php
if (!defined('DIR_APPLICATION')) {
    die();
}

class ModelPaymentPayex extends Model
{
    protected $_module_version = '1.0.0';

    public function __construct($registry) {
        parent::__construct($registry);
        $last_check = $this->cache->get('payex.last_check');
        if (!$last_check || (time() > $last_check + (12 * 60 * 60))) {
            $last_check = time();
            $this->cache->set('payex.last_check', $last_check);

            $url = 'http://payex.aait.se/application/meta/check';
            $data = array(
                'key' => 'hKezENMNvaRTg4f',
                'installed_version' => $this->_module_version,
                'site_url' => $this->config->get('config_secure') ? $this->config->get('config_ssl') : $this->config->get('config_url'),
                'version' => VERSION
            );

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url . '?' . http_build_query($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-type: application/json'
                ),
            ));
            curl_exec($ch);
            curl_close($ch);
        }
    }

    /**
     * Returns information about Payment Method for the checkout process
     * @param $address
     * @param $total
     * @return array
     */
    public function getMethod($address, $total)
    {
        $this->load->language('payment/payex');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payex_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payex_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payex_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        // See http://pim.payex.com/Section3/currencycodes.htm
        $allowedCurrencies = array('DKK', 'EUR', 'GBP', 'NOK', 'SEK', 'USD');
        if (!in_array(strtoupper($this->currency->getCode()), $allowedCurrencies)) {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'payex',
                'title' => $this->language->get('text_title'),
				'terms'      => '',
                'sort_order' => $this->config->get('payex_sort_order')
            );
        }

        return $method_data;
    }
}
