<?php
if (!defined('DIR_APPLICATION')) {
    die();
}

class ModelModulePayex extends Model
{
    /**
     * Create Database Table
     * @return mixed
     */
    public function createModuleTables()
    {
        return $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "payex_transactions` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `order_id` int(11) DEFAULT NULL COMMENT 'Order Id',
            `transaction_id` int(11) DEFAULT NULL COMMENT 'PayEx Transaction Id',
            `transaction_status` int(11) DEFAULT NULL COMMENT 'PayEx Transaction Status',
            `transaction_data` text COMMENT 'PayEx Transaction Data',
            `date` datetime DEFAULT NULL COMMENT 'PayEx Transaction Date',
            `is_captured` tinyint(4) DEFAULT '0' COMMENT 'Is Captured',
            `is_canceled` tinyint(4) DEFAULT '0' COMMENT 'Is Canceled',
            `is_refunded` tinyint(4) DEFAULT '0' COMMENT 'Is Refunded',
            `total_refunded` float DEFAULT '0' COMMENT 'Refund Amount',
            PRIMARY KEY (`id`),
            UNIQUE KEY `transaction_id` (`transaction_id`),
            KEY `order_id` (`order_id`),
            KEY `transaction_status` (`transaction_status`),
            KEY `date` (`date`)
            ) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ");
    }

    /**
     * Save Transaction in PayEx Table
     * @param $order_id
     * @param $transaction_id
     * @param $transaction_status
     * @param $transaction_data
     * @param null $date
     * @return bool
     */
    public function addTransaction($order_id, $transaction_id, $transaction_status, $transaction_data, $date = null)
    {
        $query = sprintf('INSERT INTO `' . DB_PREFIX . 'payex_transactions` (order_id, transaction_id, transaction_status, transaction_data, date) VALUES (%d, %d, %d, "%s", "%s");',
            $this->db->escape((int)$order_id),
            $this->db->escape((int)$transaction_id),
            $this->db->escape((int)$transaction_status),
            $this->db->escape(serialize($transaction_data)),
            date('Y-m-d H:i:s', $date)
        );

        try {
            return $this->db->query($query);
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Set Transaction as Captured
     * @param $transaction_id
     * @return bool
     */
    public function setAsCaptured($transaction_id)
    {
        $query = sprintf('UPDATE `' . DB_PREFIX . 'payex_transactions` SET is_captured = 1 WHERE transaction_id = %d;',
            $this->db->escape((int)$transaction_id)
        );
        try {
            return $this->db->query($query);
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Set Transaction as Canceled
     * @param $transaction_id
     * @return bool
     */
    public function setAsCanceled($transaction_id)
    {
        $query = sprintf('UPDATE `' . DB_PREFIX . 'payex_transactions` SET is_canceled = 1 WHERE transaction_id = %d;',
            $this->db->escape((int)$transaction_id)
        );
        try {
            return $this->db->query($query);
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Set Transaction as Refunded
     * @param $transaction_id
     * @param $total_refunded
     * @return bool
     */
    public function setAsRefunded($transaction_id, $total_refunded)
    {
        $query = sprintf('UPDATE `' . DB_PREFIX . 'payex_transactions` SET is_refunded = 1, total_refunded = %d WHERE transaction_id = %s;',
            $this->db->escape((int)$total_refunded),
            $this->db->escape((int)$transaction_id)
        );
        try {
            return $this->db->query($query);
        } catch(Exception $e) {
            return false;
        }
    }

	/**
	 * Get Product Lines
	 * @param int $order_id
	 * @param array $products
	 * @param array $shipping_method
	 *
	 * @return array
	 */
	public function getProductItems($order_id, $products, $shipping_method) {
		$order = $this->model_checkout_order->getOrder($order_id);

		$lines = array();
		$averageTax = array();
		foreach ($products as $key => $product) {
			$qty = $product['quantity'];
			$price = $this->currency->format($product['price'] * $qty, $order['currency_code'], $order['currency_value'], false);
			$priceWithTax = $this->tax->calculate($price, $product['tax_class_id'], 1);
			$taxPrice = $priceWithTax - $price;
			$taxPercent = ($taxPrice > 0) ? round(100 / (($priceWithTax - $taxPrice) / $taxPrice)) : 0;
			$averageTax[] = $taxPercent;

			$lines[] = array(
				'type' => 'product',
				'name' => $product['name'],
				'qty' => $qty,
				'price_with_tax' => sprintf("%.2f", $priceWithTax),
				'price_without_tax' => sprintf("%.2f", $price),
				'tax_price' => sprintf("%.2f", $taxPrice),
				'tax_percent' => sprintf("%.2f", $taxPercent)
			);
		}

		// Add Shipping Line
		if (isset($shipping_method['cost']) && (float)$shipping_method['cost'] > 0) {
			$shipping = $this->currency->format($shipping_method['cost'], $order['currency_code'], $order['currency_value'], false);
			$shippingWithTax = $this->tax->calculate($shipping, $shipping_method['tax_class_id'], 1);
			$shippingTax = $shippingWithTax - $shipping;
			$shippingTaxPercent = $shipping != 0 ? (int)((100 * ($shippingTax) / $shipping)) : 0;
			$averageTax[] = $shippingTaxPercent;

			$lines[] = array(
				'type' => 'shipping',
				'name' => $shipping_method['title'],
				'qty' => 1,
				'price_with_tax' => sprintf("%.2f", $shippingWithTax),
				'price_without_tax' => sprintf("%.2f", $shipping),
				'tax_price' => sprintf("%.2f", $shippingTax),
				'tax_percent' => sprintf("%.2f", $shippingTaxPercent)
			);
		}

		// Add Coupon Line
		$order_info = $this->model_checkout_order->getOrder($order_id);
		$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE code = 'coupon'  AND order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
		if ($order_total_query && $order_total_query->rows > 0) {
			$coupon = array_shift($order_total_query->rows);
			$coupon['value'] = $this->currency->format($coupon['value'], $order_info['currency_code'], $order_info['currency_value'], false);

			if (abs($coupon['value']) > 0) {
				// Use average tax as discount tax for workaround
				$couponTaxPercent = round(array_sum($averageTax) / count($averageTax));
				$couponTax = round($coupon['value'] / 100 * $couponTaxPercent, 2);
				$couponWithTax = $coupon['value'] + $couponTax;

				$lines[] = array(
					'type' => 'discount',
					'name' => $coupon['title'],
					'qty' => 1,
					'price_with_tax' => sprintf("%.2f", $couponWithTax),
					'price_without_tax' => sprintf("%.2f", $coupon['value']),
					'tax_price' => sprintf("%.2f", $couponTax),
					'tax_percent' => sprintf("%.2f", $couponTaxPercent)
				);
			}
		}

		// Add payment fee for Factoring
		if ($order['payment_code'] === 'factoring' && $this->config->get('factoring_fee_fee') > 0) {
			$fee = (float)$this->config->get('factoring_fee_fee');
			$fee_tax_class_id = (int)$this->config->get('factoring_fee_tax_class_id');
			$feeWithTax = $this->tax->calculate($fee, $fee_tax_class_id, 1);
			$feeTax = $feeWithTax - $fee;
			$feeTaxPercent = $fee != 0 ? (int)((100 * ($feeTax) / $fee)) : 0;

			$lines[] = array(
				'type' => 'fee',
				'name' => $this->language->get('text_factoring_fee'),
				'qty' => 1,
				'price_with_tax' => sprintf("%.2f", $feeWithTax),
				'price_without_tax' => sprintf("%.2f", $fee),
				'tax_price' => sprintf("%.2f", $feeTax),
				'tax_percent' => sprintf("%.2f", $feeTaxPercent)
			);
		}

		return $lines;
	}
}