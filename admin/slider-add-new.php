<?php
$current_text = '';
$current_pos = 1;
$current_file = '';

function ffsb_slider_addslide($filename, $position, $file_id, $text) {
    $myfile = FFSB_SLIDER_FOLDER_PATH . "libs/ffsb-slider.txt";

    $savestring = $file_id . "#" . $filename . "#" . $position . "#" . $text . "\n";
    file_put_contents($myfile, $savestring, FILE_APPEND | LOCK_EX);
    exit(wp_redirect(admin_url('admin.php?page=my-top-level-handle')));
}

function ffsb_slider_add_submenu_page() {
    add_submenu_page(
            'my-top-level-handle', 'Add Slide', 'Add Slide', 'manage_options', 'addnew_slider', 'ffsb_slider_add_options_function'
    );
}

add_action('admin_menu', 'ffsb_slider_add_submenu_page');

function ffsb_slider_add_register_settings() {
    $safe_edit_val = ffsb_is_integer($_REQUEST['editaction']);
    if (isset($safe_edit_val) && $safe_edit_val == true) {
        register_setting('ffsb_slider_add_settings_group', 'select_file', 'ffsb_edit_slides');
        register_setting('ffsb_slider_add_settings_group', 'add_text', 'ffsb_edit_slides');
    } else {
        register_setting('ffsb_slider_add_settings_group', 'select_file', 'ffsb_validate_setting');
        register_setting('ffsb_slider_add_settings_group', 'add_text', 'ffsb_validate_setting');
    }
    register_setting('ffsb_slider_add_settings_group', 'select_order');
}

add_action('admin_init', 'ffsb_slider_add_register_settings');

function ffsb_slider_upload_dir($dir) {
    return array(
        'path' => $dir['basedir'] . '/ffsb-slider',
        'url' => $dir['baseurl'] . '/ffsb-slider',
        'subdir' => '/ffsb-slider',
            ) + $dir;
}

function ffsb_validate_setting($plugin_options) {
    $keys = array_keys($_FILES);
    $i = 0;
    foreach ($_FILES as $image) {
        // if a files was upload   if ($image['size']) {     // if it is an image    
        if (preg_match('/(jpg|jpeg|png|gif)$/', $image['type'])) {
            $override = array('test_form' => false);
            // save the file, and store an array, containing its location in $file     
            // Register our path override.
            add_filter('upload_dir', 'ffsb_slider_upload_dir');

            $file = wp_handle_upload($image, $override);
            remove_filter('upload_dir', 'ffsb_slider_upload_dir');
            $plugin_options[$keys[$i]] = $file['url'];
            $name = basename($file['url']); // to get file name
            $pos = (int) $_REQUEST['select_order'];
            $text = sanitize_text_field($_REQUEST['add_text']);
            $text = (strlen($text) > 30) ? substr($text, 0, 30) : $text;
            $slider = ffsb_get_slides();
            $total_slides = 1;
            if (count($slider) > 0) {
                $total_slides = count($slider);
            }
            $slide_num = $total_slides;
            ffsb_slider_addslide($name, $pos, $slide_num, $text);
        } else {       // Not an image.     
            $options = get_option('select_file');
            $plugin_options[$keys[$i]] = $options[$logo];
            // Die and let the user know that they made a mistake.   
            wp_die('No image was uploaded.');
        }
    }   // Else, the user didn't upload a file.  
    // Retain the image that's already on file.   else {  
    $options = get_option('select_file');
    $plugin_options[$keys[$i]] = $options[$keys[$i]];
    $i++;
    return $plugin_options;
}

function ffsb_edit_slides() {
    $slider = ffsb_get_slides();
    ffsb_replace_line($slider, (int) $_REQUEST['editactionid']);
    //exit;
}

function ffsb_replace_line($sliderArr, $replaceId) {
    foreach ($sliderArr as $singlearr) {

        if ($singlearr['slide_id'] == $replaceId) {
            $keys = array_keys($_FILES);
            $i = 0;
            foreach ($_FILES as $image) {
                // if a files was upload   if ($image['size']) {     // if it is an image    
                if (preg_match('/(jpg|jpeg|png|gif)$/', $image['type'])) {
                    $override = array('test_form' => false);
                    // save the file, and store an array, containing its location in $file     
                    // Register our path override.
                    add_filter('upload_dir', 'ffsb_slider_upload_dir');
                    $file = wp_handle_upload($image, $override);
                    remove_filter('upload_dir', 'ffsb_slider_upload_dir');
                    $plugin_options[$keys[$i]] = $file['url'];
                    $name = basename($file['url']); // to get file name
                    $pos = (int) $_REQUEST['select_order'];
                    $text = sanitize_text_field($_REQUEST['add_text']);
                    $text = (strlen($text) > 30) ? substr($text, 0, 30) : $text;
                    $slider = ffsb_get_slides();
                    $total_slides = 1;
                    if (count($slider) > 0) {
                        $total_slides = count($slider);
                    }
                    $slidenum = $singlearr['slide_id'];
                    $upload_dir = wp_upload_dir();
                    if (file_exists($upload_dir['basedir'] . '/ffsb-slider/' . $singlearr['image_name'])) {
                        unlink($upload_dir['basedir'] . '/ffsb-slider/' . $singlearr['image_name']);
                    }
                    $oldline = $slidenum . "#" . $singlearr['image_name'] . "#" . $singlearr['slide_position'] . "#" . $singlearr['slide_text'];
                    $newline = $slidenum . "#" . $name . "#" . $pos . "#" . $text . "\n";
                    ffsb_replace_new_line($oldline, $newline);
                } else {       // Not an image.     
                    $pos = (int) $_REQUEST['select_order'];
                    $text = sanitize_text_field($_REQUEST['add_text']);
                    $text = (strlen($text) > 30) ? substr($text, 0, 30) : $text;
                    $slider = ffsb_get_slides();
                    $total_slides = count($slider);
                    $slidenum = $singlearr['slide_id'];
                    $oldline = $slidenum . "#" . $singlearr['image_name'] . "#" . $singlearr['slide_position'] . "#" . $singlearr['slide_text'];
                    $newline = $slidenum . "#" . $singlearr['image_name'] . "#" . $pos . "#" . $text . "\n";
                    ffsb_replace_new_line($oldline, $newline);
                }
            }   // Else, the user didn't upload a file.  
        }
        $p++;
    }
}

function ffsb_replace_new_line($old, $new) {
    $myfile = FFSB_SLIDER_FOLDER_PATH . "libs/ffsb-slider.txt";
    $contents = file_get_contents($myfile);
    $contents = str_replace($old, $new, $contents);
    file_put_contents($myfile, $contents);
    exit(wp_redirect(admin_url('admin.php?page=my-top-level-handle')));
}

function ffsb_slider_add_options_function() {
    $is_valid_id = ffsb_is_integer($_REQUEST['id']);
    if (isset($is_valid_id) && $is_valid_id == true) {
        $slider = ffsb_get_slides();
        foreach ($slider as $singlearr) {
            $edit_id = (int) $_REQUEST['id'];
            if ($singlearr['slide_id'] == $edit_id) {
                $current_id = $singlearr['slide_id'];
                $current_file = $singlearr['image_name'];
                $current_pos = $singlearr['slide_position'];
                $current_text = $singlearr['slide_text'];
            }
        }
    }

    $upload_dir = wp_upload_dir();
    ?>
    <div class="wrap">
        <h2><?php echo esc_html('Flat File Slider - Add New Slide'); ?></h2>
        <form method="post" name="test_form" id="test_form" action="options.php" enctype="multipart/form-data">
    <?php settings_fields('ffsb_slider_add_settings_group'); ?>
    <?php do_settings_sections('ffsb_slider_add_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo esc_html('Select File:'); ?></th>
                    <td><input type="file" name="select_file" class="" value="<?php echo esc_attr(get_option('select_file')); ?>" /></td>
    <?php if (isset($current_id)) { ?>
                        <td>
                            <img src="<?php echo esc_url($upload_dir['baseurl']); ?>/ffsb-slider/<?php echo $current_file; ?>" alt="" width="150px" height="150px"/>
                        </td>
    <?php } ?>

                </tr>	
                <tr valign="top">
                    <th scope="row"><?php echo esc_html('Add Text:'); ?></th>
                    <td><input type="text" name="add_text" class="" value="<?php echo esc_html($current_text); ?>" maxlength="30"/><?php echo esc_html('  - Max 30 characters.'); ?></td>
                </tr>
                <tr valign="top">
                    <th scope="row"> <?php echo esc_html('Select Position:'); ?></th>
                    <td>
    <?php
    $slider = ffsb_get_slides();
    $total_slides = 1;
    if (count($slider) > 0) {
        $total_slides = count($slider);
    }
    ?>
                        <select name="select_order" class="form-control">
                        <?php for ($k = 1; $k <= $total_slides; $k++) { ?>
                                <option <?php if ($current_pos == $k) { ?> selected="selected"<?php } ?> value="<?php echo $k; ?>"><?php echo $k; ?></option>
                            <?php } ?>
                        </select>
                            <?php if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) { ?>
                            <input type="hidden" name="editaction" id="editaction" value="1" />
                            <input type="hidden" name="editactionid" id="editactionid" value="<?php echo esc_attr($_REQUEST['id']); ?>" />
                        <?php }
                        ?>
                    </td>
                </tr>
            </table>

    <?php submit_button(); ?>

        </form>

    </div>
    <?php
}
?>