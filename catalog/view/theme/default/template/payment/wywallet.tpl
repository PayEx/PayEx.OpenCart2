<h2><?php echo $text_title; ?></h2>
<p><?php echo $text_description; ?></p>
<div class="buttons">
    <div class="right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button"/>
    </div>
</div>
<script type="text/javascript">
    <!--
    $('#button-confirm').bind('click', function () {
        window.location = '<?php echo $action; ?>';
    });
    //-->
</script>
