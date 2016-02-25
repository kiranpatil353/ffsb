<?php
wp_enqueue_script('slider-validation-js', plugins_url('/../assets/js/validation.js', __FILE__));

function ffsb_get_slides() {
    $myfile = fopen(FFSB_SLIDER_FOLDER_PATH . "libs/ffsb-slider.txt", "r") or die("Unable to open file!");
    // Output one line until end-of-file
    $slider = array();
    $i = 0;
    while (!feof($myfile)) {
        $keys = array();
        $myslide = explode("#", fgets($myfile));
        $key["slide_id"] = $myslide[0];
        $key["image_name"] = $myslide[1];
        $key["slide_position"] = $myslide[2];
        $key["slide_text"] = $myslide[3];
        $slider[$i++] = $key;
    }
    fclose($myfile);
    return $slider;
}

function ffsb_is_integer($val_to_check) {
    $val_to_check = intval($val_to_check);
    if ($val_to_check) {
        return true;
    } else {
        return false;
    }
}

// check for integer value
$is_safe_delete_id = ffsb_is_integer($_REQUEST['deleteval']);

if (isset($_REQUEST['deleteval']) && is_numeric($_REQUEST['deleteval'])) {

    $myfile = FFSB_SLIDER_FOLDER_PATH . "libs/ffsb-slider.txt";
    $slider = ffsb_get_slides();
    foreach ($slider as $singlearr) {
        $delete_id = $_REQUEST['deleteval'];
        if ($singlearr['slide_id'] == (int) $delete_id) {
            $slidenum = $singlearr['slide_id'];
            $upload_dir = wp_upload_dir();
            if (file_exists($upload_dir['basedir'] . '/ffsb-slider/' . $singlearr['image_name'])) {
                unlink($upload_dir['basedir'] . '/ffsb-slider/' . $singlearr['image_name']);
            }
            $oldline = $slidenum . "#" . $singlearr['image_name'] . "#" . $singlearr['slide_position'] . "#" . $singlearr['slide_text'];
            $contents = file_get_contents($myfile);
            $contents = str_replace($oldline, '', $contents);
            file_put_contents($myfile, $contents);
        }
    }
}

/** Step 1. */
function ffsb_slider() {
    //add_options_page( 'slider Options', 'slider', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );

    add_menu_page('Flat File Slider', 'Flat File Slider', 'manage_options', 'my-top-level-handle', 'ffsb_slider_plugin_options');
}

/** Step 2 (from text above). */
add_action('admin_menu', 'ffsb_slider');

/** Step 3. */
function ffsb_slider_plugin_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    $myfile = fopen(plugin_dir_path(__FILE__) . "ffsb-slider.txt", "r") or die("Unable to open file!");
    // Output one line until end-of-file
    $slider = array();
    $i = 0;
    while (!feof($myfile)) {
        $keys = array();
        $line_of_file = fgets($myfile);
        if (preg_match("/#/", $line_of_file, $matches)) {
            $myslide = explode("#", $line_of_file);
        } else {
            continue;
        }
        $myslide = explode("#", $line_of_file);
        if (!empty($myslide[0])) {
            $key["slide_id"] = $myslide[0];
            $key["image_name"] = $myslide[1];
            $key["slide_position"] = $myslide[2];
            $key["slide_text"] = $myslide[3];
            $slider[$i++] = $key;
        }
    }
    fclose($myfile);

    for ($i = 0; $i < count($slider) - 1; $i++) {
        for ($j = $i + 1; $j < count($slider); $j++) {
            if ($slider[$i]['slide_position'] > $slider[$j]['slide_position']) {
                $temp = $slider[$i];
                $slider[$i] = $slider[$j];
                $slider[$j] = $temp;
            }
        }
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html('Flat File Slider -'); ?>
            <a class="page-title-action" href="<?php echo admin_url(); ?>admin.php?page=addnew_slider"><?php echo esc_html('Add New'); ?></a>
        </h1>
    </div>
    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <tr>

                <th class="manage-column column-author" id="author" scope="col"><?php echo esc_html('Slide Image'); ?></th>
                <th class="manage-column column-author" id="author" scope="col"><?php echo esc_html('Position'); ?></th>
                <th class="manage-column column-author" id="author" scope="col"><?php echo esc_html('Action'); ?></th>


            </tr>
        </thead>

        <tbody id="the-list">
            <?php
            $upload_dir = wp_upload_dir();
            for ($i = 0; $i < count($slider); $i++) {
                ?>
                <tr class="iedit author-self level-0 post-61 type-page status-publish hentry">
                    <td><img height="150" width="250" src="<?php echo esc_url($upload_dir['baseurl'] . '/ffsb-slider/' . $slider[$i]['image_name']); ?>" /></td>
                    <td><?php echo esc_html($slider[$i]['slide_position']); ?></td>
                    <td><a href="<?php echo esc_url(admin_url()); ?>admin.php?page=addnew_slider&id=<?php echo $slider[$i]['slide_id']; ?>"> <?php echo esc_html('Edit'); ?> </a>
                        <form action="" id="delfrm<?php echo $slider[$i]['slide_id']; ?>" name="delfrm<?php echo $slider[$i]['slide_id']; ?>" method="post">
                            <a href="javascript:;"onclick="javascript:confirm('Do you really want to delete') ? validate(event, <?php echo $slider[$i]['slide_id']; ?>) : 0"  /><?php echo esc_html('Delete'); ?> </a>
                            <input type="hidden" name="deleteval" id="deleteval" value="<?php echo esc_html($slider[$i]['slide_id']); ?>" />

                        </form>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>

