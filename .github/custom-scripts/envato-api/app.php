<?php
define( 'APP_PATH', __DIR__ . '/' );
define( 'SAVE_PATH', APP_PATH . '../../../json/' );

if ( ! file_exists( APP_PATH . 'vendor/autoload.php' ) ) {
	die( '🛑 Envato API Library Not Found !' );
}

use MorningTrain\EnvatoApi\EnvatoApi;

try {
	require APP_PATH . 'vendor/autoload.php';
	require APP_PATH . '../functions.php';

	$final = array();
	$token = ( isset( $argv[1] ) ) ? trim( $argv[1], '/' ) : false;

	if ( empty( $token ) ) {
		throw new Exception( 'Invalid Envato Token' );
	}

	$client = new EnvatoApi( $token );
	$result = $client->getItems( [ 'username' => 'varunsridharan' ] );

	if ( isset( $result->matches ) ) {
		foreach ( $result->matches as $item ) {
			$data                 = array();
			$data['id']           = $item->id;
			$data['updated_at']   = $item->updated_at;
			$data['published_at'] = $item->published_at;
			$data['trending']     = $item->trending;
			$data['name']         = $item->name;
			$data['site']         = $item->site;
			$data['url']          = $item->url;
			$data['ref_url']      = 'https://1.envato.market/' . $item->id;
			$data['slug']         = strtolower( preg_replace( '~[^\pL\d]+~u', '-', $item->name ) );
			$data['mini_slug']    = slugify( $data['slug'] );
			$data['docs']         = 'https://p.sva.wiki/' . $data['slug'];
			$data['changelog']    = 'https://p.sva.wiki/' . $data['slug'] . '/change-log';
			$data['docs-git']     = 'https://github.com/vs-docs/' . $data['slug'];
			$data['support']      = 'https://support.varunsridharan.in/?submit-ticket=' . $item->id;
			$data['demo_site']    = 'https://' . $data['slug'] . '.sva.one';

			if ( isset( $item->previews->icon_with_video_preview ) ) {
				$data['banner'] = $item->previews->icon_with_video_preview->landscape_url;
				$data['icon']   = $item->previews->icon_with_video_preview->icon_url;
			} elseif ( isset( $item->previews->icon_with_landscape_preview ) ) {
				$data['banner'] = $item->previews->icon_with_landscape_preview->landscape_url;
				$data['icon']   = $item->previews->icon_with_landscape_preview->icon_url;
			}


			if ( 'codecanyon.net' === $data['site'] ) {
				$final['plugins'][ $data['slug'] ] = $data;
			} elseif ( 'themeforest.net' === $data['site'] ) {
				$final['html'][ $data['slug'] ] = $data;
			}
			repo_names( array( $data['slug'] => $data['name'] ) );
		}

		if ( isset( $final['plugins'] ) && ! empty( $final['plugins'] ) ) {
			uasort( $final['plugins'], 'sort_envato_items_callback' );
		}

		if ( isset( $final['html'] ) && ! empty( $final['html'] ) ) {
			uasort( $final['html'], 'sort_envato_items_callback' );
		}

		@mkdir( SAVE_PATH, 0777, true );
		@file_put_contents( SAVE_PATH . 'envato-items.json', json_encode( $final, JSON_PRETTY_PRINT ) );

	}
} catch ( \Exception $exception ) {
	$msg = '🛑 Unknown Error !!' . PHP_EOL . PHP_EOL;
	$msg .= print_r( $exception->getMessage(), true );
	die( $msg );
}
