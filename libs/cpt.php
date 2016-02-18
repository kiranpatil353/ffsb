<?php
   wp_enqueue_script('slider-validation-js', plugins_url('/../assets/js/validation.js', __FILE__));
   
function getSlides() {
    $myfile = fopen(PLUGIN_FOLDER_PATH . "libs/slider.txt", "r") or die("Unable to open file!");
    // Output one line until end-of-file
    $slider = array();
    $i = 0;
    while (!feof($myfile)) {
        $keys = array();
        $myslide = explode("#", fgets($myfile));
        $key["slide_id"] = $myslide[0];
        $key["image_name"] = $myslide[1];
        $key["slide_position"] = $myslide[2];
        $slider[$i++] = $key;
    }
    fclose($myfile);
    return $slider;
}

if (isset($_REQUEST['deleteval']) && !empty($_REQUEST['deleteval'])) {

    $myfile = PLUGIN_FOLDER_PATH . "libs/slider.txt";
    $slider = getSlides();
    foreach ($slider as $singlearr) {
        if ($singlearr['slide_id'] == $_REQUEST['deleteval']) {
            $slidenum = $singlearr['slide_id'];
            $upload_dir = wp_upload_dir();
            unlink($upload_dir['basedir'] . '/slider/' . $singlearr['image_name']);
            $oldline = $slidenum . "#" . $singlearr['image_name'] . "#" . $singlearr['slide_position'];
            $contents = file_get_contents($myfile);
            $contents = str_replace($oldline, '', $contents);
            file_put_contents($myfile, $contents);
        }
    }
}

/** Step 1. */
function slider() {
    //add_options_page( 'slider Options', 'slider', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );

    add_menu_page('Slider', 'Slider', 'manage_options', 'my-top-level-handle', 'my_plugin_options');
}

/** Step 2 (from text above). */
add_action('admin_menu', 'Slider');

/** Step 3. */
function my_plugin_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    $myfile = fopen(plugin_dir_path(__FILE__) . "slider.txt", "r") or die("Unable to open file!");
    // Output one line until end-of-file
    $slider = array();
    $i = 0;
    while (!feof($myfile)) {
        $keys = array();
        $myslide = explode("#", fgets($myfile));
        if (!empty($myslide[0])) {
            $key["slide_id"] = $myslide[0];
            $key["image_name"] = $myslide[1];
            $key["slide_position"] = $myslide[2];
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
        <h1> Flat Slider -  
            <a class="page-title-action" href="<?php echo admin_url(); ?>admin.php?page=addnew_slider">Add New</a>
        </h1>
    </div>
    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <tr>

                <th class="manage-column column-author" id="author" scope="col">Slide Image</th>
                <th class="manage-column column-author" id="author" scope="col">Position</th>
                <th class="manage-column column-author" id="author" scope="col">Action</th>


            </tr>
        </thead>

        <tbody id="the-list">
    <?php
    $upload_dir = wp_upload_dir();
    for ($i = 0; $i < count($slider); $i++) {
        ?>
                <tr class="iedit author-self level-0 post-61 type-page status-publish hentry">
                    <td><img height="150" width="250" src="<?php echo $upload_dir['baseurl'] . '/slider/' . $slider[$i]['image_name']; ?>" /></td>
                    <td><?php echo $slider[$i]['slide_position']; ?></td>
                    <td><a href="<?php echo admin_url(); ?>admin.php?page=addnew_slider&id=<?php echo $slider[$i]['slide_id']; ?>">Edit </a>
                        <form action="" id="delfrm<?php echo $slider[$i]['slide_id']; ?>" name="delfrm<?php echo $slider[$i]['slide_id']; ?>" method="post">
                            <a href="javascript:;"onclick="javascript:confirm('Do you really want to delete') ? validate(event, <?php echo $slider[$i]['slide_id']; ?>) : 0"  />Delete </a>
                            <input type="hidden" name="deleteval" id="deleteval" value="<?php echo $slider[$i]['slide_id']; ?>" />

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

