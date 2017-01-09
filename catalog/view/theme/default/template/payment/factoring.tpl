<h2><?php echo $text_title; ?></h2>
<p><?php echo $text_description; ?></p>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
    <?php if ($type === 'SELECT'): ?>
        <p>
            <label for="factoring-menu"><?php echo $text_select_payment_method; ?></label>
            <select name="factoring-menu" id="factoring-menu" class="required-entry">
                <option selected value="FINANCING"><?php echo $text_financing_invoice; ?></option>
                <option value="CREDITACCOUNT"><?php echo $text_part_payment; ?></option>
            </select>
        </p>
    <?php endif; ?>
    <label for="social-security-number"><?php echo $text_social_security_number; ?></label>
    <input type="text" name="social-security-number" id="social-security-number" value="" autocomplete="off">
</form>
<div class="buttons">
    <div class="right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button"/>
    </div>
</div>
<script type="text/javascript">
    <!--
    $('#button-confirm').bind('click', function () {
		$(this).prop('disabled', true);
        $.ajax({
            url: '<?php echo html_entity_decode($action, ENT_QUOTES, 'UTF-8'); ?>',
            type: 'POST',
            cache: false,
            async: true,
            dataType: 'json',
            data: {
                'social-security-number': $('#social-security-number').val()
            },
			error:function(){
				$('#button-confirm').prop('disabled',false);
			},
            success: function (response) {
				$('#button-confirm').prop('disabled',false);
				if (response.status !== 'ok') {
                    if ($('#payex-error').is('*')) {
                        $('#payex-error').html(response.message);
                    } else {
                        $('<div id="payex-error" class="warning">' + response.message + '</div>').insertBefore('#form');
                    }
                    return false;
                }

                $(this).attr('disabled', 'disabled');
                $('#payex-error').remove();
                $('#form').get(0).setAttribute('action', response.redirect);
                setTimeout(function() {
                    $('#form').submit();
                }, 1000);
            }
        });
    });
    //-->
</script>
