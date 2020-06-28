<?php
function sanitize_key( $key ) {
	$key = strtolower( $key );
	$key = preg_replace( '/[^a-z0-9_\-]/', '', $key );
	return $key;
}

function slugify( $islug ) {
	$slug     = '';
	$slug_gen = explode( '-', $islug );
	if ( is_array( $slug_gen ) ) {
		foreach ( $slug_gen as $slugi ) {
			$slugi = trim( strtolower( $slugi ) );
			if ( in_array( $slugi, array( 'wc', 'WC', 'Wc', 'wC', 'WooCommerce', 'woocommerce' ) ) ) {
				$slug .= 'wc';
			} elseif ( in_array( $slugi, array( 'wp', 'WP', 'Wp', 'wP', 'WordPress', 'wordpress' ) ) ) {
				$slug .= 'wp';
			} elseif ( in_array( $slugi, array( 'cf7', 'CF7' ) ) ) {
				$slug .= 'cf7';
			} elseif ( in_array( $slugi, array( 'sku', 'SKU' ) ) ) {
				$slug .= 'sku';
			} elseif ( in_array( $slugi, array( 'for', 'FOR' ) ) ) {
				$slug .= '';
			} else {
				$slug .= sanitize_key( $slugi[0] );
			}
		}
	}
	return $slug;
}

function repo_names( $names ) {
	$data = array();
	$path = __DIR__ . '/../../json/repo-titles.json';
	if ( file_exists( $path ) ) {
		$data = json_decode( file_get_contents( $path ), true );
	}

	$data = array_merge( $names, $data );
	@file_put_contents( $path, json_encode( $data, JSON_PRETTY_PRINT ) );
}