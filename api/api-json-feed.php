<?php

/**
 * Adds an endpoint where we can get the JSON version of the RSS feed
 */

API_Register::get_instance()->add_endpoint( 
  'get/jsonFeed',
  'brg_api_get_json_feed'
);

function brg_api_get_json_feed( $data ) {
  // Get our default post types to return
  $post_types = filter( 'json/post-types', array( 'post' ) );

  // hmm... need to update the query builder to include 'OR'!
  $query = DB_Query_Builder::select_query( 
    'posts', 
    array(
      'type' => 'post'
    ),
    array(
      'order'     => 'ID',
      'direction' => 'DESC',
      'limit'     => 10
    )
  );
  $results = (new Database_Interface)->query( $query );
  
  // Build out the items in the JSON feed
  $posts_array = array();
  foreach( $results as $post ) {
    $posts_array[] = array(
      'id'             => $post['slug'],
      'url'            => sprintf( '%s/?%s=%s', get_site_base_url(), $post['type'], $post['slug'] ),
      'title'          => $post['title'],
      'content_html'   => $post['content'],
      'summary'        => $post['excerpt'],
      'date_published' => $post['date'],
      'author'         => $post['author'],
    );  
  }

  // General feed info
  $output  = json_encode( array( 
    'version'       => 1,
    'title'         => SITE_TITLE,
    'home_page_url' => get_site_base_url(),
    'feed_url'      => get_site_base_url() . '/api/get/jsonFeed',
    'description'   => does_row_exist( 'options', 'name', 'json_description' ),
    'items'         => $posts_array
  ) );
  
  // DON'T WANT TO USE THE OUTPUT HELPER, 
  // SINCE WE DON'T WANT TO STANDARDIZE THE OUTPUT
  header( 'Content-type: application/json' );
  header( 'Cache-Control: no-cache, must-revalidate' );
  die( $output );
}