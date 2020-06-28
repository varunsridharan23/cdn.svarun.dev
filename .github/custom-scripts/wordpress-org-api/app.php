<?php
define( 'APP_PATH', __DIR__ . '/' );
define( 'WP_USERNAME', 'varunms' );
define( 'SAVE_PATH', APP_PATH . '../../../wordpress.org/' );

if ( ! file_exists( APP_PATH . 'vendor/autoload.php' ) ) {
	die( '🛑 WordPress.org API Library Not Found !' );
}

try {
	require APP_PATH . 'vendor/autoload.php';
	require APP_PATH . '../functions.php';
	require APP_PATH . 'WporgClient.php';
	require APP_PATH . 'WporgService.php';
	$wporgClient = \Rarst\Guzzle\WporgClient::getClient();
	$response    = $wporgClient->getPluginsBy( 'author', WP_USERNAME, 1, 10000 );
	$final       = array();

	if ( isset( $response['info']['pages'] ) ) {
		foreach ( $response['plugins'] as $plugin ) {
			$data                = $plugin;
			$data['source-code'] = 'https://github.com/varunsridharan/' . $data['slug'];
			unset( $data['author'] );
			unset( $data['author_profile'] );
			unset( $data['ratings'] );
			unset( $data['num_ratings'] );
			unset( $data['support_threads'] );
			unset( $data['support_threads_resolved'] );
			unset( $data['sections'] );
			unset( $data['description'] );
			unset( $data['tags'] );
			unset( $data['author_block_count'] );
			unset( $data['author_block_rating'] );
			unset( $data['compatibility'] );
			unset( $data['donate_link'] );
			unset( $data['versions'] );
			$data['mini_slug'] = slugify( $data['slug'] );
			$final[]           = $data;
		}
		@mkdir( SAVE_PATH );
		@file_put_contents( SAVE_PATH . 'plugins.json', json_encode( $final, JSON_PRETTY_PRINT ) );
	}
} catch ( Exception $exception ) {
	$msg = '🛑 Unknown Error !!' . PHP_EOL . PHP_EOL;
	$msg .= print_r( $exception->getMessage(), true );
	die( $msg );
}