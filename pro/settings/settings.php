<?php
function wp_roadmap_pro_settings_validate($input) {
    // Initialize an array to hold the validated settings
    $validated_settings = [];

    // Validate 'default_status_term'
    $status_terms = get_terms(['taxonomy' => 'status', 'hide_empty' => false]);
    $status_slugs = wp_list_pluck($status_terms, 'slug');
    if (in_array($input['default_status_term'], $status_slugs)) {
        $validated_settings['default_status_term'] = $input['default_status_term'];
    } else {
        add_settings_error(
            'default_status_term',
            'invalid_status_term',
            'Invalid status selected for Default Status Term for New Ideas.',
            'error'
        );
    }

    // Validate 'default_wp_post_status'
    $allowed_statuses = ['publish', 'pending', 'draft'];
    if (in_array($input['default_wp_post_status'], $allowed_statuses)) {
        $validated_settings['default_wp_post_status'] = $input['default_wp_post_status'];
    } else {
        add_settings_error(
            'default_wp_post_status',
            'invalid_wp_post_status',
            'Invalid WordPress post status selected.',
            'error'
        );
    }

    // Validate 'default_wp_post_status'
    $allowed_statuses = ['publish', 'pending', 'draft'];
    if (in_array($input['default_wp_post_status'], $allowed_statuses)) {
        $validated_settings['default_wp_post_status'] = $input['default_wp_post_status'];
    } else {
        add_settings_error(
            'default_wp_post_status',
            'invalid_wp_post_status',
            'Invalid WordPress post status selected.',
            'error'
        );
    }
    // Validate 'single_idea_template'
    $allowed_templates = ['plugin', 'page'];
    if (in_array($input['single_idea_template'], $allowed_templates)) {
        $validated_settings['single_idea_template'] = $input['single_idea_template'];

        // Validate 'single_idea_page' if 'single_idea_template' is 'page'
        if ($input['single_idea_template'] === 'page') {
            $page_id = $input['single_idea_page'];
            if (!empty($page_id) && get_post($page_id)) {
                $validated_settings['single_idea_page'] = $page_id;
            } else {
                add_settings_error(
                    'single_idea_page',
                    'invalid_single_idea_page',
                    'Invalid page selected for Single Idea.',
                    'error'
                );
            }
        }
    } else {
        add_settings_error(
            'single_idea_template',
            'invalid_single_idea_template',
            'Invalid template selected for Single Idea.',
            'error'
        );
    }

     // Validate 'allow_comments'
     $validated_settings['allow_comments'] = !empty($input['allow_comments']) && $input['allow_comments'] == '1' ? 1 : 0;
    
     // Validate 'hide_custom_idea_heading'
    $validated_settings['hide_custom_idea_heading'] = !empty($input['hide_custom_idea_heading']) && $input['hide_custom_idea_heading'] == '1' ? 1 : 0;

    // Validate 'custom_idea_heading'
    if (!empty($input['custom_idea_heading'])) {
        $validated_settings['custom_idea_heading'] = sanitize_text_field($input['custom_idea_heading']);
    } else {
        $validated_settings['custom_idea_heading'] = '';
    }
    
    // Validate 'hide_display_ideas_heading'
    $validated_settings['hide_display_ideas_heading'] = isset($input['hide_display_ideas_heading']) && $input['hide_display_ideas_heading'] == '1' ? '1' : '0';

    // Validate 'custom_display_ideas_heading'
    if (!empty($input['custom_display_ideas_heading'])) {
        $validated_settings['custom_display_ideas_heading'] = sanitize_text_field($input['custom_display_ideas_heading']);
    } else {
        $validated_settings['custom_display_ideas_heading'] = '';
    }

     // Validate 'vote_button_bg_color'
     $validated_settings['vote_button_bg_color'] = isset($input['vote_button_bg_color']) ? sanitize_hex_color($input['vote_button_bg_color']) : '#0000ff';

     // Validate 'vote_button_text_color'
     $validated_settings['vote_button_text_color'] = isset($input['vote_button_text_color']) ? sanitize_hex_color($input['vote_button_text_color']) : '#000000';
 
     // Validate 'filter_tags_bg_color'
     $validated_settings['filter_tags_bg_color'] = isset($input['filter_tags_bg_color']) ? sanitize_hex_color($input['filter_tags_bg_color']) : '#0000ff';
 
     // Validate 'filter_tags_text_color'
     $validated_settings['filter_tags_text_color'] = isset($input['filter_tags_text_color']) ? sanitize_hex_color($input['filter_tags_text_color']) : '#000000';
 
     // Validate 'filters_bg_color'
     $validated_settings['filters_bg_color'] = isset($input['filters_bg_color']) ? sanitize_hex_color($input['filters_bg_color']) : '#f5f5f5';
 
     // Validate 'tabs_container_bg_color'
     $validated_settings['tabs_container_bg_color'] = isset($input['tabs_container_bg_color']) ? sanitize_hex_color($input['tabs_container_bg_color']) : '#dddddd';
 
     // Validate 'tabs_button_bg_color'
     $validated_settings['tabs_button_bg_color'] = isset($input['tabs_button_bg_color']) ? sanitize_hex_color($input['tabs_button_bg_color']) : '#ffffff';
 
     // Validate 'tabs_text_color'
     $validated_settings['tabs_text_color'] = isset($input['tabs_text_color']) ? sanitize_hex_color($input['tabs_text_color']) : '#000000';
     // Add more validation for other settings as needed

    // Return the array of validated settings
    return $validated_settings;
}

function wp_roadmap_pro_register_settings() {
    register_setting('wp_roadmap_pro_settings', 'wp_roadmap_pro_settings', 'wp_roadmap_pro_settings_validate');
}

add_action('admin_init', 'wp_roadmap_pro_register_settings');



