<?php
/**
 * Plugin Name:       WS Event plugin
 * Plugin URI:        https://websociety.fr/
 * Description:       Add Events support on WordPress.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            WEB SOCIETY
 * Author URI:        https://websociety.fr/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       ws_event_content
 */


function ws_event_content_type() {
    // Event custom type
    $labels = array(
        'name' => _x('Evénements', 'ws_event_content'),
		'singular_name' => _x('Evénement', 'ws_event_content'),
		'add_new' => _x('Ajouter', 'ws_event_content'),
		'add_new_item' => _x('Ajouter un événement', 'ws_event_content'),
		'edit_item' => _x('Modifier un événement', 'ws_event_content'),
		'new_item' => _x('Nouvel événement', 'ws_event_content'),
		'view_item' => _x('Voir l\'événement', 'ws_event_content'),
		'search_items' => _x('Rechercher un événement', 'ws_event_content'),
		'not_found' => _x('Aucun événement trouvé', 'ws_event_content'),
		'not_found_in_trash' => _x('Aucun événement dans la corbeille', 'ws_event_content'),
		'parent_item_colon' => _x('Evénement parent :', 'ws_event_content'),
		'menu_name' => _x('Evénements', 'ws_event_content'),
    );

    register_post_type('ws_event',
        array(
            'labels' => $labels,
            'hierarchical' => false,
		    'description' => __('Liste des événements', 'ws_event_content'),
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'revisions'],
            'taxonomies' => array('post_tag','category'),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_nav_menus' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'has_archive' => true,
            'query_var' => true,
            'can_export' => true,
            'rewrite' => [ 'slug' => 'events' ],
            'capability_type' => 'post'
        )
    );
}
add_action('init', 'ws_event_content_type');

function ws_add_events_custom_type($query)
{
    if (is_home() && $query->is_main_query()) {
        $query->set('post_type', ['post', 'page', 'ws_event']);
    }
    return $query;
}
add_action('pre_get_posts', 'ws_add_events_custom_type');

function ws_events_register_metaboxes() {
    add_meta_box( 'events_data', _x('Evénements', 'ws_event_content'), 'events_data_callback', 'ws_event' );
}
function events_data_callback($post){
    global $post;
    $post_type = get_post_type();
    if($post_type == "ws_event"){
        $custom = get_post_custom($post->ID);
        $address = "";
        $linkRegistry = "";
        $linkDetailDoc = "";
        $start_date = "";
        $end_date = "";
        $short_description = "";
        if(count($custom) > 0) {
            $address = isset($custom["address"]) ? $custom["address"][0] : "";
            $linkRegistry = isset($custom["linkRegistry"]) ? $custom["linkRegistry"][0] : "";
            $linkDetailDoc = isset($custom["linkDetailDoc"]) ? $custom["linkDetailDoc"][0] : "";
            $start_date = isset($custom["start_date"]) ? date("Y-m-d\TH:i", $custom["start_date"][0]) : date("Y-m-d\TH:i", time());
            $end_date = isset($custom["end_date"]) ? date("Y-m-d\TH:i", $custom["end_date"][0]) : date("Y-m-d\TH:i", time());
            $short_description = isset($custom["short_description"]) ? $custom["short_description"][0] : "";
        }
        
        ?>
            <p><label><?php echo _x('Description courte', 'ws_event_content') ?> :</label></p>
            <textarea cols=20 rows=5 name="short_description"><?php echo $short_description ?></textarea>
            <p><label><?php echo _x('Adresse complète de l\'événement', 'ws_event_content') ?> :</label><br />
            <input type="text" name="address" value="<?php echo $address ?>"></input></p>
            <p><label><?php echo _x('Lien d\'inscription pour les partenaires', 'ws_event_content') ?> :</label><br />
            <input type="text" name="linkRegistry" value="<?php echo $linkRegistry ?>"></input></p>
            <p><label><?php echo _x('Lien vers le document d\'infos pratiques', 'ws_event_content') ?> :</label><br />
            <input type="text" name="linkDetailDoc" value="<?php echo $linkDetailDoc ?>"></input></p>
            <p><label><?php echo _x('Date et heure de début de l\'évenement', 'ws_event_content') ?> :</label><br />
            <input type="datetime-local" name="start_date" value="<?php echo $start_date ?>"></input></p>
            <p><label><?php echo _x('Date et heure de fin de l\'évenement', 'ws_event_content') ?> :</label><br />
            <input type="datetime-local" name="end_date" value="<?php echo $end_date ?>"></input></p>
        <?php
    }
}
add_action( 'add_meta_boxes', 'ws_events_register_metaboxes' );

// save custom fields on ws_event
function ws_event_save_details(){
    global $post;
    $post_type = get_post_type();
    if($post_type == "ws_event"){
        
        if(isset($_POST["address"])){
            $error = false;
            update_post_meta($post->ID, "address", $_POST["address"]);
        }
        if(isset($_POST["linkRegistry"])){
            update_post_meta($post->ID, "linkRegistry", $_POST["linkRegistry"]);
        }
        if(isset($_POST["linkDetailDoc"])){
            update_post_meta($post->ID, "linkDetailDoc", $_POST["linkDetailDoc"]);
        }
        if(isset($_POST["start_date"])){
            update_post_meta($post->ID, "start_date", strtotime($_POST["start_date"]));
        }
        if(isset($_POST["end_date"])){
            update_post_meta($post->ID, "end_date", strtotime($_POST["end_date"]));
        }
    }
}
add_action('save_post', 'ws_event_save_details');

//add columns on backoffice for fields
function ws_event_edit_columns($columns){
    $columns = array(
        "cb" => "<input type='checkbox' />",
        "title" => "Nom de l'événement",
        "short_description" => "Description courte",
        "start_date" => "Date de début",
        "end_date" => "Date de fin",
    );
    
    return $columns;
}
add_filter("manage_edit-ws_event_columns", "ws_event_edit_columns");

function ws_event_custom_columns($column){
    global $post;
    $post_type = get_post_type();
    if($post_type == "ws_event"){
        switch ($column) {
            case "short_description":
                $custom = get_post_custom();
                echo isset($custom["short_description"]) ? $custom["short_description"][0] : "";
                break;
            case "start_date":
                $custom = get_post_custom();
                echo isset($custom["start_date"]) ? date("d/m/Y", $custom["start_date"][0]) : "";
                break;
            case "end_date":
                $custom = get_post_custom();
                echo date("d/m/Y", $custom["end_date"][0]);
                break;
        }
    }
}
add_action("manage_posts_custom_column",  "ws_event_custom_columns");

// define template to load for ws_event single page
function ws_event_custom_single($single) {
    global $post;
    /* Checks for single template by post type */
    if ( $post->post_type == 'ws_event' ) {
        if ( file_exists( dirname( __FILE__ ) . '/single-ws_event.php' ) ) {
            $single = dirname( __FILE__ ) . '/single-ws_event.php';
        }
    }
    return $single;
}
add_filter('single_template', 'ws_event_custom_single');

// define template to load for ws_event archive page
function ws_event_custom_archive( $archive_template ) {
     global $post;

     if ( is_post_type_archive ( 'ws_event' ) ) {
          $archive_template = dirname( __FILE__ ) . '/archive-ws_event.php';
     }
     return $archive_template;
}

add_filter( 'archive_template', 'ws_event_custom_archive' ) ;

