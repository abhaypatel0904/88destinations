<?php

class Shortcodes
{
    protected static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $this->hooks();
    }

    public function hooks()
    {
        add_shortcode( 'tours_by_location', array( $this, 'render_tours_by_location_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_tours_list_styles' ) );
    }
    
    function enqueue_tours_list_styles() {
        wp_enqueue_style('shortcodes_style', get_stylesheet_directory_uri() . '/modules/shortcodes/css/shortcodes.css', [], rand(), 'all');
    }

    /**
     * Shortcode: Display Tours filtered by location with flexible view.
     *
     * Usage: [tours_by_location location="riyadh" limit="6" view="slider"], for home page, pass only limit 6 parameter
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_tours_by_location_shortcode( $atts = array() ) {

        $atts = shortcode_atts(
            array(
                'location' => '',
                'limit'    => 6,
                'view'     => 'list', // Accepted: 'slider' or 'list'.
            ),
            $atts,
            'tours_by_location'
        );

        $limit    = (int) $atts['limit'];
        $location = sanitize_text_field( $atts['location'] );
        $view     = sanitize_key( $atts['view'] );

        // Ensure helper object exists.
        if ( ! is_callable( 'helper_object' ) ) {
            return '';
        }

        // Base query args.
        $query_args = array(
            'post_type'      => 'tour',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
        );

        // Add taxonomy filter only if location is specified.
        if ( ! empty( $location ) ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'location',
                    'field'    => 'slug',
                    'terms'    => $location,
                ),
            );
        }

        $tour_query = new WP_Query( $query_args );

        if ( ! $tour_query->have_posts() ) {
            return '';
        }

        ob_start();

        $template_args = array( 'tour_query' => $tour_query );

        // Load appropriate template.
        if ( $view === 'list' ) {
            helper_object()->get_template( 'tours-shortcode-list-view.php', 'shortcodes', $template_args );
        } else {
            helper_object()->get_template( 'tours-shortcode-slider-view.php', 'shortcodes', $template_args );
        }

        return ob_get_clean();
    }

}
Shortcodes::get_instance();