<div class="asl-p-cont">
<div class="dump-message asl-dumper"></div>
<div class="row" id="message_complete">
</div>
	<h3>InfoBox Maker</h3>
    <form class="form-horizontal" id="frm-Info-box" method="POST" onsubmit="return false;">
    <div class="dump-message"></div>
	<div class="well">
		<div class="row">
            <div class="form-group col-xs-4 pd-0">
                <label for="txt_country" class="col-sm-3 control-label">InfoBox</label>
                <div class="col-sm-9 pd-0">
                    <select class="form-control" style="width:100%;" id="asl-infobox" name="asl-infobox">
                        <option value="1">Theme 0</option>
                        <option value="2">Theme 1</option>
                    </select>
                </div>
            </div>
            <div class="" style="clear:both"></div>
			<?php  wp_editor($tooltip, 'asl-p-cont', $settings); ?>
            <div class="" style="clear:both"></div>
		</div>
	</div>
    <div class="">
        <div class="col-xs-12 pd-0 form-group btn-group">
            <button type="button" id="asl-save-infobox" class="pull-right btn btn-primary">Update InfoBox</button>
        </div>
    </div>
    </form>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
	var ASL_Instance = {
		url: '<?php echo ASL_URL_PATH ?>'
	};
	asl_engine.pages.InfoBox_maker(<?php echo $inbox_id; ?>);
</script>