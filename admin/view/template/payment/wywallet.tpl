<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
	<div class="container-fluid">
	<h1><img src="view/image/payment/wywallet.gif" alt=""/> <?php echo $heading_title; ?></h1>
 
 <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb): ?>
     <li>   <?php echo $breadcrumb['separator']; ?>
        <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php endforeach; ?> </li>
		</ul>
 <div class="pull-right">
	 <a onclick="$('#form').submit();" class="btn btn-primary" ><?php echo $button_save; ?></a><a
                        onclick="location = '<?php echo $cancel; ?>';" class="btn btn-default"><?php echo $button_cancel; ?></a></a>
       </div>     
	</div>
    </div>
          
<div class="container-fluid">


    <?php foreach ($error as $item) : ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $item; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endforeach; ?>
<div class="panel panel-default">
			<div class="panel-body">

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

<ul id="tabs" class="nav nav-tabs">
   <li class="active"> <a href="#tab-settings" data-toggle="tab"><?php echo $text_settings; ?></a>
	</li><li>
    <?php if (count($transactions) > 0): ?>
    <a href="#tab-transactions" data-toggle="tab"><?php echo $text_transactions; ?></a>
    <?php endif; ?>
	</li><li>
    <?php if (count($pending_transactions) > 0): ?>
    <a href="#tab-pending-transactions"  data-toggle="tab"><?php echo $text_pending_transactions; ?></a>
    <?php endif; ?>
	</li>
	</ul>
<div class="tab-content">
<div id="tab-settings" class="tab-pane active">
    <table class="table table-bordered table-hover">
        <tr>
            <td>
                <label for="wywallet_account_number"><?php echo $text_account_number ?></label>
            </td>
            <td>
                <input type="text" name="wywallet_account_number" id="wywallet_account_number"
                       value="<?php echo $wywallet_account_number; ?>" autocomplete="off">
            </td>
        </tr>
        <tr>
            <td>
                <label for="wywallet_encryption_key"><?php echo $text_encryption_key ?></label>
            </td>
            <td>
                <input type="password" name="wywallet_encryption_key" id="wywallet_encryption_key"
                       value="<?php echo $wywallet_encryption_key; ?>" autocomplete="off">
            </td>
        </tr>
        <tr>
            <td>
                <label for="wywallet_mode"><?php echo $text_mode ?></label>
            </td>
            <td>
                <select name="wywallet_mode" id="wywallet_mode">
                    <option
                    <?php if ($wywallet_mode === 'LIVE') { echo 'selected="selected" '; } ?> value="LIVE">Live</option>
                    <option
                    <?php if ($wywallet_mode === 'TEST') { echo 'selected="selected" '; } ?> value="TEST">Test</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <label for="wywallet_transactiontype"><?php echo $text_transactiontype ?></label>
            </td>
            <td>
                <select name="wywallet_transactiontype" id="wywallet_transactiontype">
                    <option
                    <?php if ($wywallet_transactiontype === 'AUTHORIZATION') { echo 'selected="selected" '; } ?>
                    value="AUTHORIZATION">AUTHORIZATION</option>
                    <option
                    <?php if ($wywallet_transactiontype === 'SALE') { echo 'selected="selected" '; } ?>
                    value="SALE">SALE</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $text_responsive; ?></td>
            <td><select name="wywallet_responsive">
                    <?php if ($wywallet_responsive): ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php else: ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php endif; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_checkout_info; ?></td>
            <td><select name="wywallet_checkout_info">
                    <?php if ($wywallet_checkout_info): ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php else: ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php endif; ?>
                </select></td>
        </tr>
		<tr>
			<td><?php echo $text_client_language; ?></td>
			<td>
				<select name="wywallet_client_language">
					<option value="" <?php echo $wywallet_client_language === '' ? 'selected="selected"' : ''?>>Detect automatically</option>
					<option value="en-US" <?php echo $wywallet_client_language === 'en-US' ? 'selected="selected"' : ''?>>English</option>
					<option value="sv-SE" <?php echo $wywallet_client_language === 'sv-SE' ? 'selected="selected"' : ''?>>Swedish</option>
					<option value="nb-NO" <?php echo $wywallet_client_language === 'nb-NO' ? 'selected="selected"' : ''?>>Norway</option>
					<option value="da-DK" <?php echo $wywallet_client_language === 'da-DK' ? 'selected="selected"' : ''?>>Danish</option>
					<option value="es-ES" <?php echo $wywallet_client_language === 'es-ES' ? 'selected="selected"' : ''?>>Spanish</option>
					<option value="de-DE" <?php echo $wywallet_client_language === 'de-DE' ? 'selected="selected"' : ''?>>German</option>
					<option value="fi-FI" <?php echo $wywallet_client_language === 'fi-FI' ? 'selected="selected"' : ''?>>Finnish</option>
					<option value="fr-FR" <?php echo $wywallet_client_language === 'fr-FR' ? 'selected="selected"' : ''?>>French</option>
					<option value="pl-PL" <?php echo $wywallet_client_language === 'pl-PL' ? 'selected="selected"' : ''?>>Polish</option>
					<option value="cs-CZ" <?php echo $wywallet_client_language === 'cs-CZ' ? 'selected="selected"' : ''?>>Czech</option>
					<option value="hu-HU" <?php echo $wywallet_client_language === 'hu-HU' ? 'selected="selected"' : ''?>>Hungarian</option>
				</select>
			</td>
		</tr>
        <tr>
            <td><?php echo $text_total; ?></td>
            <td><input type="text" name="wywallet_total" value="<?php echo $wywallet_total; ?>"/></td>
        </tr>
        <tr>
            <td><?php echo $text_complete_status; ?></td>
            <td><select name="wywallet_completed_status_id">
                    <?php foreach ($order_statuses as $order_status): ?>
                    <?php if ($order_status['order_status_id'] == $wywallet_completed_status_id): ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"
                            selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_pending_status; ?></td>
            <td><select name="wywallet_pending_status_id">
                    <?php foreach ($order_statuses as $order_status): ?>
                    <?php if ($order_status['order_status_id'] == $wywallet_pending_status_id): ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"
                            selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_canceled_status; ?></td>
            <td><select name="wywallet_canceled_status_id">
                    <?php foreach ($order_statuses as $order_status): ?>
                    <?php if ($order_status['order_status_id'] == $wywallet_canceled_status_id): ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"
                            selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_failed_status; ?></td>
            <td><select name="wywallet_failed_status_id">
                    <?php foreach ($order_statuses as $order_status): ?>
                    <?php if ($order_status['order_status_id'] == $wywallet_failed_status_id): ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"
                            selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_refunded_status; ?></td>
            <td><select name="wywallet_refunded_status_id">
                    <?php foreach ($order_statuses as $order_status): ?>
                    <?php if ($order_status['order_status_id'] == $wywallet_refunded_status_id): ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"
                            selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_geo_zone; ?></td>
            <td><select name="wywallet_geo_zone_id">
                    <option value="0"><?php echo $text_all_zones; ?></option>
                    <?php foreach ($geo_zones as $geo_zone): ?>
                    <?php if ($geo_zone['geo_zone_id'] == $wywallet_geo_zone_id): ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                            selected="selected"><?php echo $geo_zone['name']; ?></option>
                    <?php else: ?>
                    <option
                            value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_status; ?></td>
            <td><select name="wywallet_status">
                    <?php if ($wywallet_status): ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php else: ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php endif; ?>
                </select></td>
        </tr>
        <tr>
            <td><?php echo $text_sort_order; ?></td>
            <td><input type="text" name="wywallet_sort_order" value="<?php echo $wywallet_sort_order; ?>"
                       size="1"/></td>
        </tr>
    </table>
</div>

<?php if (count($transactions) > 0): ?>
<div id="tab-transactions" class="tab-pane">
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th><?php echo $text_order_id; ?></th>
            <th><?php echo $text_transaction_id; ?></th>
            <th><?php echo $text_date; ?></th>
            <th><?php echo $text_transaction_status; ?></th>
            <th><?php echo $text_actions; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transactions as $transaction): ?>
        <tr style="text-align: center;">
            <td><?php echo $transaction['order_id']; ?></td>
            <td><?php echo $transaction['transaction_id']; ?></td>
            <td><?php echo $transaction['date']; ?></td>
            <td><?php echo $transaction['transaction_status']; ?></td>
            <td>
                <?php if ($transaction['transaction_status'] == 3 && $transaction['is_captured'] == 0 && $transaction['is_canceled'] == 0): ?>
                <input type="button" class="capture_button" name="capture_button"
                       value="<?php echo $text_capture; ?>" data-order-id="<?php echo $transaction['order_id']; ?>"
                       data-transaction-id="<?php echo $transaction['transaction_id']; ?>">
                <?php endif; ?>
                <?php if ($transaction['transaction_status'] == 3 && $transaction['is_captured'] == 0 && $transaction['is_canceled'] == 0): ?>
                <input type="button" class="cancel_button" name="cancel_button"
                       value="<?php echo $text_cancel; ?>" data-order-id="<?php echo $transaction['order_id']; ?>"
                       data-transaction-id="<?php echo $transaction['transaction_id']; ?>">
                <?php endif; ?>
                <?php if (($transaction['transaction_status'] == 0 || $transaction['transaction_status'] == 6) && $transaction['is_refunded'] == 0): ?>
                <input type="button" class="refund_button" name="refund_button"
                       value="<?php echo $text_refund; ?>" data-order-id="<?php echo $transaction['order_id']; ?>"
                       data-transaction-id="<?php echo $transaction['transaction_id']; ?>">
                <?php endif; ?>
                <?php if($transaction['is_captured'] != 0): ?>
                <input type="button" class="capture_button" name="capture_button"
                       value="<?php echo $text_captured; ?>" disabled="disabled">
                <?php endif; ?>
                <?php if($transaction['is_canceled'] != 0): ?>
                <input type="button" class="cancel_button" name="cancel_button"
                       value="<?php echo $text_canceled; ?>" disabled="disabled">
                <?php endif; ?>
                <?php if($transaction['is_refunded'] != 0): ?>
                <input type="button" class="refund_button" name="refund_button"
                       value="<?php echo $text_refunded; ?><?php echo ' (' . $transaction['total_refunded'] . ')'; ?>" disabled="disabled">
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (count($pending_transactions) > 0): ?>
<div id="tab-pending-transactions" class="tab-pane">
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th><?php echo $text_order_id; ?></th>
            <th><?php echo $text_transaction_id; ?></th>
            <th><?php echo $text_date; ?></th>
            <th><?php echo $text_transaction_status; ?></th>
            <th><?php echo $text_actions; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pending_transactions as $transaction): ?>
        <tr style="text-align: center;">
            <td><a href="<?php echo $transaction['order_link']; ?>"><?php echo $transaction['order_id']; ?></a></td>
            <td><?php echo $transaction['transaction_id']; ?></td>
            <td><?php echo $transaction['date']; ?></td>
            <td><?php echo $transaction['transaction_status']; ?></td>
            <td>
                <?php if ($transaction['transaction_status'] == 3): ?>
                <input type="button" class="capture_button" name="capture_button"
                       value="<?php echo $text_capture; ?>" data-order-id="<?php echo $transaction['order_id']; ?>"
                       data-transaction-id="<?php echo $transaction['transaction_id']; ?>">
                <input type="button" class="cancel_button" name="cancel_button"
                       value="<?php echo $text_cancel; ?>" data-order-id="<?php echo $transaction['order_id']; ?>"
                       data-transaction-id="<?php echo $transaction['transaction_id']; ?>">
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

</div>
</form>
</div>
</div>
</div>
<script type="text/javascript"><!--
  $('#tabs a:first').tab('show');

    $('.capture_button').on('click', function (e) {
        call_wywallet_action(this, 'capture');
    });

    $('.cancel_button').on('click', function (e) {
        call_wywallet_action(this, 'cancel');
    });

    $('.refund_button').on('click', function (e) {
        var total_refunded = prompt('Enter refund amount:', '0');
        if (parseInt(total_refunded) > 0) {
            call_wywallet_action_refund(this, total_refunded);
        }
    });

    function call_wywallet_action(el, action) {
        var order_id = $(el).data('order-id');
        var transaction_id = $(el).data('transaction-id');
        var current_label = $(el).val();
        $(el).attr('disabled', 'disabled');
        $(el).val('<?php echo $text_wait; ?>');

        $.ajax({
            url: '<?php echo html_entity_decode($action, ENT_QUOTES, 'UTF-8'); ?>',
            type: 'POST',
            cache: false,
            async: true,
            dataType: 'json',
            data: {
                action: action,
                order_id: order_id,
                transaction_id: transaction_id
            },
            success: function (response) {
                if (response.status !== 'ok') {
                    alert('Error: ' + response.message);
                    $(el).removeAttr('disabled');
                    $(el).val(current_label);
                    return false;
                }
                $(el).val(response.label);
            }
        });
    }

    function call_wywallet_action_refund(el, total_refunded) {
        var order_id = $(el).data('order-id');
        var transaction_id = $(el).data('transaction-id');
        var current_label = $(el).val();
        $(el).attr('disabled', 'disabled');
        $(el).val('<?php echo $text_wait; ?>');

        $.ajax({
            url: '<?php echo html_entity_decode($action, ENT_QUOTES, 'UTF-8'); ?>',
            type: 'POST',
            cache: false,
            async: true,
            dataType: 'json',
            data: {
                action: 'refund',
                order_id: order_id,
                transaction_id: transaction_id,
                total_refunded: total_refunded
            },
            success: function (response) {
                if (response.status !== 'ok') {
                    alert('Error: ' + response.message);
                    $(el).removeAttr('disabled');
                    $(el).val(current_label);
                    return false;
                }
                $(el).val(response.label);
            }
        });
    }

    //--></script>
<?php echo $footer ?>