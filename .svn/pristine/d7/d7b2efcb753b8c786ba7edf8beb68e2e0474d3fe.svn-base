<?php

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */
function wpsbg_optionsframework_option_name() {
    $wpsbg_optionsframework_settings = get_option('wpsbg_optionsframework');
    $wpsbg_optionsframework_settings['id'] = WPSBG_OPTIONS_FRAMEWORK_TAG;
    update_option('wpsbg_optionsframework', $wpsbg_optionsframework_settings);
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *  
 */
function wpsbg_optionsframework_options() {
    $colorboxpath = WPSTORYBOARDGALLERY_URL . 'colorbox/';
    $options = array();

    $options[] = array('name' => __('Basic Settings', 'wpstoryboardgallery'),
        'type' => 'heading');

    $options[] = array('name' => __('Animation Time (ms)', 'wpstoryboardgallery'),
        'desc' => __('', 'wpstoryboardgallery'),
        'id' => 'wpstoryboardgallery_animate_time',
        'std' => '2000',
        'class' => 'mini',
        'type' => 'text');

    $options[] = array('name' => __('Advance Gallery By', 'wpstoryboardgallery'),
        'id' => 'wpstoryboardgallery_advance_slide_num',
        'std' => '1',
        'type' => 'select',
        'options' => array(
            '1' => '1',
            '2' => '2'
        )
    );

    $options[] = array('name' => __('Gallery Easing', 'wpstoryboardgallery'),
        'id' => 'wpstoryboardgallery_gallery_easing',
        'std' => 'easeInOutQuad',
        'type' => 'select',
        'options' => array(
            'linear' => 'linear',
            'easeInOutQuad' => 'easeInOutQuad',
            'easeInOutCubic' => 'easeInOutCubic',
            'easeInOutQuart' => 'easeInOutQuart',
            'easeInOutQuint' => 'easeInOutQuint',
            'easeInOutSine' => 'easeInOutSine',
            'easeInOutExpo' => 'easeInOutExpo',
            'easeInOutElastic' => 'easeInOutElastic',
            'easeInOutElastic' => 'easeInOutElastic',
            'easeInOutCirc' => 'easeInOutCirc',
            'easeInOutBack' =>  'easeInOutBack',
            'easeInOutBounce' =>  'easeInOutBounce'
        )
    );

    $post_types_default = array('post' => '1', 'page' => '1');
    $post_types = get_post_types();

    unset($post_types['attachment']);
    unset($post_types['revision']);
    unset($post_types['nav_menu_item']);
    unset($post_types['mediapage']);

    foreach ($post_types as $k => $v) {
        $post_types[$k] = ucwords($v);
    }

    $options[] = array('name' => __('Post Types', 'wpstoryboardgallery'),
        'desc' => __('What post types do you want to have galleries enabled', 'wpstoryboardgallery'),
        'id' => 'wpstoryboardgallery_post_types',
        'std' => $post_types_default,
        'options' => $post_types,
        'type' => 'multicheck');

    $options[] = array('name' => __('Please Donate', 'wpstoryboardgallery'),
        'desc' => __('Alot of time and effort went into making this plugin, all donations are hugely appreciated.', 'wpstoryboardgallery'),
        'url' => 'http://maca134.co.uk/blog/wp-simple-galleries/',
        'type' => 'donate');

    return $options;
}