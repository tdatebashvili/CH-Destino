<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>
    <h1><?php esc_attr_e( 'CH-Destino', 'WpAdminStyle' ); ?></h1>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">

                        <h2><span><?php esc_attr_e( 'Opciones:', 'WpAdminStyle' ); ?></span></h2>

                        <div class="inside">
                            <p>Con este plugin podras contabilizar tus horas sin nigún problema.</p>
                            <p>Estado actual: <?php echo $ch_destino_state; ?></p><br>
                            <p>Total horas: <?php echo $ch_destino_hours_minuts[0] ."h ".$ch_destino_hours_minuts[1]?>min</p>
                            <p>
                                <form class="ch_destino_options" action="" method="post">
                                    <input type="hidden" name="ch_destino_form_submitted" value ="Y">
                                    <input class="button-primary" type="submit" name="start" value="<?php esc_attr_e( 'Start' ); ?>" />
                                    <input class="button-primary" type="submit" name="stop" value="<?php esc_attr_e( 'Stop' ); ?>" />
                                </form>
                            </p>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

            </div>
            <!-- post-body-content -->
            <!-- .postbox -->

        </div>
        <!-- .meta-box-sortables -->

    </div>
    <!-- #postbox-container-1 .postbox-container -->

</div>
<!-- #post-body .metabox-holder .columns-2 -->

<br class="clear">
</div>
<!-- #poststuff -->

</div> <!-- .wrap -->
