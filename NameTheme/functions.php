<?php

register_nav_menus( array(
	'top' => 'Верхнее меню',
	'left' => 'Нижнее'
) );


add_theme_support('post-thumbnails');
set_post_thumbnail_size(254, 190);

if ( function_exists('register_sidebar') ) register_sidebar();

// Style & Scripts
if (!is_admin()) {
	function theme_styles() {
	    wp_enqueue_style( 'congif', get_template_directory_uri() . '/css/template_cfg_css.css');
	    wp_enqueue_style( 'main', get_template_directory_uri() . '/style.css');
	}
	function theme_js() {
	    wp_enqueue_script( 'map', '//api-maps.yandex.ru/2.1/?apikey=c96eb908-6efc-4537-90fa-c0d2b2fdfb97&lang=ru_RU', array('jquery'), '', true );
	    wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js', array('jquery'), '', true );

	}
	add_action( 'wp_enqueue_scripts', 'theme_styles' );
	add_action( 'wp_enqueue_scripts', 'theme_js' );
}

add_action('init', 'my_custom_init');
function my_custom_init(){
    register_post_type('units', array(
        'labels'             => array(
            'name'               => 'Юниты', // Основное название типа записи
            'singular_name'      => 'Юниты', // отдельное название записи типа Book
            'add_new'            => 'Добавить новую',
            'add_new_item'       => 'Добавить новую',
            'edit_item'          => 'Редактировать',
            'new_item'           => 'Новая',
            'view_item'          => 'Посмотреть',
            'search_items'       => 'Найти',
            'not_found'          =>  'Не найдено',
            'not_found_in_trash' => 'В корзине не найдено',
            'parent_item_colon'  => '',
            'menu_name'          => 'Юниты'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'custom-fields')
    ) );
}

function new_meta_boxes() {
    add_meta_box('postcustom', 'Координаты', 'new_print_box', 'units', 'normal', 'high');
}
add_action( 'admin_menu', 'new_meta_boxes' );

function new_print_box($post) {
    wp_nonce_field( basename( __FILE__ ), 'seo_metabox_nonce' );


    $html .= '<label>Широта:  <input type="text" name="x_cood" value="' . get_post_meta($post->ID, 'x_cood',true) . '" /></label><br />';
    $html .= '<label>Долгота: <input type="text" name="y_cood" value="' . get_post_meta($post->ID, 'y_cood',true) . '" /></label> ';
    echo $html;
}

function new_save_box_data( $post_id ) {
    if (isset($_POST['x_cood']) || isset($_POST['y_cood'])){
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;

        $post = get_post($post_id);
        if ($post->post_type == 'units') {
            update_post_meta($post_id, 'x_cood', esc_attr($_POST['x_cood']));
            update_post_meta($post_id, 'y_cood', esc_attr($_POST['y_cood']));
        }
    }
    return $post_id;
}

add_action('save_post', 'new_save_box_data');

add_shortcode( 'yandex_map', 'yandex_map_func' );
function yandex_map_func() {
    global $post;
    return '<div id="map" data-x="' . get_post_meta($post->ID, 'x_cood',true) . '" data-y="' . get_post_meta($post->ID, 'y_cood',true) . '"></div>';
}