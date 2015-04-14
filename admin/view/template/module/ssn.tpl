<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb): ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php endforeach; ?>
    </div>
    <?php if ($error_warning): ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php endif; ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <div id="tab-settings">
                    <table class="form">
                        <tr>
                            <td><?php echo $text_status; ?></td>
                            <td><select name="ssn_payex_status">
                                    <?php if ($ssn_payex_status): ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                    <?php else: ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php endif; ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ssn_payex_account_number"><?php echo $text_account_number ?></label>
                            </td>
                            <td>
                                <input type="text" name="ssn_payex_account_number" id="ssn_payex_account_number"
                                       value="<?php echo $ssn_payex_account_number; ?>" autocomplete="off">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ssn_payex_encryption_key"><?php echo $text_encryption_key ?></label>
                            </td>
                            <td>
                                <input type="password" name="ssn_payex_encryption_key" id="ssn_payex_encryption_key"
                                       value="<?php echo $ssn_payex_encryption_key; ?>" autocomplete="off">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="ssn_payex_mode"><?php echo $text_mode ?></label>
                            </td>
                            <td>
                                <select name="ssn_payex_mode" id="ssn_payex_mode">
                                    <option
                                    <?php if ($ssn_payex_mode === 'LIVE') { echo 'selected="selected" '; } ?> value="LIVE">Live</option>
                                    <option
                                    <?php if ($ssn_payex_mode === 'TEST') { echo 'selected="selected" '; } ?> value="TEST">Test</option>
                                </select>
                            </td>
                        </tr>
                    </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>