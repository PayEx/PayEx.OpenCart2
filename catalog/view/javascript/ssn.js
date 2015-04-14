//Start SSN Block
/**
 * Social Security Number Module
 */
$(document).ready(function () {
    // Attach field to form
    $('body').bind('DOMNodeInserted DOMNodeRemoved', function(event) {
        if ($('#payment-new input[name="firstname"]').length && !$('#payment-new input[name="social_security_number"]').length) {
            var fields = '<tr><td>Social Security Number:</td><td><input type="text" name="social_security_number" id="social_security_number" placeholder="Social Security Number" class="large-field"><input type="button" name="getAddress" value="Get Address" class="button" style="width: 100px; margin-left: 10px;"></td></tr>';
            $(fields).insertBefore(jQuery('#payment-new input[name="firstname"]').closest('tr'));
        }
        setTimeout(function() {
            if ($('#payment-address input[name="firstname"]').length && !$('input[name="social_security_number"]').length) {
                var fields = '<span>Social Security Number:</span><br /><input type="text" name="social_security_number" id="social_security_number" placeholder="Social Security Number" class="large-field"><input type="button" name="getAddress" value="Get Address" class="button" style="width: 100px; margin-left: 10px;"><br />';
                $(fields).insertAfter(jQuery('#payment-address h2').first());
            }
        }, 100);
    });

    $(document).on('click', "input[name='getAddress']", function() {
        $.ajax({
            url: 'index.php?route=module/ssn',
            type: 'POST',
            cache: false,
            async: true,
            dataType: 'json',
            data: {
                ajax: true,
                ssn: $("input[name='social_security_number']").val()
            },
            success: function (response) {
                if (!response.success) {
                    alert(response.message);
                    return false;
                }

                jQuery('input[name="firstname"]').val(response.first_name);
                jQuery('input[name="lastname"]').val(response.last_name);
                jQuery('input[name="address_1"]').val(response.address_1);
                jQuery('input[name="address_2"]').val(response.address_2);
                jQuery('input[name="postcode"]').val(response.postcode);
                jQuery('input[name="city"]').val(response.city);
                jQuery('select[name="country_id"]').val(203);
                jQuery('select[name="country_id"]').click();
            }
        });
    });
});
//End SSN Block