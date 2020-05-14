<?php
/*
Plugin Name: CH-Destino
Description: Este plugin contarÃ¡ el total de la horas dedicadas a una tarea
Version: 1.0
Author:Teymuraz Datebashvili
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (! defined( 'WPINC')){
    die;
}
require ('include/ch-functions.php');

// Variables del plugin
$plugin_url = WP_PLUGIN_URL. '/ch-destino';

/*
 *
 * Seccion de las funciones y classes
 *
 *
 */

//Genera una tabla para el plugin
function ch_destino_create_plugin_database_table(){
    global  $wpdb;

    // Crea el nombre de la tabla y asegura que se cree con el mismo prefijo que ya tienen las otras tablas creadas (wp_form).
    $tblname = $wpdb->prefix . "ch_destino";

    $tbuser = $wpdb->prefix . "users";

    $charset_collate = $wpdb->get_charset_collate();

    // Declara la tabla del plugin.
    $sql = /** @lang MySQL */
        "CREATE TABLE  {$tblname}
          ( id  int(11)   NOT NULL auto_increment,
            user_id  int(128)   NOT NULL,
            date date,
            hour_start  time,
            hour_finish time DEFAULT '00:00:00',
            hours_total time DEFAULT '00:00:00',
            CONSTRAINT PK_ch_destino PRIMARY KEY (id),
            CONSTRAINT FK_ch_destino_users FOREIGN KEY (user_id)
                    REFERENCES {$tbuser} (ID)
                    ) ENGINE=MyISAM DEFAULT CHARSET={$charset_collate} AUTO_INCREMENT=1 ; ";

    // upgrade contiene la funcion dbDelta la cual revisa si existe la tabla.
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

    // Ejecuta la sentencia sql para crear la tabla
    dbDelta($sql);
}

/*
function ch_destino_create_plugin_trigger(){

    global $wpdb;

    // Crea el nombre de la tabla y asegura que se cree con el mismo prefijo que ya tienen las otras tablas creadas (wp_form).
    $tblname = $wpdb->prefix . "ch_destino";

    // Declaramos el trigger que se crearÃ¡ de la forma comÃºn.
    $sql_trigger = /** @lang MySQL */
/*
        "CREATE  TRIGGER ch_destino_trigger_update AFTER UPDATE ON {$tblname} FOR EACH ROW
            BEGIN
                DECLARE ch_destino_hour_start, ch_destino_hour_finish float;
                SELECT OLD.hour_start INTO ch_destino_hour_start FROM {$tblname} WHERE id = OLD.id;
                SELECT NEW.hour_finish INTO ch_destino_hour_finish FROM {$tblname} WHERE id = OLD.id;
                UPDATE {$tblname}
                  SET hours_total = ch_destino_hour_finish - ch_destino_hour_start
                WHERE id = OLD.id;
            END;";

    mysqli_multi_query($wpdb->dbh,$sql_trigger);
}
*/

// Genera una pagina de configuracion
function ch_destino_menu() {
    add_options_page(
        'CH-Destino Plugin',
        'CH-Destino',
        'manage_options',
        'ch-destino',
        'ch_destino_options_page'
    );
}

// Añade la pagina de configuración en el menu del aministrador
function ch_destino_options_page() {
    if ( !current_user_can( 'manage_options' ) ){
        wp_die( 'Uste no dispone de suficinetes permisos para acceder a esta pagina' );
    }

    global $plugin_url;

    $user_id = get_current_user_id();
    $ch_destino_date_now = (new DateTime())->format('Y-m-d');

    if (isset($_POST['ch_destino_form_submitted'])) {
        $hidden_field = esc_html($_POST['ch_destino_form_submitted']);

        if ($hidden_field == 'Y') {

            if (isset($_POST["start"])) {
                $ch_destino_hour_start = (new DateTime())->format('H:i');
                ch_destino_insert_data($user_id, $ch_destino_hour_start, $ch_destino_date_now);
                $ch_destino_options ['ch_destino_state'] = 'Start';
                $ch_destino_options ['ch_destino_last_update'] = time();
                update_user_meta($user_id, 'ch_destino', $ch_destino_options);
            }

            if (isset($_POST["stop"])) {
                $ch_destino_hour_stop = (new DateTime())->format('H:i');
                ch_destino_update_data($user_id, $ch_destino_hour_stop, $ch_destino_date_now);
                $ch_destino_options ["ch_destino_state"] = 'Stop';
                $ch_destino_options ['ch_destino_last_update'] = time();
                update_user_meta($user_id, 'ch_destino', $ch_destino_options);
            }
        }
    }

    $ch_destino_data = ch_destino_get_data($user_id);

    $ch_destino_hours_minuts = explode(':', strval($ch_destino_data));

    $ch_destino_options = get_user_meta($user_id, 'ch_destino', 'true');

    if($ch_destino_options != ''){

        $ch_destino_state = $ch_destino_options ['ch_destino_state'];
    }
    require( 'include/options-page-wrapper.php');
}

/* Añade los estilos CSS a la pagina de aministrador
function ch_destino_style (){

    wp_enqueue_style('ch_destino_style', plugins_url('ch_destino/css/ch_destino.css'));
}
*/

// Crea una clase para el Widget del plugin Ch-Destino
class ch_destino_Widget extends WP_Widget {

    function __construct() {
        // Instantiate the parent object
        parent::__construct( false, 'CH-Destino' );
    }

    function widget( $args, $instance ) {

        extract ($args);
        $title = apply_filters( 'widget_title', $instance['title'] );

        $user_id = get_current_user_id();

        $ch_destino_options = get_user_meta($user_id, 'ch_destino', 'true');

        if($ch_destino_options != ''){

            $ch_destino_state = $ch_destino_options ['ch_destino_state'];
        }

        $ch_destino_data = ch_destino_get_data($user_id);

        $ch_destino_hours_minuts = explode(':', strval($ch_destino_data));

        require('include/front-end.php');
    }

    function update( $new_instance, $old_instance ) {

        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title']);

        return $instance;
    }

    function form( $instance ) {

        $title = esc_attr($instance['title']);

        require('include/widget-fields.php');
    }
}

// Registra el widget
function ch_destino_register_widgets() {
    register_widget( 'ch_destino_Widget' );
}

/*
 *
 * SecciÃ³n de acciones
 *
 *
 */

add_action('admin_menu', 'ch_destino_menu');
add_action('admin_head', 'ch_destino_style');
add_action( 'widgets_init', 'ch_destino_register_widgets');
//add_action('init', 'ch_destino_create_session');

/*
 *
 * Seccion de hooks
 *
 *
 */

register_activation_hook( __FILE__, 'ch_destino_create_plugin_database_table' );

//register_activation_hook( __FILE__, 'ch_destino_create_plugin_trigger' );
