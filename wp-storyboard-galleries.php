<?php
/*
  Plugin Name: WP Storyboard Galleries
  Version: 0.1
  Description: A plugin that adds an storyboard gallery to each post and page.
  Plugin URI: http://maca134.co.uk/blog/wp-simple-galleries/
  Author: Matthew McConnell
  Author URI: http://maca134.co.uk/
 */

$plugin_dir = plugin_basename(__FILE__);
$plugin_dir = str_replace(basename($plugin_dir), '', $plugin_dir);
define('WPSTORYBOARDGALLERY_DIR', WP_PLUGIN_DIR . '/' . $plugin_dir);
define('WPSTORYBOARDGALLERY_URL', WP_PLUGIN_URL . '/' . $plugin_dir);
define('WPSTORYBOARDGALLERY_DEBUG', false);
define('WPSTORYBOARDGALLERY_VERSION', 0.1);


define('WPSBG_OPTIONS_FRAMEWORK_URL', WPSTORYBOARDGALLERY_URL . 'admin/');
define('WPSBG_OPTIONS_FRAMEWORK_DIRECTORY', WPSTORYBOARDGALLERY_DIR . 'admin/');
define('WPSBG_OPTIONS_FRAMEWORK_NAME', 'Storyboard');
define('WPSBG_OPTIONS_FRAMEWORK_TAG', 'wpstoryboardgalleries');
require_once (WPSBG_OPTIONS_FRAMEWORK_DIRECTORY . 'options-framework.php');

class wpstoryboardgalleries {
	private static $instance;
    private $admin_thumbnail_size = 109;
    private $thumbnail_size_w = 150;
    private $thumbnail_size_h = 150;

    public static function forge() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    private function __construct() {
        add_image_size('wpstoryboardgalleries_admin_thumb', $this->admin_thumbnail_size, $this->admin_thumbnail_size, true);
        add_image_size('wpstoryboardgalleries_thumb', $this->thumbnail_size_w, $this->thumbnail_size_h, true);
        if (is_admin()) {
            add_action('admin_print_scripts-post.php', array(&$this, 'admin_print_scripts'));
	        add_action('admin_print_scripts-post-new.php', array(&$this, 'admin_print_scripts'));
	        add_action('admin_print_styles', array(&$this, 'admin_print_styles'));
	        add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
            add_action('admin_init', array(&$this, 'add_meta_boxes'), 1);
            add_action('save_post', array(&$this, 'save_post_meta'));
            add_action('wp_ajax_wpstoryboardgalleries_get_pages', array(&$this, 'ajax_get_pages'));
            add_action('wp_ajax_wpstoryboardgalleries_get_thumbnail', array(&$this, 'ajax_get_thumbnail'));
            add_action('wp_ajax_wpstoryboardgalleries_get_all_thumbnail', array(&$this, 'ajax_get_all_attachments'));
        } else {
	        add_action('wp_print_scripts', array(&$this, 'print_scripts'));
	        add_action('wp_print_styles', array(&$this, 'print_styles'));
	        add_filter('the_content', array(&$this, 'output_gallery'));	
        }
    }

    public function admin_print_scripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('wpstoryboardgalleries-admin-scripts', WPSTORYBOARDGALLERY_URL . 'js/wp-storyboard-galleries-admin.js', array('jquery', 'jquery-ui-autocomplete'), WPSTORYBOARDGALLERY_VERSION);
    }

    public function admin_print_styles() {
        wp_enqueue_style('wpstoryboardgalleries-admin-style', WPSTORYBOARDGALLERY_URL . 'wp-storyboard-galleries-admin.css', array(), WPSTORYBOARDGALLERY_VERSION);
    }

    public function add_meta_boxes() {
        $post_types = wpsbg_of_get_option('wpstoryboardgalleries_post_types');
        $post_types = ($post_types !== false) ? $post_types : array('page' => '1', 'post' => '1');
        
        foreach ($post_types as $type => $value) {
            if ($value == '1') {
                add_meta_box(
                        'wpstoryboardgalleries', __('Storyboard Gallery', 'wpstoryboardgalleries'), array(&$this, 'inner_custom_box'), $type
                );
            }
        }
    }

    public function inner_custom_box($post) {
    	$gallery = get_post_meta($post->ID, 'wpstoryboardgalleries_gallery', true);
    	$autoopen = get_post_meta($post->ID, 'wpstoryboardgalleries_auto_open', true);

        wp_nonce_field(basename(__FILE__), 'wpstoryboardgalleries_gallery_nonce');
        ?>
        <script type="text/javascript">
            var POST_ID = <?php echo $post->ID; ?>;
        </script>
        <input style="width: auto;" id="wpstoryboardgalleries_upload_button" class="upload_button button" type="button" value="<?php echo __('Upload Image', 'wpstoryboardgalleries'); ?>" rel="" />
        <input id="wpstoryboardgalleries_add_attachments_button" class="button" type="button" value="<?php echo __('Add All Attachments', 'wpstoryboardgalleries'); ?>" rel="" />
        <input id="wpstoryboardgalleries_delete_all_button" class="button" type="button" value="<?php echo __('Delete All', 'wpstoryboardgalleries'); ?>" rel="" />
        <label>
        	<input value="1" type="checkbox" name="wpstoryboardgalleries_auto_open" id="wpstoryboardgalleries_auto_open" <?php if ($autoopen == 1) { ?>checked="checked"<?php } ?>> Auto Open Gallery
        </label>
        <div id="wpstoryboardgalleries_container">
            <ul id="wpstoryboardgalleries_thumbs" class="clearfix"><?php
        if (is_array($gallery) && count($gallery) > 0) {
            foreach ($gallery as $img) {
                echo $this->admin_thumb($img['image'], $img['caption'], $img['link'], $img['text']);
            }
        }
        ?></ul>
        </div>
        <?php
    }

    public function ajax_get_thumbnail() {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        echo $this->admin_thumb($_POST['imageid'], '', '', '');
        die;
    }
 	
 	public function ajax_get_all_attachments() {
        $post_id = $_POST['post_id'];
        $included = (isset($_POST['included'])) ? $_POST['included'] : array();

        $attachments = get_children(array(//do only if there are attachments of these qualifications
            'post_parent' => $post_id,
            'post_type' => 'attachment',
            'numberposts' => -1,
            'order' => 'ASC',
            'post_mime_type' => 'image', //MIME Type condition
                )
        );
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        if (count($attachments) > 0) {
            foreach ($attachments as $a) {
                if (!in_array($a->ID, $included)) {
                    echo $this->admin_thumb($a->ID, '', '', '');
                }
            }
        }
        die;
    }

	public function ajax_get_pages() {
        $term = $_POST['term'];
        $post_types = get_post_types();

        unset($post_types['attachment']);
        unset($post_types['revision']);
        unset($post_types['nav_menu_item']);
        unset($post_types['mediapage']);

        $the_query = new WP_Query(array(
            's' => $term,
            'posts_per_page' => 10,
            'post_type' => $post_types
            ));
        
        $data = array();
        while($the_query->have_posts()):
            $the_query->the_post();
        	$data[] = array(
        		'label' => get_the_title() . ' (' . get_post_type() . ')',
        		'value' => get_permalink()
        		);
        endwhile;
        wp_reset_postdata();
        header('Content-type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        echo json_encode($data);
        die;
    }

    public function save_post_meta($id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return '';
        }
        if (!isset($_POST['wpstoryboardgalleries_gallery_nonce']) || !wp_verify_nonce($_POST['wpstoryboardgalleries_gallery_nonce'], basename(__FILE__)))
            return (isset($post_id)) ? $post_id : 0;

        $images = (isset($_POST['wpstoryboardgalleries_thumb'])) ? $_POST['wpstoryboardgalleries_thumb'] : array();
        $captions = (isset($_POST['wpstoryboardgalleries_caption'])) ? $_POST['wpstoryboardgalleries_caption'] : array();
        $links = (isset($_POST['wpstoryboardgalleries_link'])) ? $_POST['wpstoryboardgalleries_link'] : array();
        $texts = (isset($_POST['wpstoryboardgalleries_text'])) ? $_POST['wpstoryboardgalleries_text'] : array();

        $gallery = array();
        if (count($images) > 0) {
            foreach ($images as $i => $img) {
            	$caption = $captions[$i];
            	$link = $links[$i];
                $text = $texts[$i];
                if (is_numeric($img))
                    $gallery[] = array(
                    	'image' => $img,
                    	'caption' => $caption,
                    	'link' => $link,
                        'text' => $text
                    	);
            }
        }

        $autoopen = (isset($_POST['wpstoryboardgalleries_auto_open'])) ? 1 : 0;

        update_post_meta($id, 'wpstoryboardgalleries_gallery', $gallery);
        update_post_meta($id, 'wpstoryboardgalleries_auto_open', $autoopen);
        return $id;
    }

	public function print_scripts() {
        wp_enqueue_script('jquery-easing', WPSTORYBOARDGALLERY_URL . 'js/jquery.easing.1.3.min.js', array('jquery'));
        wp_enqueue_script('jquery-pagination', WPSTORYBOARDGALLERY_URL . 'js/jquery.pagination.js', array('jquery'));
        wp_enqueue_script('wpstoryboardgalleries-scripts', WPSTORYBOARDGALLERY_URL . 'js/wp-storyboard-galleries.js');
	}

	public function print_styles() {
	    wp_enqueue_style('wpstoryboardgalleries-style', WPSTORYBOARDGALLERY_URL . 'wp-storyboard-galleries.css');
	}
	
	private function admin_thumb($id, $caption, $link, $text) {
        $image = wp_get_attachment_image_src($id, 'wpstoryboardgalleries_admin_thumb', true);
        ?>
        <li class="group">
        	<img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />
        	<div class="wpstoryboardgalleries_thumb_form_container">
        		<input type="text" name="wpstoryboardgalleries_caption[]" value="<?php echo $caption; ?>" placeholder="Caption">
        		<label><input type="checkbox" name="wpstoryboardgalleries_link_panel[]" class="wpstoryboardgalleries_link_panel" value="1" <?php if (!empty($link)) { ?>checked="checked"<?php } ?>> Link Panel</label>
        		<input type="text" name="wpstoryboardgalleries_link[]" class="wpstoryboardgalleries_link<?php if (!empty($link)) { ?> showfield<?php } ?> linkonlyinput" value="<?php echo $link; ?>" placeholder="URL">
                <input type="text" name="wpstoryboardgalleries_text[]" class="wpstoryboardgalleries_text<?php if (!empty($link)) { ?> showfield<?php } ?> linkonlyinput" value="<?php echo $text; ?>" placeholder="Text">
        	</div>
        	<a href="#" class="wpstoryboardgalleries_remove"><?php echo __('Remove', 'wpstoryboardgalleries'); ?></a>
        	<input type="hidden" name="wpstoryboardgalleries_thumb[]" value="<?php echo $id; ?>" />
        </li>
        <?php
    }

    public function output_gallery($content) {
        global $post;
        if (!is_single()) {
            return $content;
        }
        $timthumb = WPSTORYBOARDGALLERY_URL . 'timthumb.php?src=%s&w=%d&q=%d';
        $mobile_width = 300;
        $medium_width = 600;
        $low_width = 150;

        $images = get_post_meta($post->ID, 'wpstoryboardgalleries_gallery', true);
        $autoopen = get_post_meta($post->ID, 'wpstoryboardgalleries_auto_open', true);

        $advanceslide = wpsbg_of_get_option('wpstoryboardgallery_advance_slide_num', '1');

        if (!is_array($images) || count($images) < 1) {
            return $content;
        }

        $gallery = array();
        foreach ($images as $img) {
            $image = wp_get_attachment_image_src($img['image'], 'full', true);
            $gallery[] = array(
                'image' => $image[0],
                'caption' => htmlspecialchars($img['caption']),
                'link' => $img['link'],
                'text' => htmlspecialchars($img['text']),
                'size' => array($image[1], $image[2])
                );
        }
        
        ob_start();
        require 'gallery.php';
        $gallery = ob_get_clean();
        return $content . $gallery;
    }
}


global $wpstoryboardgalleries;
$wpstoryboardgalleries = wpstoryboardgalleries::forge();
