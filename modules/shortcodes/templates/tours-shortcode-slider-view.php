<div class="grid-12">
    <div class="row animated fadeInUp">
        <div class="card-carousel">
            <?php
            while ($tour_query->have_posts()) {
                $tour_query->the_post();

                $post_id   = get_the_ID();
                $title     = get_the_title();
                $permalink = get_permalink($post_id);
                $image_url = get_the_post_thumbnail_url($post_id, 'large');
                $location_terms = wp_get_post_terms($post_id, 'location', array('fields' => 'names'));
                $location_name  = ! empty($location_terms) ? implode(', ', $location_terms) : '';

                if (empty($image_url)) {
                    $image_url = esc_url('/images/no-image.png');
                }
            ?>
                <div class="grid-3">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <div class="card culture-left">
                            <div class="zoom" style="background-image: url('<?php echo esc_url($image_url); ?>'); background-size: cover; background-position: 50% 50%;"></div>
                            <div class="card-content content-bl">
                                <h4><?php echo esc_html($title); ?></h4>
                                <p>
                                    <i class="icon-cal"></i>
                                    <?php echo esc_html(get_post_meta($post_id, '_tour_nights', true) ?: '0'); ?> nights
                                    <i class="icon-location"></i>
                                    <?php echo esc_html($location_name); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div>