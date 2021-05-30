<?php

/**
 * @package           Voice_Studio
 * @author            Paul Shryock
 * @copyright         2021 Paul Shryock
 * @license           Hippocratic 2.1
 *
 * @wordpress-plugin
 * Plugin Name:       Voice Studio
 * Plugin URI:        https://github.com/paulshryock/wp-voice-studio
 * Description:       WordPress enhancements for your voice studio.
 * Version:           0.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Paul Shryock
 * Author URI:        https://pshry.com
 * License:           Hippocratic 2.1
 * License URI:       LICENSE
 * Text Domain:       wpvs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Setup a Voice Studio.
 */
require plugin_dir_path( __FILE__ ) . 'src/classes/voice-studio.php';
$voice_studio = new Voice_Studio();

// Register plugin hooks.
register_activation_hook( __FILE__, 'Voice_Studio::activate' );
register_deactivation_hook( __FILE__, 'Voice_Studio::deactivate' );
register_uninstall_hook(__FILE__, 'Voice_Studio::uninstall' );
