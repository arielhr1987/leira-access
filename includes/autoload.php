<?php

/**
 * WordPress-style autoloader supporting nested namespaces and class filenames like class-*.php
 */
spl_autoload_register( function ( $class ) {

	$prefix   = 'Leira_Access\\';
	$base_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR;

	// Check if the class belongs to our namespace
	if ( strpos( $class, $prefix ) !== 0 ) {
		return;
	}

	// Remove namespace prefix
	$relative_class = substr( $class, strlen( $prefix ) );

	// Break into parts, convert to lowercase-dashed format
	$path_parts = explode( '\\', $relative_class );
	$path_parts = array_map( function ( $part ) {
		return str_replace( '_', '-', strtolower( $part ) );
	}, $path_parts );

	$class_name = array_pop( $path_parts );
	$sub_path   = implode( DIRECTORY_SEPARATOR, $path_parts );

	// Build file path
	$file = $base_dir
	        . ( $sub_path ? $sub_path . DIRECTORY_SEPARATOR : '' )
	        . 'class-' . $class_name . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

