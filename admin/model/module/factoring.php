<?php
if (!defined('DIR_APPLICATION')) {
    die();
}

class ModelModuleFactoring extends Model
{
    /**
     * Create Database Table
     * @return mixed
     */
    public function createModuleTables()
    {
        return $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "factoring_transactions` (
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
        $query = sprintf('INSERT INTO `' . DB_PREFIX . 'factoring_transactions` (order_id, transaction_id, transaction_status, transaction_data, date) VALUES (%d, %d, %d, "%s", "%s");',
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
        $query = sprintf('UPDATE `' . DB_PREFIX . 'factoring_transactions` SET is_captured = 1 WHERE transaction_id = %d;',
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
        $query = sprintf('UPDATE `' . DB_PREFIX . 'factoring_transactions` SET is_canceled = 1 WHERE transaction_id = %d;',
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
        $query = sprintf('UPDATE `' . DB_PREFIX . 'factoring_transactions` SET is_refunded = 1, total_refunded = %d WHERE transaction_id = %s;',
            $this->db->escape((int)$total_refunded),
            $this->db->escape((int)$transaction_id)
        );
        try {
            return $this->db->query($query);
        } catch(Exception $e) {
            return false;
        }
    }
}