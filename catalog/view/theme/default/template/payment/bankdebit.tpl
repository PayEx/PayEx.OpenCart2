<h2><?php echo $text_title; ?></h2>
<p><?php echo $text_description; ?></p>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
    <label for="bank_id"><?php echo $text_select_bank; ?></label>
    <select name="bank_id" id="bank_id">
        <?php foreach ($bankdebit_banks as $_key => $bank_id):?>
        <option value="<?php echo $bank_id; ?>"><?php echo $available_banks[$bank_id]; ?></option>
        <?php endforeach; ?>
    </select>
</form>
<div class="buttons">
    <div class="right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button"/>
    </div>
</div>
<script type="text/javascript">
    <!--
    $('#button-confirm').bind('click', function () {
        $('#form').submit();
    });
    //-->
</script>
