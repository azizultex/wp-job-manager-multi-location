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
     * @since 1.0
     */
    public function __construct() {
        $this->version      = '1.0';
        $this->file         = __FILE__;
        $this->basename     = plugin_basename( $this->file );
        $this->plugin_dir   = plugin_dir_path( $this->file );
        $this->plugin_url   = set_url_scheme( plugin_dir_url ( $this->file ), is_ssl() ? 'https' : 'http' );
        $this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );
        $this->domain       = 'multi-location';
        $this->setup_actions();
    }

    /**
    * Setup the default hooks and actions
    *
    * @since 1.0
    *
    * @return void
    */
   private function setup_actions() {
        if( !class_exists( 'WP_Job_Manager_Extended_Location' ) ){
            add_action( 'admin_notices', array( $this, 'wp_job_manager_extended_location_missing' ));
            return;
        }
        /* Register Scripts */
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 99);
        add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 99);
        /* frontend job edit, submit page */
        add_action( 'submit_job_form_end', array( $this, 'front_end_job_edit_submit' ) );

        /* load text domain */
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        /* Save Geo for New Post */
        add_action( 'job_manager_save_job_listing', array($this, 'save_post_location'), 31, 2 );
        add_action( 'resume_manager_save_resume', array($this, 'save_post_location'), 31, 2 );
        add_action( 'wpjm_events_save_event', array($this, 'save_post_location'), 30, 2 );

        /* Save Geo on Update Post */
        add_action( 'job_manager_update_job_data', array($this, 'save_post_location'), 26, 2 );
        add_action( 'resume_manager_update_resume_data', array($this, 'save_post_location'), 25, 2 );
        add_action( 'wpjm_events_update_event_data', array($this, 'save_post_location'), 26, 2 );

        // multi-location widgets
        add_action( 'widgets_init', array( $this, 'widgets_init' ), 20);

        // explore page facets locations ids 
        add_filter( 'facetwp_filtered_post_ids', array($this, 'localize_explore_page_results_ids'), 10, 2);
    }

    public function wp_job_manager_extended_location_missing(){
        $class = 'notice notice-error';
        $message = __( 'Extended Location for WP Job Manager is required to activate this plugin plugin.', $this->domain );
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html__( $message ) ); 
    }

    public function localize_scripts_data(){
        // localize script data
        global $post;
        $listing = listify_get_listing( $post->ID );
        $mapSettings = apply_filters(
            'listify_single_map_settings',
            array(
                'provider'   => 'googlemaps' === get_theme_mod( 'map-service-provider', 'googlemaps' ) ? 'googlemaps' : 'mapbox',
                'lat'        => $listing->get_lat(),
                'lng'        => $listing->get_lng(),
                'term'       => $listing->get_marker_term()->term_id,
                'icon'       => $listing->get_marker_term_icon(),
                'mapOptions' => array(
                    'zoom'          => apply_filters( 'listify_single_listing_map_zoom', 15 ),
                    'styles'        => get_theme_mod( 'map-appearance-scheme', 'blue-water' ),
                    'mapboxTileUrl' => get_theme_mod( 'mapbox-tile-url', '' ),
                    'maxZoom'       => get_theme_mod( 'map-behavior-max-zoom', 17 ),
                ),
            )
        );

        $additionallocations = get_post_meta($post->ID, '_additionallocations', true);
        $listingEditPage = get_post_meta($_GET['job_id'], '_additionallocations', true);
        $options = array(
            'lat'         => $listing->get_lat(),
            'lng'         => $listing->get_lng()
        );
        $this->local = array(
            'listing' => $listing,
            'mapSettings' => $mapSettings,
            'additionallocations' => $additionallocations,
            'listingEditPage'   => $listingEditPage,
            'options'   => $options,
        );
    }

    public function localize_explore_page_results_ids( $post_ids, $class ) {
        $per_page = get_option( 'job_manager_per_page' );
        $locations = array_slice($post_ids, 0, $per_page);
        $extraMarkers = [];
        foreach($locations as $id){
            $latLng = get_post_meta($id, '_additionallocations', true);
            foreach($latLng as $lt){
                $extraMarkers[] = array(
                    'id' => $id,
                    'location' => $lt,
                );
            }
        }
        wp_localize_script('multi-location-explore', 'expPgAddiLoc', $extraMarkers);
        return $post_ids;
    }

    public function register_scripts(){
        $this->localize_scripts_data();
        wp_enqueue_style( 'multi-location-css', $this->plugin_url . 'assets/css/multilocation.css');
        if(is_singular('job_listing')){
            wp_enqueue_script( 'single-listing', $this->plugin_url . 'assets/js/single-listing.js', array('jquery', 'listify', 'wp-util', 'listify-map', 'mapify'), $this->version, true );
            wp_localize_script( 'single-listing', 'mapSettings', $this->local['mapSettings'] );
            wp_localize_script( 'single-listing', 'additionallocations', $this->local['additionallocations'] );
        }
        if(listify_results_has_map()){
            wp_enqueue_script( 'multi-location-explore', $this->plugin_url . 'assets/js/multilocation-explore.js', array('jquery', 'listify', 'wp-util', 'listify-results'), $this->version, true );
        }
        if(is_admin()){
            wp_enqueue_script( 'admin-script', $this->plugin_url . 'assets/js/admin-script.js', array( 'jquery', 'mapify' ), $this->version, true );
            wp_localize_script( 'admin-script', 'additionallocations', $this->local['additionallocations'] );
            wp_localize_script( 'admin-script', 'latlng', $this->local['options'] );
        }
    }

    public function front_end_job_edit_submit(){
        wp_enqueue_script( 'frontend-script', $this->plugin_url . 'assets/js/frontend-script.js', array( 'jquery', 'mapify' ), $this->version, true );
        wp_localize_script( 'frontend-script', 'additionallocations', $this->local['listingEditPage'] );
		wp_localize_script( 'frontend-script', 'latlng', $this->local['options'] );
    }

    function save_post_location($post_id, $values) {
        $post_type = get_post_type( $post_id );
        // WP_Job_Manager_Geocode::get_location_data();
        /* Job Listing Location */
        if( 'job_listing' == $post_type && isset ( $_POST[ 'additionallocation' ] ) ){
            update_post_meta( $post_id, '_additionallocations', $_POST[ 'additionallocation' ]);
        } else {
            update_post_meta( $post_id, '_additionallocations', []);
        }
    }

    function widgets_init(){
        /* deregister existing map widget */ 
        unregister_widget( 'Listify_Widget_Listing_Map' );
        /* create custom multi location widget */
        include_once  $this->plugin_dir . 'widgets/map-widget.php';
        register_widget( 'Multi_Location_Listify_Widget_Listing_Map' );
    }

    public function load_textdomain() {
        // $locale = apply_filters( 'plugin_locale', get_locale(), 'wp-job-manager-locations' );
        // load_textdomain( 'wp-job-manager-locations', WP_LANG_DIR . "/wp-job-manager-locations/wp-job-manager-locations-$locale.mo" );
        load_plugin_textdomain( $this->domain, false, dirname( $this->basename ) . '/languages/' );
    }
}


/**
 * Start things up.
 *
 * Use this function instead of a global.
 *
 * $ajmr = ajmr();
 *
 * @since 1.0
 */
function wp_job_manager_multi_location() {
    return Keendevs_Multi_Location_WP_JOB_M::instance();
}
add_action( 'plugins_loaded', 'wp_job_manager_multi_location' );