<?php
/**
 * Plugin Name: Caldera Forms - Slack Integration
 * Plugin URI:  
 * Description: Send messages into Slack on form submission.
 * Version:     1.1.0
 * Author:      David Cramer for Caldera WP LLC
 * Author URI:  
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */


/**
 * Add the proccesors
 *
 * @since 1.0.0
 */
add_filter('caldera_forms_get_form_processors', 'cf_slack_register_processor');
function cf_slack_register_processor($pr){
	$pr['slack'] = array(
		"name"              =>  __('Slack: Message', 'cf-slack'),
		"description"       =>  __("Post a message to slack on submission", 'cf-slack'),
		"author"            =>  'David Cramer',
		"author_url"        =>  'https://Calderawp.com',
		"icon"				=>	plugin_dir_url(__FILE__) . "icon.png",
		"processor"         =>  'cf_send_slack_message',
		"template"          =>  plugin_dir_path(__FILE__) . "config.php",
	);

	//@since 1.1.0
	$pr['slack-invite'] = array(
		"name"              =>  __('Slack: Invite', 'cf-slack'),
		"description"       =>  __("Send a Slack Invite", 'cf-slack'),
		"author"            =>  'Josh Pollock',
		"author_url"        =>  'https://Calderawp.com',
		"icon"				=>	plugin_dir_url(__FILE__) . "icon.png",
		"processor"         =>  'cf_slack_send_invite',
		"template"          =>  plugin_dir_path(__FILE__) . "config-invite.php",
	);

	return $pr;
}

/**
 * Send a message to Slack
 *
 * @since 1.0.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 */
function cf_send_slack_message($config, $form){

	// create fields
	$fields = array();
	foreach( $form['fields'] as $field ){
		if( $field['type'] === 'html' || $field['type'] === 'button' ){
			continue;
		}
		$entry_value = Caldera_Forms::get_field_data( $field['ID'], $form );
		$fields[] = array(
			'title'		=>	$field['label'],
			'value'		=>	$entry_value,
			'short'		=>	( strlen( $entry_value ) < 100 ? true : false )
		);
	}
	// Create Payload
	$payload = array(
		"username"		=>	 Caldera_Forms::do_magic_tags( $config['username'] )
	);
	// icon
	if( !empty( $config['file'] ) ){
		$payload['icon_url'] =	$config['file'];
	}
	
	// override channel if set
	$channel = trim( $config['channel'] );
	if( !empty( $channel ) ){
		$payload['channel'] = Caldera_Forms::do_magic_tags( trim( $config['channel'] ) );
	}
	// attach if setup
	if( !empty( $config['attach'] ) ){
		$payload['attachments'] = array(
				array(
					"fallback" 	=>	Caldera_Forms::do_magic_tags( $config['text'] ),
					"pretext"	=>	Caldera_Forms::do_magic_tags( $config['text'] ),
					"color"		=>	$config['color'],
					"fields"	=>	$fields
				)
			);
	}else{
		$payload['text'] = Caldera_Forms::do_magic_tags( $config['text'] );
	}

	/**
	 * Filter request before sending message to Slack API
	 *
	 * Runs before encoding to JSON
	 *
	 * @since 1.1.0
	 *
	 * @param array $payload Arguments for API request
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	add_filter( 'cf_slack_message_pre_request', $payload, $config, $form );

	$args = array(
		'body' => array(
			'payload'	=>	json_encode($payload)
		)
	);

	$response = wp_remote_post( $config['url'], $args );
	/**
	 * Get response from Slack API message request.
	 *
	 * Runs after request is sent, but before form processor ends
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Error|array $response The response or WP_Error on failure.
	 * @param array $payload Arguments for API request
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	do_action( 'cf_slack_invite_request_sent', $response, $payload, $config, $form );

}

/**
 * Send a Slack API request to invite a user
 *
 * @since 1.1.0
 *
 * @param array $config Processor config
 * @param array $form Form config
 *
 * @return array|bool
 */
function cf_slack_send_invite( $config, $form ) {

	$request_args = array(
		'email'      => Caldera_Forms::do_magic_tags( $config['email'] ),
		'first_name' => Caldera_Forms::do_magic_tags( $config['first_name'] ),
		'last_name'  => Caldera_Forms::do_magic_tags( $config['last_name'] ),
		'token'      => $config['token'],
		'set_active' => 'true',
		'_attempts'  => 1,
	);

	/**
	 * Filter request args before inviting a user
	 *
	 * Runs before error checking
	 *
	 * @since 1.1.0
	 *
	 * @param array $request_args Arguments for API request
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	add_filter( 'cf_slack_invite_pre_request', $request_args, $config, $form );


	$required_errors = false;

	if ( empty ( $config[ 'team_name' ] ) ) {
		$required_errors[] = array(
			'type' => 'error',
			'note' => __( 'No team was set', 'cf-slack' )
		);
	}

	if ( empty ( $config[ 'token' ] ) ) {
		$required_errors[] = array(
			'type' => 'error',
			'note' => _( 'A valid API token was not found', 'cf-slack' ),
		);
	}


	if ( ! is_email( $request_args['email'] ) ) {
		$required_errors[] = array(
			'type'=>'error',
			'note' => __( 'Email entered is invalid', 'cf-slack' )
		);
	}

	if ( is_array( $required_errors ) ) {
		return $required_errors;

	}


	$url = add_query_arg( $request_args, 'https://slack.com/api/users.admin.invite' );
	$url = add_query_arg( 'timestamp', cf_slack_get_timestamp(), $url );


	$response = wp_remote_post( $url );

	/**
	 * Get response from Slack API invite request.
	 *
	 * Runs after request is sent, but before form processor ends
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Error|array $response The response or WP_Error on failure.
	 * @param array $request_args Arguments for API request
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	do_action( 'cf_slack_invite_request_sent', $response, $request_args, $config, $form );
	if ( ! is_wp_error( $response ) && ! is_wp_error( wp_remote_retrieve_body( $response ) ) ) {
		$response = json_decode( wp_remote_retrieve_body( $response ) );
		if ( false == $response->ok ) {
			return array(
				'type'=>'error',
				'note' => $response->error
			);
		}

	}

}

/**
 * Get timestamp for API requests
 *
 * @since 1.1.0
 *
 * @return int
 */
function cf_slack_get_timestamp() {
	$date = new DateTime( 'NOW', new DateTimeZone( 'America/Los_Angeles' ) );
	return $date->getTimestamp();
}
