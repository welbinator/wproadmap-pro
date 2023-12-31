<?php
/**
 * Ajax handling for voting functionality.
 */
function wp_roadmap_pro_handle_vote() {
    check_ajax_referer('wp-roadmap-vote-nonce', 'nonce');

    $post_id = intval($_POST['post_id']);
    $user_id = get_current_user_id();

    // Generate a unique key for non-logged-in user
    $user_key = $user_id ? 'user_' . $user_id : 'guest_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

    // Retrieve the current vote count
    $current_votes = get_post_meta($post_id, 'idea_votes', true) ?: 0;
    
    // Check if this user or guest has already voted
    $has_voted = get_post_meta($post_id, 'voted_' . $user_key, true);

    if ($has_voted) {
        // User or guest has voted, remove their vote
        $new_votes = max($current_votes - 1, 0);
        delete_post_meta($post_id, 'voted_' . $user_key);
    } else {
        // User or guest hasn't voted, add their vote
        $new_votes = $current_votes + 1;
        update_post_meta($post_id, 'voted_' . $user_key, true);
    }

    // Update the post meta with the new vote count
    update_post_meta($post_id, 'idea_votes', $new_votes);

    wp_send_json_success(array('new_count' => $new_votes, 'voted' => !$has_voted));

    wp_die();
}

add_action('wp_ajax_wp_roadmap_handle_vote', 'wp_roadmap_pro_handle_vote');
add_action('wp_ajax_nopriv_wp_roadmap_handle_vote', 'wp_roadmap_pro_handle_vote');

/**
 * Handle AJAX requests for ideas filter.
 */
function wp_roadmap_pro_filter_ideas() {
    check_ajax_referer('wp-roadmap-idea-filter-nonce', 'nonce');

    $filter_data = $_POST['filter_data'];
    $tax_query = array();

    $custom_taxonomies = get_option('wp_roadmap_custom_taxonomies', array());
    $display_taxonomies = array_merge(array('idea-tag'), array_keys($custom_taxonomies));

    // Retrieve color settings
    $pro_options = get_option('wp_roadmap_pro_settings');
    $vote_button_bg_color = isset($pro_options['vote_button_bg_color']) ? $pro_options['vote_button_bg_color'] : '#ff0000';
    $vote_button_text_color = isset($pro_options['vote_button_text_color']) ? $pro_options['vote_button_text_color'] : '#000000';
    $filter_tags_bg_color = isset($pro_options['filter_tags_bg_color']) ? $pro_options['filter_tags_bg_color'] : '#ff0000';
    $filter_tags_text_color = isset($pro_options['filter_tags_text_color']) ? $pro_options['filter_tags_text_color'] : '#000000';
    $filters_bg_color = isset($pro_options['filters_bg_color']) ? $pro_options['filters_bg_color'] : '#f5f5f5';



    foreach ($filter_data as $taxonomy => $data) {
        if (!empty($data['terms'])) {
            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $data['terms'],
                'operator' => ($data['matchType'] === 'all') ? 'AND' : 'IN'
            );
        }
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }
    $args = array(
        'post_type' => 'idea',
        'posts_per_page' => -1,
        'tax_query' => $tax_query
    );

     // Validate color settings
     $vote_button_bg_color = sanitize_hex_color($pro_options['vote_button_bg_color']);
     $vote_button_text_color = sanitize_hex_color($pro_options['vote_button_text_color']);
     $filter_tags_bg_color = sanitize_hex_color($pro_options['filter_tags_bg_color']);
     $filter_tags_text_color = sanitize_hex_color($pro_options['filter_tags_text_color']);

    $query = new WP_Query($args);
    if ($query->have_posts()) : ?>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 px-6 py-8">
            <?php while ($query->have_posts()) : $query->the_post();
                $idea_id = get_the_ID(); ?>
    
                <div class="wp-roadmap-idea border bg-card text-card-foreground rounded-lg shadow-lg overflow-hidden" data-v0-t="card">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold"><a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h2>
    
                        <p class="text-gray-500 mt-2 text-sm"><?php esc_html_e('Submitted on:', 'wp-roadmap'); ?> <?php echo get_the_date(); ?></p>
                        <div class="flex flex-wrap space-x-2 mt-2">
                            <?php $terms = wp_get_post_terms($idea_id, $display_taxonomies);
                            foreach ($terms as $term) :
                                $term_link = get_term_link($term);
                                if (!is_wp_error($term_link)) : ?>
                                    <a href="<?php echo esc_url($term_link); ?>" class="inline-flex items-center border font-semibold bg-blue-500 text-white px-3 py-1 rounded-full text-sm" style="background-color: <?php echo esc_attr($filter_tags_bg_color); ?>; color: <?php echo esc_attr($filter_tags_text_color); ?>;"><?php echo esc_html($term->name); ?></a>
                                <?php endif;
                            endforeach; ?>
                        </div>
    
                        
                        <p class="text-gray-700 mt-4"><?php echo get_the_excerpt(); ?></p>
    
                        <div class="flex items-center justify-between mt-6">
                            <a class="text-blue-500 hover:underline" href="<?php echo esc_url(get_permalink()); ?>" rel="ugc">Read More</a>
                            <div class="flex items-center idea-vote-box" data-idea-id="<?php echo $idea_id; ?>">
                            <button class="inline-flex items-center justify-center text-sm font-medium h-10 bg-blue-500 text-white px-4 py-2 rounded-lg idea-vote-button" style="background-color: <?php echo esc_attr($vote_button_bg_color); ?>!important;background-image: none!important;color: <?php echo esc_attr($vote_button_text_color); ?>!important;">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="w-5 h-5 mr-1"
                                >
                                    <path d="M7 10v12"></path>
                                    <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z"></path>
                                </svg>
                                Vote
                            </button>
                            
                        <div class="text-gray-600 ml-2 idea-vote-count"><?php echo $vote_count ?? 0; ?> votes</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p><?php esc_html_e('No ideas found.', 'wp-roadmap'); ?></p>
    <?php endif; 

    wp_reset_postdata();
    wp_die();
}


add_action('wp_ajax_filter_ideas', 'wp_roadmap_pro_filter_ideas');
add_action('wp_ajax_nopriv_filter_ideas', 'wp_roadmap_pro_filter_ideas');



// Handles the AJAX request for deleting a custom taxonomy
function wp_roadmap_pro_handle_delete_custom_taxonomy() {
    error_log('Received AJAX request: ' . print_r($_POST, true));
    // Check if the nonce and taxonomy parameters are set
    if (!isset($_POST['nonce'], $_POST['taxonomy'])) {
        wp_send_json_error(array('message' => __('Missing parameters.', 'wp-roadmap')));
        return;
    }

    // Sanitize and assign the taxonomy
    $taxonomy = sanitize_text_field($_POST['taxonomy']);

    // Verify the nonce
    if (!wp_verify_nonce($_POST['nonce'], 'wp_roadmap_delete_taxonomy_nonce')) {
        wp_send_json_error(array('message' => __('Nonce verification failed.', 'wp-roadmap')));
        return;
    }

    // Fetch the custom taxonomies
    $custom_taxonomies = get_option('wp_roadmap_custom_taxonomies', array());

    // Check if the taxonomy exists and delete it
    if (array_key_exists($taxonomy, $custom_taxonomies)) {
        unset($custom_taxonomies[$taxonomy]);
        update_option('wp_roadmap_custom_taxonomies', $custom_taxonomies);
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => __('Taxonomy not found.', 'wp-roadmap')));
    }
}
add_action('wp_ajax_delete_custom_taxonomy', 'wp_roadmap_pro_handle_delete_custom_taxonomy');


// Handles the AJAX request for deleting selected terms
function wp_roadmap_pro_handle_delete_selected_terms() {
    check_ajax_referer('wp_roadmap_delete_terms_nonce', 'nonce');

    $taxonomy = sanitize_text_field($_POST['taxonomy']);
    $terms = array_map('intval', (array) $_POST['terms']);
    $deletion_successful = true;

    foreach ($terms as $term_id) {
        $deleted_term = wp_delete_term($term_id, $taxonomy);
        if (is_wp_error($deleted_term)) {
            $deletion_successful = false;
            break; // Exit the loop if any deletion fails
        }
    }

    if ($deletion_successful) {
        wp_send_json_success(array('message' => 'Term deleted successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Error occurred while deleting term.'));
    }

    wp_die(); // This is important to terminate immediately and return a proper response
}
add_action('wp_ajax_delete_selected_terms', 'wp_roadmap_pro_handle_delete_selected_terms');


function wp_roadmap_pro_update_idea_status() {
    check_ajax_referer('wp-roadmap-admin-frontend-nonce', 'nonce');

    $idea_id = isset($_POST['idea_id']) ? intval($_POST['idea_id']) : 0;
    $statuses = isset($_POST['statuses']) ? json_decode(stripslashes($_POST['statuses']), true) : array();

    if ($idea_id && !empty($statuses)) {
        // Remove all existing status terms from the post
        $current_terms = wp_get_post_terms($idea_id, 'status', array('fields' => 'ids'));
        foreach ($current_terms as $term_id) {
            wp_remove_object_terms($idea_id, $term_id, 'status');
        }

        // Add each new status term
        foreach ($statuses as $status_slug) {
            $term = get_term_by('slug', $status_slug, 'status');
            if ($term && !is_wp_error($term)) {
                wp_add_object_terms($idea_id, $term->term_id, 'status');
            }
        }

        // Check current terms after setting
        $current_terms = wp_get_post_terms($idea_id, 'status', array('fields' => 'slugs'));

        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid data');
    }
}
add_action('wp_ajax_update_idea_status', 'wp_roadmap_pro_update_idea_status');


function load_ideas_for_status() {
    $pro_options = get_option('wp_roadmap_pro_settings');
    $vote_button_bg_color = isset($pro_options['vote_button_bg_color']) ? $pro_options['vote_button_bg_color'] : '#ff0000';
    $vote_button_text_color = isset($pro_options['vote_button_text_color']) ? $pro_options['vote_button_text_color'] : '#000000';
    $filter_tags_bg_color = isset($pro_options['filter_tags_bg_color']) ? $pro_options['filter_tags_bg_color'] : '#ff0000';
    $filter_tags_text_color = isset($pro_options['filter_tags_text_color']) ? $pro_options['filter_tags_text_color'] : '#000000';
    $filters_bg_color = isset($pro_options['filters_bg_color']) ? $pro_options['filters_bg_color'] : '#f5f5f5';

    
    check_ajax_referer('roadmap_nonce', 'nonce');

    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

    $args = array(
        'post_type' => 'idea',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'status',
                'field'    => 'slug',
                'terms'    => $status,
            ),
        ),
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $idea_id = get_the_ID();
            $tags = wp_get_post_terms($idea_id, 'idea-tag', array('fields' => 'names'));
            $vote_count = get_post_meta($idea_id, 'idea_votes', true) ?: '0';
            
            
            ?>

            <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-2xl font-semibold leading-none tracking-tight">
                        <a href="<?php echo get_permalink($idea_id); ?>"><?php echo esc_html(get_the_title()); ?></a>
                    </h3>

                    <?php if (!empty($tags)) : ?>
                        <div class="flex flex-wrap space-x-2 mt-2">
                            <?php foreach ($tags as $tag) : ?>
                                <?php $tag_link = get_term_link($tag, 'idea-tag'); // Get the term link ?>
                                <?php if (!is_wp_error($tag_link)) : // Check if the link is valid ?>
                                    <a href="<?php echo esc_url($tag_link); ?>" class="inline-flex items-center border font-semibold bg-blue-500 px-3 py-1 rounded-full text-sm" style="background-color: <?php echo esc_attr($filter_tags_bg_color); ?>;color: <?php echo esc_attr($filter_tags_text_color); ?>;">
                                        <?php echo esc_html($tag); ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <p><?php echo get_the_excerpt(); ?></p>
                </div>

                <div class="p-6 flex items-center idea-vote-box" data-idea-id="<?php echo $idea_id; ?>">
                <a class="text-blue-500 hover:underline mr-4" href="<?php the_permalink(); ?>" rel="ugc">Read More</a>
                    <button class="inline-flex items-center justify-center text-sm font-medium h-10 bg-blue-500 px-4 py-2 rounded-lg idea-vote-button" style="background-color: <?php echo esc_attr($vote_button_bg_color); ?>!important;background-image: none!important;color: <?php echo esc_attr($vote_button_text_color); ?>!important;">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-5 h-5 mr-1"
                        >
                            <path d="M7 10v12"></path>
                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z"></path>
                        </svg>
                        Vote
                    </button>
                    <div class="text-gray-600 ml-2 idea-vote-count flex gap-4"><?php echo $vote_count; ?> votes </div>
                </div>
            </div>

            <?php
        }
    } else {
        echo '<p>No ideas found for this status.</p>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();
    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_load_ideas_for_status', 'load_ideas_for_status');
add_action('wp_ajax_nopriv_load_ideas_for_status', 'load_ideas_for_status');





