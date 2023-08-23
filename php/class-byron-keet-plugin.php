<?php
/**
 * Class Byron_Keet_Plugin
 *
 * Main plugin class for handling Byron Keet functionalities.
 */
class Byron_Keet_Plugin {
	
	/**
	 * Constructor function to initialize the class.
	 */
	public function __construct() {
		add_action( 'init', [$this, 'create_block_byron_keet_block_block_init'] );
		add_action( 'admin_menu', [$this, 'byron_keet_admin_menu'] );
		add_action( 'admin_enqueue_scripts', [$this, 'byron_keet_admin_styles_and_scripts'] );
		add_action( 'wp_ajax_byron_keet_fetch_miusage_data', [$this, 'byron_keet_fetch_miusage_data'] );
		add_action( 'wp_ajax_nopriv_byron_keet_fetch_miusage_data', [$this, 'byron_keet_fetch_miusage_data'] );
		add_action( 'wp_ajax_byron_keet_refresh_miusage_data', [$this, 'byron_keet_refresh_miusage_data'] );
		add_action( 'wp_ajax_nopriv_byron_keet_refresh_miusage_data', [$this, 'byron_keet_refresh_miusage_data'] );
	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 *
	 * @return void
	 */
	public function create_block_byron_keet_block_block_init() {
		register_block_type( __DIR__ . '/../build' );
	}

	/**
	 * Adds a menu item to the admin menu to display data from the api call.
	 *
	 * @return void
	 */
	public function byron_keet_admin_menu() {
		add_menu_page(
			__( 'Byron Keet Data Display', 'byron-keet' ),
			__( 'Byron Keet Data', 'byron-keet' ),
			'manage_options',
			'byron-keet-data',
			[$this, 'byron_keet_admin_page_callback'],
			'dashicons-database',
			6
		);
	}

	/**
	 * Adds a menu item to the admin menu to display data from the api call.
	 *
	 * @return void
	 */
	public function byron_keet_admin_page_callback() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Byron Keet Data Display', 'byron-keet' ); ?></h1>
				<?php settings_fields( 'byron_keet_settings_group' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Data', 'byron-keet' ); ?></th>
						<td>
							<div>
								<?php
								$data = get_transient( 'byron_keet_miusage_data' );
								if ($data) {
									$block_content = '<!-- wp:create-block/byron-keet-block /-->';
									echo do_blocks($block_content);
								} else {
									$this->byron_keet_fetch_miusage_data( true );
									$block_content = '<!-- wp:create-block/byron-keet-block /-->';
									echo do_blocks( $block_content );
								}
								?>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Refresh Data', 'byron-keet' ); ?></th>
						<td>
							<button id="byron-keet-refresh" class="button-primary"><?php esc_html_e( 'Refresh Data', 'byron-keet' ); ?></button>
						</td>
					</tr>
				</table>
		</div>
		<?php
	}

	/**
	 * Enqueues the styles and scripts for the admin page.
	 *
	 * @return void
	 */
	public function byron_keet_admin_styles_and_scripts() {
		wp_enqueue_style( 'byron-keet-admin-style', plugin_dir_url( __DIR__ ) . 'css/byron-keet-styles.css' );
		wp_enqueue_script( 'byron-keet-admin-js', plugin_dir_url( __DIR__ ) . 'js/byron-keet-scripts.js', ['jquery'], '', true) ;
		wp_localize_script('byron-keet-admin-js', 'byronKeet', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('byron_keet_nonce')
		]);
	}

	/**
	 * Fetches the data from the miusage endpoint and stores it in a transient.
	 *
	 * @param bool $return Whether to return the data or send it as a JSON response.
	 * @return mixed The data if $return is true, otherwise null.
	 */
	public function byron_keet_fetch_miusage_data( $return = false ) {
		$data = get_transient( 'byron_keet_miusage_data' );

		if (!$data) {
			$response = wp_remote_get( 'https://miusage.com/v1/challenge/1/' );

			if ( is_wp_error($response) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				wp_send_json_error( ['message' => __( 'Unable to fetch data', 'byron-keet' )] );
			}

			$data = wp_remote_retrieve_body( $response );

			// Store the data for 1 hour
			set_transient( 'byron_keet_miusage_data', $data, HOUR_IN_SECONDS );
		}

		if ($return) {
			return $data;
		} else {
			wp_send_json_success( $data );
		}
	}

	/**
	 * Deletes the data from the transient and re-fetches new data to store in a transient.
	 *
	 * @return void
	 */
	public function byron_keet_refresh_miusage_data() {
		if ( ! check_ajax_referer('byron_keet_nonce', 'nonce', false ) ) {
			wp_send_json_error(['message' => 'Nonce verification failed']);
			exit;
		}
		delete_transient( 'byron_keet_miusage_data' );
		$this->byron_keet_fetch_miusage_data();
	}
}
