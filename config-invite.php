<div class="caldera-config-group" xmlns="http://www.w3.org/1999/html">
	<label><?php echo __('Slack Team Name', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required " name="{{_name}}[team_name]" value="{{team_name}}">
		<p><?php _e( 'The Name of Your Slack Team', 'cf-slack'); ?></p>
	</div>
</div>

<div class="caldera-config-group">
	<label><?php echo __('Token', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required " name="{{_name}}[token]" value="{{token}}">
		<p><?php _e( 'Slack API Token', 'cf-slack'); ?></p>
	</div>
</div>


<div class="caldera-config-group">
	<label><?php echo __('Email', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config required magic-tag-enabled" name="{{_name}}[email]" value="{{email}}">
		<p><?php _e( 'Email Address For Person Requesting Invite', 'cf-slack'); ?></p>
	</div>
</div>


<div class="caldera-config-group">
	<label><?php echo __('First Name', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config  magic-tag-enabled" name="{{_name}}[first_name]" value="{{first_name}}">
		<p><?php _e( 'First Name For Person Requesting Invite', 'cf-slack'); ?></p>
	</div>
</div>


<div class="caldera-config-group">
	<label><?php echo __('Last Name', 'cf-slack'); ?> </label>
	<div class="caldera-config-field">
		<input type="text" class="block-input field-config  magic-tag-enabled" name="{{_name}}[last_name]" value="{{last_name}}">
		<p><?php _e( 'Last Name For Person Requesting Invite', 'cf-slack'); ?></p>
	</div>
</div>

