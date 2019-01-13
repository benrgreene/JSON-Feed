<?php

define( 'JSON_OPTION', array(
  'json_description' => 'description'
) );

// Add our RSS feed API endpoint
add_action( 'load-apis', function() {
  load_directory( __DIR__ . '/api' );
});

// Ignore the README file
add_filter( 'filter-excludes', function( $excludes ) {
  $excludes = array_merge( $excludes, array( 'README.md' ));
  return $excludes;
});

// Add our option for setting the post types for the RSS feed
add_action( 'admin_set_defaults', function() {
  foreach( JSON_OPTION as $option_name => $option_value) {
    if( !does_row_exist( 'options', 'name', $option_name ) ) {
      add_option( $option_name, $option_value );
    } 
  }
});