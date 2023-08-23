<?php

/**
 * Plugin Name:       Byron Keet Block
 * Description:       Byron Keet plugin for AM project.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Byron Keet
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       byron-keet
 *
 * @package           byron-keet
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path(__FILE__) . 'php/class-byron-keet-plugin.php';

if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI_Command')) {
	require_once plugin_dir_path(__FILE__) . 'php/class-byron-keet-cli-command.php';
}


new Byron_Keet_Plugin();
