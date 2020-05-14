<?php

    function ch_destino_calcula_diferencia($ch_destino_hour_start, $ch_destino_hour_finish){


        $ch_destino_datetime1 = date_create($ch_destino_hour_start);
        $ch_destino_datetime2 = date_create($ch_destino_hour_finish);

        $ch_destino_interval = date_diff($ch_destino_datetime1, $ch_destino_datetime2);

        return $ch_destino_interval->format('%H:%i');

    }

    // Agrega nuevo registro con el id del usuario, la fecha y la hora de inicio
    function ch_destino_insert_data($ch_destino_user_id, $ch_destino_hour_start, $ch_destino_date_now){

        global $wpdb;

        $ch_destino_data = array(
            'user_id' => $ch_destino_user_id,
            'hour_start' => $ch_destino_hour_start,
            'date' => $ch_destino_date_now
        );

        $wpdb->insert($wpdb->prefix.'ch_destino', $ch_destino_data, array('%d', '%s', '%s'));
    }
    // Actualiza el registro  con la hora de finalizacion del conteo.
    function ch_destino_update_data($ch_destino_user_id, $ch_destino_hour_finish, $ch_destino_date_now){

        global $wpdb;

        $ch_destino_hour_start = $wpdb->get_var($wpdb->prepare("SELECT  hour_start
                                                                FROM {$wpdb->prefix}ch_destino 
                                                            WHERE hour_finish = %s", '00:00:00'));

        $wpdb->update($wpdb->prefix.'ch_destino',
            array('hour_finish' => $ch_destino_hour_finish),
            array('user_id' => $ch_destino_user_id,
                  'hour_finish' => '00:00:00',
                  'date'=>$ch_destino_date_now));

        $ch_destino_total_hours = ch_destino_calcula_diferencia($ch_destino_hour_finish,  $ch_destino_hour_start);

        $wpdb->update($wpdb->prefix.'ch_destino',
            array('hours_total' => $ch_destino_total_hours),
            array('user_id' => $ch_destino_user_id,
                'hours_total' => '00:00:00',
                'date'=>$ch_destino_date_now));
    }

    // Obtiene los datos necesarios para calcular las horas destinadas
    function ch_destino_get_data($ch_destino_user_id){

        global $wpdb;

        $ch_destino_data = $wpdb->get_var($wpdb->prepare("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(hours_total))) 
                                                                FROM {$wpdb->prefix}ch_destino 
                                                            WHERE user_id = %d", $ch_destino_user_id));

        return $ch_destino_data;
    }
