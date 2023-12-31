<?php
// Include this file in your plugin's main file or functions.php of your theme.

function wp_roadmap_pro_register_roadmap_tabs_block() {
    // Register the block script
    wp_register_script(
        'wp-roadmap-pro-roadmap-tabs-block',
        plugin_dir_url(__FILE__) . '../../build/roadmap-tabs-block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-api-fetch')
    );

    // Register the block
    register_block_type('wp-roadmap-pro/roadmap-tabs-block', array(
        'editor_script' => 'wp-roadmap-pro-roadmap-tabs-block',
        'render_callback' => 'wp_roadmap_pro_roadmap_tabs_block_render',
    ));
}

add_action('init', 'wp_roadmap_pro_register_roadmap_tabs_block');

// The render callback function for the block
function wp_roadmap_pro_roadmap_tabs_block_render($attributes) {
    if (!isset($attributes['selectedStatuses']) || !is_array($attributes['selectedStatuses'])) {
        return '<p>No statuses selected.</p>';
    }

    $selected_statuses = array_keys(array_filter($attributes['selectedStatuses']));
    if (empty($selected_statuses)) {
        return '<p>No statuses selected.</p>';
    }

    // Convert slugs back to names for display
    $statuses = array_map(function($slug) {
        $term = get_term_by('slug', $slug, 'status');
        return $term ? $term->name : $slug;
    }, $selected_statuses);

    $pro_options = get_option('wp_roadmap_pro_settings');
    $vote_button_bg_color = !empty($pro_options['vote_button_bg_color']) ? $pro_options['vote_button_bg_color'] : '';
    $vote_button_text_color = !empty($pro_options['vote_button_text_color']) ? $pro_options['vote_button_text_color'] : '';
    $filter_tags_bg_color = !empty($pro_options['filter_tags_bg_color']) ? $pro_options['filter_tags_bg_color'] : '';
    $filter_tags_text_color = !empty($pro_options['filter_tags_text_color']) ? $pro_options['filter_tags_text_color'] : '';
    $filters_bg_color = !empty($pro_options['filters_bg_color']) ? $pro_options['filters_bg_color'] : '';
    $tabs_container_bg_color = !empty($pro_options['tabs_container_bg_color']) ? $pro_options['tabs_container_bg_color'] : '#dddddd';
    $tabs_text_color = !empty($pro_options['tabs_text_color']) ? $pro_options['tabs_text_color'] : '#000000';
    $tabs_button_bg_color = !empty($pro_options['tabs_button_bg_color']) ? $pro_options['tabs_button_bg_color'] : '#ffffff';

    ob_start();
    ?>

    <!-- Tabbed interface -->
    <div dir="ltr" data-orientation="horizontal" class="w-full border-b roadmap-tabs-wrapper">
        <div style="background-color: <?php echo esc_attr($tabs_container_bg_color); ?>;" role="tablist" aria-orientation="horizontal" class="h-9 items-center justify-center rounded-lg bg-muted p-1 text-muted-foreground flex gap-4 px-2 py-4 scrollbar-none roadmap-tabs">
            <?php foreach ($statuses as $status): ?>
                <button style="color: <?php echo esc_attr($tabs_text_color); ?>; background-color: <?php echo esc_attr($tabs_button_bg_color); ?>;" type="button" role="tab" aria-selected="true" data-state="inactive" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1 text-sm font-medium roadmap-tab" data-status="<?php echo esc_attr(strtolower(str_replace(' ', '-', $status))); ?>">
                    <?php echo esc_html($status); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mt-2 roadmap-ideas-container">
            <!-- Ideas will be loaded here via JavaScript -->
        </div>
    </div>

    <script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('.roadmap-tab');
    var ideasContainer = document.querySelector('.roadmap-ideas-container');
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    var nonce = '<?php echo wp_create_nonce('roadmap_nonce'); ?>';

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var status = this.getAttribute('data-status');
            loadIdeas(status);
        });
    });

    function loadIdeas(status) {
        var formData = new FormData();
        formData.append('action', 'load_ideas_for_status');
        formData.append('status', status);
        formData.append('nonce', nonce);

        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.html) {
                ideasContainer.innerHTML = data.data.html;
            } else {
                ideasContainer.innerHTML = '<p>Error: Invalid response format.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading ideas:', error);
            ideasContainer.innerHTML = '<p>Error loading ideas.</p>';
        });
    }

    // Automatically load ideas for the first tab
    if (tabs.length > 0) {
        tabs[0].click();
    }
});
</script>


    <?php
    return ob_get_clean();
}

