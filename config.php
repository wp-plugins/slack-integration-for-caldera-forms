<div class="caldera-config-group">
	<label><?php echo __('Webhook URL', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required" name="{{_name}}[url]" value="{{url}}">
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Username', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required magic-tag-enabled" name="{{_name}}[username]" value="{{#if username}}{{username}}{{else}}incoming-webhook{{/if}}">
		<p><?php _e('The name of the user the message will be displays as', 'cf-slack'); ?></p>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php _e( 'Icon', 'cf-slack'); ?> </label>
	<div class="caldera-config-field" id="{{_id}}_preview">
		{{#if file}}
			<span id="{{_id}}_filepreview"><img class="{{_id}}_filepreview" src="{{file}}" style="margin: 0px 12px 0px 0px; vertical-align: middle; width: 24px;">{{name}}&nbsp;&nbsp;</span><span class="{{_id}}_btn button button-small"><?php _e( 'Change Image', 'cf-slack'); ?></span> <button type="button" class="button {{_id}}_btn_remove button-small"><?php _e( 'Remove Image', 'cf-slack'); ?></button>
		{{else}}
		<button type="button" class="button {{_id}}_btn button-small"><?php _e( 'Select Image', 'cf-slack'); ?></button>
		{{/if}}
	</div>
</div>
<input type="hidden" id="{{_id}}_file" class="block-input field-config" name="{{_name}}[file]" value="{{file}}">
<input type="hidden" id="{{_id}}_name" class="block-input field-config" name="{{_name}}[name]" value="{{name}}">


{{#script}}
//<script>
jQuery(document).on('click', '.{{_id}}_btn_remove', function(){

	jQuery('.{{_id}}_btn').html('<?php _e( 'Select Image', 'cf-slack'); ?>');
	jQuery('#{{_id}}_filepreview').remove();
	jQuery('#{{_id}}_file').val('');
	jQuery('#{{_id}}_name').val('');
	jQuery(this).remove();

});
jQuery(document).on('click', '.{{_id}}_btn', function(){

	var button = jQuery(this);
	var frame = wp.media({
		title : '<?php _e( 'Select Image', 'cf-slack'); ?>',
		multiple : false,
		button : { text : '<?php _e( 'Use Image', 'cf-slack'); ?>' }
	});
	var preview = jQuery('#{{_id}}_preview');

	// Runs on select
	frame.on('select',function(){
		var objSettings = frame.state().get('selection').first().toJSON(),
			fid = jQuery('#{{_id}}_file'),
			nid = jQuery('#{{_id}}_name');
			//console.log(button.parent().parent());

		nid.val(objSettings.filename);
		//console.log(objSettings);
		var icon = '<img class="{{_id}}_filepreview" src="'+objSettings.icon+'" style="margin: 0px 12px 0px 0px; vertical-align: middle; width: 20px;">';
		if(objSettings.type === 'image'){
			if(objSettings.sizes.thumbnail){
				fid.val(objSettings.sizes.thumbnail.url);
				icon = '<img class="{{_id}}_filepreview image" src="'+objSettings.sizes.thumbnail.url+'" style="margin: 0px 12px 0px 0px; vertical-align: middle; width: 24px;">';
			}else if(objSettings.sizes.medium){
				fid.val(objSettings.sizes.medium.url);
				icon = '<img class="{{_id}}_filepreview" src="'+objSettings.sizes.medium.url+'" style="margin: 0px 12px 0px 0px; vertical-align: middle; width: 24px;">';
			}else if(objSettings.sizes.full){
				fid.val(objSettings.sizes.full.url);
				icon = '<img class="{{_id}}_filepreview" src="'+objSettings.sizes.full.url+'" style="margin: 0px 12px 0px 0px; vertical-align: middle; width: 24px;">';
			}
		}
		//preview.html(icon+objSettings.filename+'&nbsp;&nbsp;<span class="{{_id}}_btn button button-small">Change Image</span>');
		preview.html('<span id="{{_id}}_filepreview">' + icon+objSettings.filename+'&nbsp;&nbsp;</span><span class="{{_id}}_btn button button-small"><?php _e( 'Change Image', 'cf-slack' ); ?></span> <button type="button" class="button {{_id}}_btn_remove button-small"><?php _e( 'Remove Image', 'cf-slack'); ?></button>');

	});

	// Open ML
	frame.open();
});
{{/script}}


<div class="caldera-config-group">
	<label><?php echo __('Message Text', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<textarea class="block-input field-config required magic-tag-enabled" name="{{_name}}[text]">{{#if text}}{{text}}{{else}}<?php _e('New form submission', 'cf-slack'); ?>{{/if}}</textarea>
		<p><?php _e('The message that gets sent to Slack.', 'cf-slack'); ?></p>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Submission', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<label><input type="checkbox" class="field-config" name="{{_name}}[attach]" id="{{_id}}_attach" value="1" {{#if attach}}checked="checked"{{/if}}> Attach the submission data to the message</label>
	</div>
</div>

<div id="{{_id}}_do_attach">
	<div class="caldera-config-group">
		<label><?php echo __('Color', 'cf-slack'); ?> </label>
		<div class="caldera-config-field">
			<input type="text" class="field-config minicolor-picker" name="{{_name}}[color]" id="{{_id}}_dcolor" style="width:110px;" value="{{#if color}}{{color}}{{else}}#91A856{{/if}}"><span id="{{_id}}_dcolor_preview" data-for="{{_id}}_dcolor" class="preview-color-selector" style="margin-left: -28px;background-color: {{color}};"></span>
			<p><?php _e('The color assigned to the attachment message.', 'cf-slack'); ?></p>
		</div>
	</div>
</div>
<div class="caldera-config-group">
	<label><?php echo __('Channel Override', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config magic-tag-enabled" name="{{_name}}[channel]" value="{{channel}}">
		<p><?php _e('Overrides the default channel for this webhook. Leave blank to use default.', 'cf-slack'); ?></p>
	</div>
</div>
{{#script}}
	color_picker_init();
	jQuery('body').on('change', '#{{_id}}_dcolor', function(){
		jQuery('#{{_id}}_dcolor_preview').css('background-color', this.value );
	});
	jQuery('body').on('change', '#{{_id}}_attach', function(){
		if(jQuery(this).prop('checked')){
			jQuery('#{{_id}}_do_attach').show();
		}else{
			jQuery('#{{_id}}_do_attach').hide();
		}
	});
	jQuery('#{{_id}}_attach').trigger('change');
{{/script}}