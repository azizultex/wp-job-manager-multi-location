<?php 
/**
 * Plugin Name: Multi Location for WP Job Manager
 * Plugin URI:  https://plugins.keendevs.com/wp-job-manager-multi-location
 * Description: Enable adding multiple locations for a single listing for admin. This plugin also shows in the multiple locations on the frontend search and single listing page location map. This plugin require https://astoundify.com/products/wp-job-manager-extended-location/
 * Author:      Azizul Haque
 * Author URI:  https://keendevs.com
 * Version:     1.0
 * Text Domain: multi-location
 * Domain Path: /languages
 */


 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Keendevs_Multi_Location_WP_JOB_M {

        /**
     * @var $instance
     */
    private static $instance;

    /**
     * Make sure only one instance is only running.
     */
    public static function instance() {
        if ( ! isset ( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Start things up.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->file         = __FILE__;
        $this->basename     = plugin_basename( $this->file );
        $this->plugin_dir   = plugin_dir_path( $this->file );
        $this->plugin_url   = set_url_scheme( plugin_dir_url ( $this->file ), is_ssl() ? 'https' : 'http' );
        $this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );
        $this->domain       = 'multi-location';

        // $files = array(
        //     'includes/class-taxonomy.php',
        //     'includes/class-template.php',
        //     'includes/class-widgets.php'
        // );

        // foreach ( $files as $file ) {
        //     include_once( $this->plugin_dir . '/' . $file );
        // }

        // $this->taxonomy = new Astoundify_Job_Manager_Regions_Taxonomy;
        // $this->template = new Astoundify_Job_Manager_Regions_Template;

        // $this->setup_actions();
    }

    /**
    * Setup the default hooks and actions
    *
    * @since 1.0.0
    *
    * @return void
    */
   private function setup_actions() {
       add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

       /* Job Manager */
    //    add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
   }

    public function load_textdomain() {
        // $locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-locations' );
        // load_textdomain( 'wp-job-manager-locations', WP_LANG_DIR . "/wp-job-manager-locations/wp-job-manager-locations-$locale.mo" );
        load_plugin_textdomain( 'multi-location', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

}


/**
 * Start things up.
 *
 * Use this function instead of a global.
 *
 * $ajmr = ajmr();
 *
 * @since 1.0.0
 */
function wp_job_manager_multi_location() {
    return Keendevs_Multi_Location_WP_JOB_M::instance();
}

wp_job_manager_multi_location();