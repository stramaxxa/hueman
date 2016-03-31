<?php
/**
* Init admin page actions : Welcome, help page
*
*/
if ( ! class_exists( 'HU_admin_page' ) ) :
  class HU_admin_page {
    static $instance;
    public $support_url;

    function __construct () {
      self::$instance =& $this;
      //add welcome page in menu
      add_action( 'admin_menu'             , array( $this , 'hu_add_welcome_page' ));
      //changelog
      add_action( '__after_welcome_panel'  , array( $this , 'hu_extract_changelog' ));
      //config infos
      add_action( '__after_welcome_panel'  , array( $this , 'hu_config_infos' ), 20 );
      //build the support url
      $this -> support_url = esc_url('wordpress.org/support/theme/hueman');
      //fix #wpfooter absolute positioning in the welcome and about pages
      add_action( 'admin_print_styles'      , array( $this, 'hu_fix_wp_footer_link_style') );
    }



   /**
   * Add fallback admin page.
   * @package Hueman
   * @since Hueman 1.1
   */
    function hu_add_welcome_page() {
        $_name = __( 'About Hueman' , 'hueman' );

        $theme_page = add_theme_page(
            $_name,   // Name of page
            $_name,   // Label in menu
            'edit_theme_options' ,          // Capability required
            'welcome.php' ,             // Menu slug, used to uniquely identify the page
            array( $this , 'hu_welcome_panel' )         //function to be called to output the content of this page
        );
    }



      /**
     * Render welcome admin page.
     */
      function hu_welcome_panel() {

        $is_help        = isset($_GET['help'])  ?  true : false;
        $_faq_url       = esc_url('hueman.presscustomizr.com/');
        $_support_url   = $this -> support_url;
        $_theme_name    = 'Hueman';

        do_action('__before_welcome_panel');

        ?>
        <div id="hueman-admin-panel" class="wrap about-wrap">
          <?php
            if ( $is_help ) {
              printf( '<h1 style="font-size: 2.5em;" class="need-help-title">%1$s %2$s ?</h1>',
                __( "Need help with", 'hueman' ),
                $_theme_name
              );
            } else {
              printf( '<h1 class="need-help-title">%1$s %2$s %3$s</h1>',
                __( "Welcome to", 'hueman' ),
                $_theme_name,
                HUEMAN_VER
              );
            }
          ?>

          <?php if ( $is_help ) : ?>

            <?php $this -> hu_render_help_content(); ?>

          <?php else: ?>

            <div class="about-text tc-welcome">
              <?php
                printf( '<p><strong>%1$s %2$s <a href="#hueman-changelog">(%3$s)</a></strong></p>',
                  sprintf( __( "Thank you for using %s!", 'hueman' ), $_theme_name ),
                  sprintf( __( "%s %s has more features, is safer and more stable than ever to help you designing an awesome website.", 'hueman' ), $_theme_name, HUEMAN_VER ),
                  __( "check the changelog", 'hueman')
                );

                printf( '<p><strong>%1$s</strong></p>',
                  sprintf( __( "The best way to start with %s is to read the %s and visit the %s.", 'hueman'),
                    $_theme_name,
                    sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('hueman.presscustomizr.com/'), __("documentation", 'hueman') ),
                    sprintf( '<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('demo-hueman.presscustomizr.com'), __("demo website", 'hueman') )
                  )
                );
              ?>
            </div>

          <?php endif; ?>

          <?php if ( hu_is_child() ) : ?>
            <div class="changelog point-releases"></div>

            <div class="tc-upgrade-notice">
              <p>
              <?php
                printf( __('You are using a child theme of Hueman %1$s : always check the %2$s after upgrading to see if a function or a template has been deprecated.' , 'hueman'),
                  'v'.HUEMAN_VER,
                  '<strong><a href="#hueman-changelog">changelog</a></strong>'
                  );
                ?>
              </p>
            </div>
          <?php endif; ?>

          <div class="changelog point-releases"></div>


        <?php do_action( '__after_welcome_panel' ); ?>

        <div class="return-to-dashboard">
          <a href="<?php echo esc_url( self_admin_url() ); ?>"><?php
            is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home','hueman' ) : _e( 'Go to Dashboard','hueman' ); ?></a>
        </div>

      </div><!-- //#hueman-admin-panel -->
      <?php
    }


    function hu_render_help_content() {
      ob_start();

      ?>
        <div class="changelog">
              <div class="about-text tc-welcome">
            <?php
              printf( '<p>%1$s</p>',
                sprintf( __( "The best way to start is to read the %s." , 'hueman' ),
                  sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s</a>', esc_url('hueman.presscustomizr.com/'), __("documentation" , 'hueman') )
                )
              );
              printf( '<p>%1$s <a href="%2$s" title="support forum" target="_blank">%3$s</a>.</p>',
                  __( "If you don't find an answer to your question in the documentation, don't panic :) ! The Hueman theme is used by a large number of webmasters constantly reporting bugs and potential issues. If you encounter a problem with the theme, chances are that it's already been reported and fixed in the", 'hueman' ),
                  $this -> support_url,
                  __('support forum', 'hueman')
                );//printf
              ?>
            </div>
            <div class="feature-section col two-col">
              <div class="col">
                  <a class="button-secondary hueman-help" title="documentation" href="<?php echo esc_url('hueman.presscustomizr.com/') ?>" target="_blank"><?php _e( 'Read the documentation','hueman' ); ?></a>
              </div>
              <!-- <div class="col">
                  <a class="button-secondary hueman-help" title="faq" href="<?php echo $_faq_url; ?>" target="_blank"><?php _e( 'Check the FAQ','hueman' ); ?></a>
               </div> -->
               <div class="last-feature col">
                  <a class="button-secondary hueman-help" title="help" href="<?php echo $_support_url; ?>" target="_blank">
                    <?php _e( 'Get help in the support forum','hueman' ); ?>
                  </a>
               </div>
            </div><!-- .two-col -->
          </div><!-- .changelog -->
        <?php
      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo convert_smilies($html);
    }


    /**
   * Extract changelog of latest version from readme.txt file
   *
   * @package Customizr
   * @since Customizr 3.0.5
   */
    function hu_extract_changelog() {
      if( ! file_exists(HU_BASE."readme.txt") ) {
        return;
      }
      if( ! is_readable(HU_BASE."readme.txt") ) {
        echo '<p>The changelog in readme.txt is not readable.</p>';
        return;
      }

      ob_start();
      $stylelines = explode("\n", implode('', file(HU_BASE."readme.txt")));
      $read = false;
      $i = 0;

      foreach ($stylelines as $line) {
        //echo 'i = '.$i.'|read = '.$read.'pos = '.strpos($line, '= ').'|line :'.$line.'<br/>';
        //we stop reading if we reach the next version change
        if ($i == 1 && strpos($line, '= ') === 0 ) {
          $read = false;
          $i = 0;
        }
        //we write the line if between current and previous version
        if ($read) {
          echo $line.'<br/>';
        }
        //we skip all lines before the current version changelog
        if ($line != strpos($line, '= '.HUEMAN_VER)) {
          if ($i == 0) {
            $read = false;
          }
        }
        //we begin to read after current version title
        else {
          $read = true;
          $i = 1;
        }
      }
      $html = ob_get_contents();
      if ($html) ob_end_clean();

      ?>
      <div id="hueman-changelog" class="changelog">
        <h3><?php printf( __( 'Changelog in version %1$s' , 'hueman' ) , HUEMAN_VER ); ?></h3>
          <p><?php echo $html ?></p>
      </div>
      <?php
    }



    /*
    * Inspired by Easy Digital Download plugin by Pippin Williamson
    * @since 3.2.1
    */
    function hu_config_infos() {
      global $wpdb;
      //get WP_Theme
      $_theme                     = wp_get_theme();

      //Get infos from parent theme if using a child theme
      $_theme = $_theme -> parent() ? $_theme -> parent() : $_theme;

      ?>
<div class="wrap">
<h3><?php _e( 'System Informations', 'hueman' ); ?></h3>
<h4 style="text-align: left"><?php _e( 'Please include the following informations when posting support requests' , 'hueman' ) ?></h4>
<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="tc-sysinfo" title="<?php _e( 'To copy the system infos, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'hueman' ); ?>" style="width: 800px;min-height: 800px;font-family: Menlo,Monaco,monospace;background: 0 0;white-space: pre;overflow: auto;display:block;">
<?php do_action( '__system_config_before' ); ?>
# SITE_URL:                 <?php echo site_url() . "\n"; ?>
# HOME_URL:                 <?php echo home_url() . "\n"; ?>
# IS MULTISITE :            <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

# THEME | VERSION :         <?php printf( '%1$s | v%2$s', $_theme -> name , HUEMAN_VER ) . "\n"; ?>
# WP VERSION :              <?php echo get_bloginfo( 'version' ) . "\n"; ?>
# PERMALINK STRUCTURE :     <?php echo get_option( 'permalink_structure' ) . "\n"; ?>

# ACTIVE PLUGINS :
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
  // If the plugin isn't active, don't show it.
  if ( ! in_array( $plugin_path, $active_plugins ) )
    continue;

  echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}

if ( is_multisite() ) :
?>
#  NETWORK ACTIVE PLUGINS:
<?php
$plugins = wp_get_active_network_plugins();
$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

foreach ( $plugins as $plugin_path ) {
  $plugin_base = plugin_basename( $plugin_path );

  // If the plugin isn't active, don't show it.
  if ( ! array_key_exists( $plugin_base, $active_plugins ) )
    continue;

  $plugin = get_plugin_data( $plugin_path );

  echo $plugin['Name'] . ' :' . $plugin['Version'] ."\n";
}
endif;
//GET MYSQL VERSION
global $wpdb;
$mysql_ver =  ( ! empty( $wpdb->use_mysqli ) && $wpdb->use_mysqli ) ? @mysqli_get_server_info( $wpdb->dbh ) : @mysql_get_server_info();
?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo $mysql_ver . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:   <?php echo ( $this -> hu_let_to_num( WP_MEMORY_LIMIT )/( 1024 ) )."MB"; ?><?php echo "\n"; ?>
PHP Safe Mode:            <?php echo ini_get( 'safe_mode' ) ? "Yes" : "No\n"; ?>
PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Upload Max Size:      <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:  <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:        <?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:  <?php echo ini_get( 'allow_url_fopen' ) ? "Yes" : "No\n"; ?>

WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

Show On Front:            <?php echo get_option( 'show_on_front' ) . "\n" ?>
Page On Front:            <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
Page For Posts:           <?php $id = get_option( 'page_for_posts' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
<?php do_action( '__system_config_after' ); ?>
</textarea>
</div>
</div>
      <?php
      }//end of function


      /**
       * TC Let To Num
       *
       * Does Size Conversions
       *
       *
       * @since 3.2.2
       */
      function hu_let_to_num( $v ) {
        $l   = substr( $v, -1 );
        $ret = substr( $v, 0, -1 );

        switch ( strtoupper( $l ) ) {
          case 'P': // fall-through
          case 'T': // fall-through
          case 'G': // fall-through
          case 'M': // fall-through
          case 'K': // fall-through
            $ret *= 1024;
            break;
          default:
            break;
        }

        return $ret;
      }

    /**
    * hook : admin_print_styles
    * fix the absolute positioning of the wp footer admin link in the welcome pages
    * @return void
    */
    function hu_fix_wp_footer_link_style() {
      /* if ( is_array(get_current_screen()) )
        array_walk_recursive(get_current_screen(), function(&$v) { $v = htmlspecialchars($v); }); */
      $screen = get_current_screen();
      if ( 'appearance_page_welcome' != $screen-> id )
        return;
      ?>
        <style type="text/css" id="tc-fix-wp-footer-position">
          .wp-admin #wpfooter {bottom: inherit;}
        </style>
      <?php
    }

  }//end of class
endif;