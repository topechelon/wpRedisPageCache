<?php
namespace RedisPageCache;
class AdminPage {
  protected $is_connected;
  function __construct($is_connected) {
    $this->is_connected = $is_connected;
  }
  function init() {
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );
  }
  function admin_init() {
    register_setting('redis_page_cache_connect','redis_page_cache_connect');
    add_settings_section('redis_page_cache_connect_fields',
                         'Connection',
                         array($this,'getConnectSectionHTML'),
                         'redis-page-cache');
    add_settings_field('redis_page_cache_host',
                       'Host',
                       array($this,'getOptionHostHTML'),
                       'redis-page-cache',
                       'redis_page_cache_connect_fields');
    add_settings_field('redis_page_cache_port',
                       'Port',
                       array($this,'getOptionPortHTML'),
                       'redis-page-cache',
                       'redis_page_cache_connect_fields');
    add_settings_field('redis_page_cache_db',
                       'Regis DB',
                       array($this,'getOptionDbHTML'),
                       'redis-page-cache',
                       'redis_page_cache_connect_fields');
    add_settings_field('redis_page_cache_ttl',
                       'Time To Live',
                       array($this,'getOptionTtlHTML'),
                       'redis-page-cache',
                       'redis_page_cache_connect_fields');
  }
  function admin_menu() {
    add_options_page('Redis Page Cache',
                     'Redis Page Cache',
                     'manage_options',
                     'redis-page-cache',
                     array($this,'getOptionScreenHTML')
                     );
  }
  function getOptionScreenHTML() {
    if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
?>
<div class='wrap'>
  <h2>Redis Page Cache Options</h2>
  <form method="post" action="options.php">
  <?php if(!$this->is_connected) :?>
  <div class='error settings-error notice is-dismissible'>
  <p>Redis Not Connected</p>
  </div>
  <?php endif; ?>
  <?php settings_fields('redis_page_cache_connect'); ?>
  <?php do_settings_sections('redis-page-cache'); ?>
  <?php submit_button(); ?>
  </form>
</div>
<?php
  }
  function getConnectSectionHTML() {
    echo "Settings for Connecting to Redis.";
  }
  function getOptionHostHTML() {
    $settings = \get_option('redis_page_cache_connect');
    $host = !empty($settings['host']) ? $settings['host'] : '127.0.0.1';
    echo "<input type='text' name='redis_page_cache_connect[host]' value='$host'>";
  }
  function getOptionPortHTML() {
    $settings = \get_option('redis_page_cache_connect');
    $port = !empty($settings['port']) ? $settings['port'] : '6379';
    echo "<input type='text' name='redis_page_cache_connect[port]' value='$port'>";
  }
  function getOptionDbHTML() {
    $settings = \get_option('redis_page_cache_connect');
    $db = !empty($settings['db']) ? $settings['db'] : '0';
    echo "<input type='text' name='redis_page_cache_connect[db]' value='$db'>";
  }
  function getOptionTtlHTML() {
    $settings = \get_option('redis_page_cache_connect');
    $ttl = !empty($settings['ttl']) ? $settings['ttl'] : '3600';
    echo "<input type='text' name='redis_page_cache_connect[ttl]' value='$ttl'>";
  }

}
