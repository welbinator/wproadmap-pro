<?php
/**
 * Adds the functionality to enable/disable comments on ideas
 */

// Hook into the settings page of the free version to add the comments setting
add_filter('wp_roadmap_enable_comments_setting', 'wp_roadmap_pro_enable_comments');

function wp_roadmap_pro_enable_comments() {
    $pro_options = get_option('wp_roadmap_pro_settings', []); // Retrieve the current settings
    $allow_comments = isset($pro_options['allow_comments']) ? $pro_options['allow_comments'] : '';
    echo '<input type="checkbox" name="wp_roadmap_pro_settings[allow_comments]" value="1"' . checked(1, $allow_comments, false) . '/>';
}