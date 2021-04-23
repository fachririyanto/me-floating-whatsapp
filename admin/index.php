<?php
/**
 * Admin Page.
 * 
 * @version 1.0.0
 * @since Me Floating WhatsApp 1.0.0
 */
define( 'ME_FLOATING_WA_NONCE_ACTION', 'me_fwa_save_options' );
define( 'ME_FLOATING_WA_NONCE_FIELD', md5( 'me_fwa_form' ) );
?>

<?php
/**
 * Save changes if form submitted.
 * 
 * @uses esc_textarea()
 * @uses update_option()
 * @uses wp_verify_nonce()
 * @since 1.0.0
 */
function me_floating_wa_save_options() {
    // nonce validate
    if ( ! isset( $_POST[ ME_FLOATING_WA_NONCE_FIELD ] ) || ! wp_verify_nonce( $_POST[ ME_FLOATING_WA_NONCE_FIELD ], ME_FLOATING_WA_NONCE_ACTION ) ) {
        return array(
            'status'  => false,
            'message' => 'Not authorized.'
        );
    }

    // get input data
    $image    = isset( $_POST['me-fwa-image'] ) ? $_POST['me-fwa-image'] : '';
    $phone    = isset( $_POST['me-fwa-phone'] ) ? $_POST['me-fwa-phone'] : '';
    $message  = isset( $_POST['me-fwa-message'] ) ? $_POST['me-fwa-message'] : '';
    $size     = isset( $_POST['me-fwa-size'] ) ? $_POST['me-fwa-size'] : '';
    $position = isset( $_POST['me-fwa-position'] ) ? $_POST['me-fwa-position'] : '';
    $y_axis   = isset( $_POST['me-fwa-yaxis'] ) ? $_POST['me-fwa-yaxis'] : '';
    $x_axis   = isset( $_POST['me-fwa-xaxis'] ) ? $_POST['me-fwa-xaxis'] : '';
    $z_index  = isset( $_POST['me-fwa-zindex'] ) ? $_POST['me-fwa-zindex'] : '';
    $open_tab = isset( $_POST['me-fwa-open-tab'] ) ? $_POST['me-fwa-open-tab'] : 'no';

    // validate data
    if ( empty( $phone ) ) {
        return array(
            'status'  => false,
            'message' => 'Empty phone number.'
        );
    }
    if ( empty( $message ) ) {
        return array(
            'status'  => false,
            'message' => 'Empty text message.'
        );
    }
    if ( empty( $position ) ) {
        return array(
            'status'  => false,
            'message' => 'Empty position.'
        );
    }

    // save options
    update_option( 'me_floating_wa_options', array(
        'image'    => $image,
        'phone'    => $phone,
        'message'  => esc_textarea( $message ),
        'size'     => $size,
        'position' => $position,
        'y_axis'   => $y_axis,
        'x_axis'   => $x_axis,
        'z_index'  => $z_index,
        'open_tab' => $open_tab
    ), true );

    // return response
    return array(
        'status'  => true,
        'message' => 'Settings updated.'
    );
}
$responses = array();
if ( isset( $_POST['me-button-save'] ) ) {
    $responses = me_floating_wa_save_options();
}

/**
 * Define available positions.
 */
$available_positions = array(
    'bottom-right' => 'Bottom Right (Default)',
    'bottom-left'  => 'Bottom Left'
);

/**
 * Get current settings.
 */
$settings = me_floating_wa_get_settings();

// define image
$default_image = me_floating_wa_default_image();
$image_url     = me_floating_wa_image_url( $settings['image'] );
?>

<style type="text/css">
    .me-fwa-admin {
        padding: 64px 0;
        color: #444;
    }
    .me-fwa-admin .me-fwa-container {
        margin: auto;
        max-width: 800px;
    }
    .me-fwa-admin .me-fwa-block-header {
        margin: 0 0 8px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(0,0,0,.1);
    }
    .me-fwa-admin .me-fwa-block-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 500;
        line-height: 1.1;
    }
    .me-fwa-admin .me-fwa-boxed {
        position: relative;
        padding: 24px;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.1);
    }
    .me-fwa-admin .me-fwa-textbox,
    .me-fwa-admin .me-fwa-textarea {
        display: block;
        margin: 0;
        width: 100%;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.1);
        border-radius: 6px;
    }
    .me-fwa-admin .me-fwa-textbox {
        padding: 0 12px;
        height: 40px;
    }
    .me-fwa-admin .me-fwa-textarea {
        padding: 12px;
    }
    .me-fwa-admin .me-fwa-select {
        display: block;
        position: relative;
        width: 100%;
        height: 40px;
        overflow: hidden;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.1);
        border-radius: 6px;
    }
    .me-fwa-admin .me-fwa-select select {
        position: absolute;
        top: 0;
        left: 12px;
        right: 4px;
        bottom: 0;
        z-index: 1;
        margin: 0;
        padding: 0;
        width: calc(100% - 16px);
        max-width: 100%;
        height: 100%;
        border: 0;
        outline: 0;
        border-radius: 0;
        box-shadow: none;
    }
    .me-fwa-admin .me-fwa-form .me-form-required {
        color: red;
    }
    .me-fwa-admin .me-fwa-form .me-form-logo {
        display: block;
        margin: 0 0 12px;
        line-height: 0;
    }
    .me-fwa-admin .me-fwa-form .me-form-logo picture {
        display: inline-block;
        position: relative;
        padding: 24px;
        background-color: #ddd;
    }
    .me-fwa-admin .me-fwa-form .me-form-logo img {
        display: block;
        width: 100%;
        max-width: 64px;
        height: auto;
    }
    .me-fwa-admin .me-fwa-form .me-form-logo .logo-remove-button {
        display: none;
        position: absolute;
        top: 0;
        right: 0;
        z-index: 1;
        padding: 4px;
        color: #fff;
        border: 0;
        outline: 0;
        background-color: rgba(0,0,0,.8);
        cursor: pointer;
    }
    .me-fwa-admin .me-fwa-form .me-form-logo.is-selected .logo-remove-button {
        display: block;
    }
    .me-fwa-admin .me-fwa-form .me-form-table {
        width: 100%;
    }
    .me-fwa-admin .me-fwa-form .me-form-table th {
        font-size: 15px;
        font-weight: normal;
        text-align: left;
    }
    .me-fwa-admin .me-fwa-form .me-form-table th,
    .me-fwa-admin .me-fwa-form .me-form-table td {
        padding: 16px 0;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    .me-fwa-admin .me-fwa-form .me-form-action {
        margin-top: 24px;
        text-align: right;
    }
</style>

<div class="wrap me-fwa-admin" id="me-fwa-admin">
    <div class="me-fwa-container">
        <div class="me-fwa-boxed">
            <header class="me-fwa-block-header">
                <h1>Settings</h1>
            </header>
            <form class="me-fwa-form" action="" method="POST">
                <table class="me-form-table">
                    <tr>
                        <th width="200"><label for="me-input-image">Image</label></th>
                        <td>
                            <figure class="me-form-logo<?php echo $settings['image'] ? ' is-selected' : ''; ?>" id="me-logo-preview" data-default="<?php echo $default_image; ?>">
                                <picture>
                                    <img src="<?php echo $image_url; ?>" alt="Logo WhatsApp">
                                    <button class="logo-remove-button" id="me-button-remove-image">
                                        <span class="dashicons dashicons-no"></span>
                                    </button>
                                </picture>
                            </figure>
                            <button type="button" class="button button-secondary" id="me-button-select-image">Select Image</button>
                            <input type="hidden" name="me-fwa-image" id="me-input-image" value="<?php echo $settings['image']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><label for="me-input-phone">Phone Number<span class="me-form-required">*</span></label></th>
                        <td>
                            <input required value="<?php echo $settings['phone']; ?>" type="textbox" name="me-fwa-phone" id="me-input-phone" class="me-fwa-textbox" placeholder="Ex: 621234567890" style="max-width: 240px;"><br />
                            <cite>See the link below how to input phone number. <br /><a target="_blank" href="https://faq.whatsapp.com/general/chats/how-to-use-click-to-chat/?lang=kk">https://faq.whatsapp.com/general/chats/how-to-use-click-to-chat/?lang=kk</a></cite>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="me-input-message">Text Message<span class="me-form-required">*</span></label></th>
                        <td><textarea required name="me-fwa-message" id="me-input-message" rows="5" class="me-fwa-textarea"><?php echo stripslashes_deep( $settings['message'] ); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="me-input-size">Size (max width in px)</label></th>
                        <td><input value="<?php echo $settings['size']; ?>" type="number" name="me-fwa-size" id="me-input-size" class="me-fwa-textbox" placeholder="56" style="max-width: 100px;"></td>
                    </tr>
                    <tr>
                        <th><label for="me-input-position">Position</label></th>
                        <td>
                            <span class="me-fwa-select" style="max-width: 240px;">
                                <select name="me-fwa-position" id="me-input-position">
                                    <?php foreach ( $available_positions as $key => $position ) : ?>
                                        <?php if ( $key == $settings['position'] ) : ?>
                                            <option value="<?php echo $key; ?>" selected><?php echo $position; ?></option>
                                        <?php else : ?>
                                            <option value="<?php echo $key; ?>"><?php echo $position; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="me-input-yaxis">Y-Axis Margin (px)</label></th>
                        <td><input value="<?php echo $settings['y_axis']; ?>" type="number" name="me-fwa-yaxis" id="me-input-yaxis" class="me-fwa-textbox" placeholder="20" style="max-width: 100px;"></td>
                    </tr>
                    <tr>
                        <th><label for="me-input-xaxis">X-Axis Margin (px)</label></th>
                        <td><input value="<?php echo $settings['x_axis']; ?>" type="number" name="me-fwa-xaxis" id="me-input-xaxis" class="me-fwa-textbox" placeholder="20" style="max-width: 100px;"></td>
                    </tr>
                    <tr>
                        <th><label for="me-input-zindex">Z-Index</label></th>
                        <td><input value="<?php echo $settings['z_index']; ?>" type="number" name="me-fwa-zindex" id="me-input-zindex" class="me-fwa-textbox" placeholder="100" style="max-width: 100px;"></td>
                    </tr>
                    <tr>
                        <th><label for="me-input-open-tab">Open New Tab?</label></th>
                        <td><input type="checkbox" name="me-fwa-open-tab" id="me-input-open-tab" value="yes"<?php echo $settings['open_tab'] == 'yes' ? ' checked' : ''; ?>></td>
                    </tr>
                </table>
                <div class="me-form-action">
                    <button type="submit" class="button button-primary button-large" name="me-button-save">Save Changes</button>
                </div>
                <?php wp_nonce_field( ME_FLOATING_WA_NONCE_ACTION, ME_FLOATING_WA_NONCE_FIELD ); ?>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    ( function( $ ) {
        $( document ).ready( function() {
            /**
             * Handle select image.
             */
            var mediaFrame;
            $( '#me-fwa-admin' ).on( 'click', '#me-button-select-image', function( e ) {
                e.preventDefault();

                if ( mediaFrame ) {
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media( {
                    title: 'Upload Image',
                    button: {
                        text: 'Select Image'
                    },
                    library: {
                        type: [ 'image/png', 'image/jpg', 'image/jpeg', 'image/gif' ]
                    },
                    multiple: false
                } );

                mediaFrame.on( 'open', function() {
                    var selection    = mediaFrame.state().get( 'selection' );
                    var attachmentId = $( '#me-input-image' ).val();
                    var attachment   = wp.media.attachment( attachmentId );
                    selection.add( attachment ? [ attachment ] : [] );
                } );

                mediaFrame.on( 'select', function() {
                    var attachment = mediaFrame.state().get( 'selection' ).first().toJSON();

                    // setup preview
                    $( '#me-input-image' ).val( attachment.id );
                    $( '#me-logo-preview' ).addClass( 'is-selected' ).find( 'img' ).attr(
                        'src', attachment.sizes.thumbnail.url
                    );
                } );

                mediaFrame.open();
            } )

            /**
             * Handle remove button.
             */
            .on( 'click', '#me-button-remove-image', function( e ) {
                e.preventDefault();

                // set to default image
                $( '#me-input-image' ).val( '' );
                $( '#me-logo-preview' ).removeClass( 'is-selected' ).find( 'img' ).attr(
                    'src', $( '#me-logo-preview' ).attr( 'data-default' )
                );
            } );
        } );
    } )( jQuery );
</script>
