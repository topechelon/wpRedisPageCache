<?php
namespace RedisPageCache;
class PageCache {
  protected $Cache = null;
  function __construct($Cache) {
    $this->Cache = $Cache;
  }
  function setHash($hash) {
    $this->hash = $hash;
  }
  function getHash() {
    return $this->hash;
  }
  function start_capture() {
    ob_start(array($this,"ob_callback"));
  }
  function clear_post($postId) {
	  // listofurls from varnish-http-purge plugin
    $listofurls = array();
	  // Category purge based on Donnacha's work in WP Super Cache
    $categories = get_the_category($postId);
    if ( $categories ) {
      foreach ($categories as $cat) {
        array_push($listofurls, get_category_link( $cat->term_id ) );
      }
    }
    // Tag purge based on Donnacha's work in WP Super Cache
    $tags = get_the_tags($postId);
    if ( $tags ) {
      foreach ($tags as $tag) {
        array_push($listofurls, get_tag_link( $tag->term_id ) );
      }
    }

    // Author URL
    $post_author = get_post_field( 'post_author', $postId );
    array_push($listofurls,
      get_author_posts_url( $post_author ),
      get_author_feed_link( $post_author )
    );

    // Archives and their feeds
    $archiveurls = array();
    $post_type = get_post_type( $postId );
    if ( get_post_type_archive_link( $post_type ) == true ) {
      array_push($listofurls,
        get_post_type_archive_link( $post_type ),
        get_post_type_archive_feed_link( $post_type )
      );
    }

    // Post URL
    array_push($listofurls, get_permalink($postId) );

    // Feeds
    array_push($listofurls,
      get_bloginfo_rss('rdf_url') ,
      get_bloginfo_rss('rss_url') ,
      get_bloginfo_rss('rss2_url'),
      get_bloginfo_rss('atom_url'),
      get_bloginfo_rss('comments_rss2_url'),
      get_post_comments_feed_link($postId)
    );

    // Home Page and (if used) posts page
    array_push($listofurls, home_url('/') );
    if ( get_option('show_on_front') == 'page' ) {
      array_push($listofurls, get_permalink( get_option('page_for_posts') ) );
    }
    foreach($listofurls as $url) {
      $hash = sha1(str_replace(array("http://","https://"),"",$url));
      $this->log("clear hash: $hash");
      $this->Cache->delete($hash);
    }
  }
  function admin_bar_menu($admin_bar) {
    $admin_bar->add_node(array(
                               'id' => 'redis-page-cache-purge',
                               'title' => 'Purge Redis',
                               'href' => wp_nonce_url(add_query_arg('redis-page-cache-purge',1),'redis-page-cache-purge')
                               ));
  }
  function ob_callback($content) {
    $hash = $this->getHash();
    if(!is_user_logged_in() && !empty($content)) {
      $response_headers = headers_list();
      $save = array("headers" => $response_headers,
                    "content" => $content);
      $this->log("set hash: $hash");
      $this->Cache->set($hash,$save);
    }
    return $content;
  }
  function check_cache() {
    $hash = $this->getHash();
    if($this->Cache->has($hash)) {
      $this->log("found hash: $hash");
      $cache = $this->Cache->get($hash);
      $this->processHeaders($cache['headers']);
      echo $cache['content'];
      return true;
    }
    return false;
  }
  function clear_all() {
    if(!empty($_GET['redis-page-cache-purge']) && check_admin_referer('redis-page-cache-purge')) {
      $this->log("all cleared");
      $this->Cache->flushdb();
      add_action( 'admin_notices' , function () {
        echo "<div class='updated notice is-dismissible'><p>Redis Page Cache Purged</p></div>";
      } );
    }
  }
  protected function processHeaders($headers) {
    if(count($headers) > 0) {
      foreach ($headers as $header){
        header($header);
      }
    }
  }
  protected function log($info) {
    //$this->Cache->log($info);
  }
}
