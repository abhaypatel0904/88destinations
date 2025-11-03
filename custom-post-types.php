<?php
/**
 * Register Custom Post Type: Tour
 * Register Custom Taxonomy: Tour Type (Single-Day, Multi-Day)
 */
function register_tour_cpt_and_taxonomy() {
    
    // --- Register CPT: Tour ---
    $labels = array(
        'name'               => __( 'Tours', 'textdomain' ),
        'singular_name'      => __( 'Tour', 'textdomain' ),
        'menu_name'          => __( 'Tours', 'textdomain' ),
        'name_admin_bar'     => __( 'Tour', 'textdomain' ),
        'add_new'            => __( 'Add New Tour', 'textdomain' ),
        'add_new_item'       => __( 'Add New Tour', 'textdomain' ),
        'new_item'           => __( 'New Tour', 'textdomain' ),
        'edit_item'          => __( 'Edit Tour', 'textdomain' ),
        'view_item'          => __( 'View Tour', 'textdomain' ),
        'all_items'          => __( 'All Tours', 'textdomain' ),
        'search_items'       => __( 'Search Tours', 'textdomain' ),
        'not_found'          => __( 'No tours found.', 'textdomain' ),
        'not_found_in_trash' => __( 'No tours found in Trash.', 'textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'tours' ),
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-palmtree', // 🌴
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true, // Enable Gutenberg & REST API
    );

    register_post_type( 'tour', $args );

    // --- Register Taxonomy: Tour Type ---
    $tax_labels = array(
        'name'              => __( 'Tour Types', 'textdomain' ),
        'singular_name'     => __( 'Tour Type', 'textdomain' ),
        'search_items'      => __( 'Search Tour Types', 'textdomain' ),
        'all_items'         => __( 'All Tour Types', 'textdomain' ),
        'edit_item'         => __( 'Edit Tour Type', 'textdomain' ),
        'update_item'       => __( 'Update Tour Type', 'textdomain' ),
        'add_new_item'      => __( 'Add New Tour Type', 'textdomain' ),
        'new_item_name'     => __( 'New Tour Type Name', 'textdomain' ),
        'menu_name'         => __( 'Tour Types', 'textdomain' ),
    );

    $tax_args = array(
        'hierarchical'      => true,
        'labels'            => $tax_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'tour-type' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'tour_type', array( 'tour' ), $tax_args );

    // --- Add Default Terms ---
    if (!term_exists('Single-Day', 'tour_type')) {
        wp_insert_term('Single-Day', 'tour_type');
    }
    if (!term_exists('Multi-Day', 'tour_type')) {
        wp_insert_term('Multi-Day', 'tour_type');
    }
}
add_action( 'init', 'register_tour_cpt_and_taxonomy' );

/**
 * Add Meta Boxes for Tour CPT
 */
function tour_add_meta_boxes() {
    add_meta_box(
        'tour_details_box',
        'Tour Details',
        'tour_details_box_callback',
        'tour',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'tour_add_meta_boxes');

/**
 * Meta Box Display Callback
 */
function tour_details_box_callback($post) {
    wp_nonce_field(basename(__FILE__), 'tour_details_nonce');

    $price      = get_post_meta($post->ID, '_tour_price', true);
    $max_people = get_post_meta($post->ID, '_tour_max_people', true);
    $image      = get_post_meta($post->ID, '_tour_image', true);
    $map_points = get_post_meta($post->ID, '_tour_map_points', true);
    $inclusions = get_post_meta($post->ID, '_tour_inclusions', true);
    $exclusions = get_post_meta($post->ID, '_tour_exclusions', true);
    $itinerary  = get_post_meta($post->ID, '_tour_itinerary', true);

    if (!is_array($map_points)) $map_points = [];
    if (!is_array($inclusions)) $inclusions = [];
    if (!is_array($exclusions)) $exclusions = [];
    if (!is_array($itinerary))  $itinerary  = [];
    ?>

    <style>
        .tour-field { margin-bottom: 20px; }
        .tour-field label { font-weight: bold; display: block; margin-bottom: 6px; }
        .tour-repeatable { margin-bottom: 10px; border: 1px solid #ccc; padding: 10px; background: #fafafa; }
        .tour-repeatable input, .tour-repeatable textarea { width: 100%; margin-bottom: 6px; }
        .add-row, .remove-row { margin-top: 5px; }
    </style>

    <div class="tour-field">
        <label for="tour_price">Price (approx per person)</label>
        <input type="number" name="tour_price" id="tour_price" value="<?php echo esc_attr($price); ?>" min="0" step="0.01">
    </div>

    <div class="tour-field">
        <label for="tour_max_people">Max People</label>
        <input type="number" name="tour_max_people" id="tour_max_people" value="<?php echo esc_attr($max_people); ?>" min="1">
    </div>

    <div class="tour-field">
        <label for="tour_image">Image (Media URL)</label>
        <input type="text" name="tour_image" id="tour_image" value="<?php echo esc_url($image); ?>">
        <button type="button" class="button upload-tour-image">Upload Image</button>
    </div>

    <hr>

    <!-- Map Coordinates -->
    <div class="tour-field">
        <label>Map Coordinates (Multiple)</label>
        <div id="tour_map_points">
            <?php foreach ($map_points as $index => $coords): ?>
                <div class="tour-repeatable">
                    <input type="text" name="tour_map_points[<?php echo $index; ?>][lat]" placeholder="Latitude" value="<?php echo esc_attr($coords['lat']); ?>">
                    <input type="text" name="tour_map_points[<?php echo $index; ?>][lng]" placeholder="Longitude" value="<?php echo esc_attr($coords['lng']); ?>">
                    <button type="button" class="button remove-row">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button add-row" data-target="tour_map_points">+ Add Coordinate</button>
    </div>

    <!-- Inclusions -->
    <div class="tour-field">
        <label>Inclusions</label>
        <div id="tour_inclusions">
            <?php foreach ($inclusions as $index => $item): ?>
                <div class="tour-repeatable">
                    <input type="text" name="tour_inclusions[<?php echo $index; ?>]" value="<?php echo esc_attr($item); ?>" placeholder="Included facility">
                    <button type="button" class="button remove-row">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button add-row" data-target="tour_inclusions">+ Add Inclusion</button>
    </div>

    <!-- Exclusions -->
    <div class="tour-field">
        <label>Exclusions</label>
        <div id="tour_exclusions">
            <?php foreach ($exclusions as $index => $item): ?>
                <div class="tour-repeatable">
                    <input type="text" name="tour_exclusions[<?php echo $index; ?>]" value="<?php echo esc_attr($item); ?>" placeholder="Excluded facility">
                    <button type="button" class="button remove-row">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button add-row" data-target="tour_exclusions">+ Add Exclusion</button>
    </div>

    <!-- Itinerary -->
    <div class="tour-field">
        <label>Itinerary</label>
        <div id="tour_itinerary">
            <?php foreach ($itinerary as $index => $item): ?>
                <div class="tour-repeatable">
                    <input type="number" name="tour_itinerary[<?php echo $index; ?>][day]" placeholder="Day Number" value="<?php echo esc_attr($item['day']); ?>">
                    <input type="text" name="tour_itinerary[<?php echo $index; ?>][heading]" placeholder="Heading" value="<?php echo esc_attr($item['heading']); ?>">
                    <textarea name="tour_itinerary[<?php echo $index; ?>][description]" rows="2" placeholder="Description"><?php echo esc_textarea($item['description']); ?></textarea>
                    <input type="text" name="tour_itinerary[<?php echo $index; ?>][icon]" placeholder="Icon (URL or class)" value="<?php echo esc_attr($item['icon']); ?>">
                    <button type="button" class="button remove-row">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button add-row" data-target="tour_itinerary">+ Add Itinerary Item</button>
    </div>

    <script>
    jQuery(document).ready(function($){
        // Add new row
        $('.add-row').click(function(){
            const target = $(this).data('target');
            const container = $('#' + target);
            let index = container.children('.tour-repeatable').length;

            let html = '';
            if(target === 'tour_map_points'){
                html = `<div class="tour-repeatable">
                    <input type="text" name="tour_map_points[${index}][lat]" placeholder="Latitude">
                    <input type="text" name="tour_map_points[${index}][lng]" placeholder="Longitude">
                    <button type="button" class="button remove-row">Remove</button>
                </div>`;
            } else if(target === 'tour_inclusions' || target === 'tour_exclusions'){
                html = `<div class="tour-repeatable">
                    <input type="text" name="${target}[${index}]" placeholder="Enter item">
                    <button type="button" class="button remove-row">Remove</button>
                </div>`;
            } else if(target === 'tour_itinerary'){
                html = `<div class="tour-repeatable">
                    <input type="number" name="tour_itinerary[${index}][day]" placeholder="Day Number">
                    <input type="text" name="tour_itinerary[${index}][heading]" placeholder="Heading">
                    <textarea name="tour_itinerary[${index}][description]" rows="2" placeholder="Description"></textarea>
                    <input type="text" name="tour_itinerary[${index}][icon]" placeholder="Icon (URL or class)">
                    <button type="button" class="button remove-row">Remove</button>
                </div>`;
            }
            container.append(html);
        });

        // Remove row
        $(document).on('click', '.remove-row', function(){
            $(this).closest('.tour-repeatable').remove();
        });

        // Media uploader
        $('.upload-tour-image').click(function(e){
            e.preventDefault();
            const button = $(this);
            const input = $('#tour_image');
            const custom_uploader = wp.media({
                title: 'Select or Upload Image',
                button: { text: 'Use this image' },
                multiple: false
            }).on('select', function(){
                const attachment = custom_uploader.state().get('selection').first().toJSON();
                input.val(attachment.url);
            }).open();
        });
    });
    </script>
    <?php
}

/**
 * Save Meta Box Data
 */
function tour_save_meta_box_data($post_id) {
    if (!isset($_POST['tour_details_nonce']) || !wp_verify_nonce($_POST['tour_details_nonce'], basename(__FILE__))) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        '_tour_price'      => sanitize_text_field($_POST['tour_price'] ?? ''),
        '_tour_max_people' => sanitize_text_field($_POST['tour_max_people'] ?? ''),
        '_tour_image'      => esc_url_raw($_POST['tour_image'] ?? ''),
    ];
    foreach ($fields as $key => $val) {
        update_post_meta($post_id, $key, $val);
    }

    // Map Points
    if (isset($_POST['tour_map_points'])) {
        $clean = array_map(function($p){
            return [
                'lat' => sanitize_text_field($p['lat']),
                'lng' => sanitize_text_field($p['lng']),
            ];
        }, $_POST['tour_map_points']);
        update_post_meta($post_id, '_tour_map_points', $clean);
    }

    // Inclusions / Exclusions
    update_post_meta($post_id, '_tour_inclusions', array_filter(array_map('sanitize_text_field', $_POST['tour_inclusions'] ?? [])));
    update_post_meta($post_id, '_tour_exclusions', array_filter(array_map('sanitize_text_field', $_POST['tour_exclusions'] ?? [])));

    // Itinerary
    if (isset($_POST['tour_itinerary'])) {
        $clean = array_map(function($item){
            return [
                'day' => intval($item['day']),
                'heading' => sanitize_text_field($item['heading']),
                'description' => sanitize_textarea_field($item['description']),
                'icon' => sanitize_text_field($item['icon']),
            ];
        }, $_POST['tour_itinerary']);
        update_post_meta($post_id, '_tour_itinerary', $clean);
    }
}
add_action('save_post', 'tour_save_meta_box_data');
