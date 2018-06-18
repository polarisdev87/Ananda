<?php
    /**
     * The template for the panel header area.
     * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
     *
     * @author      Redux Framework
     * @package     ReduxFramework/Templates
     * @version:    3.5.4.18
     */


?>
<div id="redux-header">
    <?php if ( ! empty( $this->parent->args['display_name'] ) ) { ?>
        <div class="display_header">

            <div class="grve-redux-logo"><img src="<?php echo esc_url( get_template_directory_uri() . "/includes/images/grve-logo.png" ); ?>" alt="Blade Logo"></div>

            <?php if ( ! empty( $this->parent->args['display_version'] ) ) { ?>
                <span class="grve-theme-version"><?php echo wp_kses_post( $this->parent->args['display_version'] ); ?></span>
            <?php } ?>

        </div>
    <?php } ?>

    <div class="clear"></div>
</div>