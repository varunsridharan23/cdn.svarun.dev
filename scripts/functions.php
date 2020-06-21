<?php
function sanitize_key( $key ) {
	$key = strtolower( $key );
	$key = preg_replace( '/[^a-z0-9_\-]/', '', $key );
	return $key;
}