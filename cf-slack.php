<?php
/**
 * Plugin Name: Slack Integration for Caldera Forms
 * Plugin URI:  
 * Description: Send messages into Slack on form submission.
 * Version:     1.0.0
 * Author:      David Cramer
 * Author URI:  
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */



// add filters
add_filter('caldera_forms_get_form_processors', 'cf_slack_register_processor');
function cf_slack_register_processor($pr){
	$pr['slack'] = array(
		"name"              =>  __('Slack', 'cf-slack'),
		"description"       =>  __("post a message to slack on submission", 'cf-slack'),
		"author"            =>  'David Cramer',
		"author_url"        =>  'http://cramer.co.za',
		"icon"				=>	plugin_dir_url(__FILE__) . "icon.png",
		"processor"         =>  'cf_send_slack_message',
		"template"          =>  plugin_dir_path(__FILE__) . "config.php",
	);

	return $pr;
}

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
		$payload['channel'] = Caldera_Forms::do_magic_tags( $channel );
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

	$args = array(
		'body' => array(
			'payload'	=>	json_encode($payload)
		)
	);
	$raw_request = wp_remote_post( $config['url'], $args );
}