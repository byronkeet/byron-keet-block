<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Byron_Keet_Cli_Command extends \WP_CLI_Command {

	/**
	 * Force the refresh of the data from miusage.com.
	 *
	 * ## EXAMPLES
	 *
	 *     wp byron_keet_force_refresh
	 *
	 */
	public function force_refresh( $args, $assoc_args ) {

		// Delete the transient to force a new fetch on the next AJAX request
		delete_transient( 'byron_keet_miusage_data' );

		// Provide a success message
		
		WP_CLI::success( __( 'The data will be fetched afresh on the next AJAX call.', 'byron-keet' ) );
	}
}


if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::add_command( 'byron_keet_force_refresh', 'Byron_Keet_Cli_Command' );
}
