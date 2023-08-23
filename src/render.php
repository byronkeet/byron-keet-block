<?php

/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Fetch the data using your existing function or a variation of it.
function byron_keet_fetch_miusage_data_for_block()
{
	// Check for the transient data
	$data = get_transient('byron_keet_miusage_data');

	if (!$data) {
		$response = wp_remote_get('https://miusage.com/v1/challenge/1/');

		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			return null;
		}

		$data = wp_remote_retrieve_body($response);

		// Store the data for 1 hour
		set_transient('byron_keet_miusage_data', $data, HOUR_IN_SECONDS);
	}

	$parsed_data = json_decode($data, true);

	if (isset($parsed_data['data'])) {
		return $parsed_data['data'];
	}

	return null;
}

$data = byron_keet_fetch_miusage_data_for_block();
if ( ! $data ) {
	_e( 'Unable to fetch data', 'byron-keet-block' );
} else {
	$headers_map = array(
		'showID' => 'ID',
		'showFirstName' => 'First Name',
		'showLastName' => 'Last Name',
		'showEmail' => 'Email',
		'showDate' => 'Date'
	);
?>
	<table <?php echo get_block_wrapper_attributes(); ?>>
		<thead>
			<tr>
				<?php foreach ( $headers_map as $attr => $header ) : ?>
					<?php if ( isset($attributes[$attr]) && $attributes[$attr] ) : ?>
						<th><?php echo $header; ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $data['rows'] as $row ) : ?>
				<tr>
					<?php if ( $attributes['showID'] ) : ?>
						<td><?php echo esc_html( $row['id'] ); ?></td>
					<?php endif; ?>
					<?php if ( $attributes['showFirstName'] ) : ?>
						<td><?php echo esc_html( $row['fname'] ); ?></td>
					<?php endif; ?>
					<?php if ( $attributes['showLastName'] ) : ?>
						<td><?php echo esc_html( $row['lname'] ); ?></td>
					<?php endif; ?>
					<?php if ( $attributes['showEmail'] ) : ?>
						<td><?php echo esc_html( $row['email'] ); ?></td>
					<?php endif; ?>
					<?php if ( $attributes['showDate'] ) : ?>
						<td><?php echo date( 'n/j/Y', $row['date'] ); ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php
}
