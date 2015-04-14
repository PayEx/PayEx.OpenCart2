<?php
class ModelTotalFactoringFee extends Model {
    public function getTotal(&$total_data, &$total, &$taxes) {
        $this->load->language('total/factoring_fee');

        $factoring_fee = $this->config->get('factoring_fee_fee');
        $factoring_status = (bool)$this->config->get('factoring_fee_status');
        $factoring_total = (float)$this->config->get('factoring_fee_total');
        $factoring_sort_order = (int)$this->config->get('factoring_fee_sort_order');
        $factoring_fee_tax_class_id = (int)$this->config->get('factoring_fee_tax_class_id');

        $status = true;
        if (!isset($this->session->data['payment_method']['code']) || $this->session->data['payment_method']['code'] !== 'factoring') {
            $status = false;
        } elseif (!$factoring_status) {
            $status = false;
        } elseif (($factoring_total > 0) && ($factoring_total >= $this->cart->getSubTotal())) {
            $status = false;
        }

        if ($status) {
            $total_data[] = array(
                'code'       => 'factoring_fee',
                'title'      => $this->language->get('text_factoring_fee'),
                'text'       => $this->currency->format($factoring_fee),
                'value'      => $factoring_fee,
                'sort_order' => $factoring_sort_order
            );

            $tax_rates = $this->tax->getRates($factoring_fee, $factoring_fee_tax_class_id);

            foreach ($tax_rates as $tax_rate) {
                if (!isset($taxes[$tax_rate['tax_rate_id']])) {
                    $taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
                } else {
                    $taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
                }
            }

            $total += $factoring_fee;
        }
    }
}
