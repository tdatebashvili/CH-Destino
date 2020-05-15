<?php
if ( is_user_logged_in() ) {

    echo $before_widget;

    echo $before_title . $title . $after_title;

    echo '<p>Total horas: ' . $ch_destino_hours_minuts[0] .'h '.$ch_destino_hours_minuts[1].'min</p>
            <p>Estado: ' . $ch_destino_state . '</p>';

    echo $after_widget;
}
?>
