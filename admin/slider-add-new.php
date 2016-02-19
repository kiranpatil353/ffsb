<?php
	$current_text = '';
	$current_pos = 1;
	$current_file ='';
	
function AddSlide($filename, $position, $file_id ,$text) {
    $myfile = PLUGIN_FOLDER_PATH . "libs/slider.txt";
    $savestring = $file_id . "#" . $filename . "#" . $position ."#".$text."\n";
    file_put_contents($myfile, $savestring, FILE_APPEND | LOCK_EX);
    exit(wp_redirect(admin_url('admin.php?page=my-top-level-handle')));
}

function slider_add_submenu_page() {
    add_submenu_page(
            'my-top-level-handle', 'Add Slide', 'Add Slide', 'manage_options', 'addnew_slider', 'slider_slider_add_options_function'
    );
}

add_action('admin_menu', 'slider_add_submenu_page');

function slider_slider_add_register_settings() {
     if (isset($_REQUEST['editaction'])) {
        register_setting('slider_slider_add_settings_group', 'select_file', 'EditSlides');
		register_setting('slider_slider_add_settings_group', 'add_text', 'EditSlides');
		
		
    } else {
        register_setting('slider_slider_add_settings_group', 'select_file', 'validate_setting');
		register_setting('slider_slider_add_settings_group', 'add_text', 'validate_setting');
    }
    register_setting('slider_slider_add_settings_group', 'select_order');
}

add_action('admin_init', 'slider_slider_add_register_settings');

function slider_slider_add_options_function() {
	
	
		if(isset($_REQUEST['id'])){
		$slider =  getSlides();
		 foreach ($slider as $singlearr) {
			
		 if ($singlearr['slide_id'] == $_REQUEST['id']) {
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
        <h2>Flat Slider - Add New Slide</h2>
        <form method="post" name="test_form" id="test_form" action="options.php" enctype="multipart/form-data">
    <?php settings_fields('slider_slider_add_settings_group'); ?>
    <?php do_settings_sections('slider_slider_add_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select File:</th>
                    <td><input type="file" name="select_file" class="" value="<?php echo esc_attr(get_option('select_file')); ?>" /></td>
						<?php if(isset($current_id)){ ?>
							<td>
								<img src="<?php echo $upload_dir['baseurl'];?>/slider/<?php echo $current_file;?>" alt="" width="150px" height="150px"/>
								</td>
						<?php 	} ?>
					
                </tr>	
				<tr valign="top">
                    <th scope="row">Add Text:</th>
                    <td><input type="text" name="add_text" class="" value="<?php echo $current_text; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"> Select Position:</th>
                    <td>
    <?php
    $slider = getSlides();
    $total_slides = count($slider);
    ?>
                        <select name="select_order" class="form-control">
                        <?php for ($k = 1; $k <= $total_slides; $k++) { ?>
                                <option <?php if ( $current_pos == $k ) { ?> selected="selected"<?php } ?> value="<?php echo $k; ?>"><?php echo $k; ?></option>
                        <?php } ?>
                        </select>
                            <?php if (isset($_GET[id])) { ?>
                            <input type="hidden" name="editaction" id="editaction" value="1" />
                            <input type="hidden" name="editactionid" id="editactionid" value="<?php echo $_GET[id]; ?>" />
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

function slider_upload_dir($dir) {
    return array(
        'path' => $dir['basedir'] . '/slider',
        'url' => $dir['baseurl'] . '/slider',
        'subdir' => '/slider',
            ) + $dir;
}

function validate_setting($plugin_options) {
    $keys = array_keys($_FILES);
    $i = 0;
    foreach ($_FILES as $image) {
        // if a files was upload   if ($image['size']) {     // if it is an image    
        if (preg_match('/(jpg|jpeg|png|gif)$/', $image['type'])) {
            $override = array('test_form' => false);
            // save the file, and store an array, containing its location in $file     
            // Register our path override.
            add_filter('upload_dir', 'slider_upload_dir');

            $file = wp_handle_upload($image, $override);
            remove_filter('upload_dir', 'slider_upload_dir');
            $plugin_options[$keys[$i]] = $file['url'];
            $name = basename($file['url']); // to get file name
            $pos = $_POST['select_order'];
			$text = $_POST['add_text'];
            $slider = getSlides();
            $total_slides = count($slider);
            $slide_num = $total_slides;
            $total_slides = count($slider);
            AddSlide($name, $pos, $slide_num, $text);
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

function EditSlides() {
    $slider = getSlides();
    replaceLine($slider, $_REQUEST['editactionid']);
    //exit;
}

function replaceLine($sliderArr, $replaceId) {
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
                    add_filter('upload_dir', 'slider_upload_dir');
                    $file = wp_handle_upload($image, $override);
                    remove_filter('upload_dir', 'slider_upload_dir');
                    $plugin_options[$keys[$i]] = $file['url'];
                    $name = basename($file['url']); // to get file name
                    $pos = $_POST['select_order'];
					$text = $_POST['add_text'];
                    $slider = getSlides();
                    $total_slides = count($slider);
                    $slidenum = $singlearr['slide_id'];
                    $upload_dir = wp_upload_dir();
                    unlink($upload_dir['basedir'] . '/slider/' . $singlearr['image_name']);
                    $oldline = $slidenum . "#" . $singlearr['image_name'] . "#" . $singlearr['slide_position']."#".$singlearr['slide_text'];
                    $newline = $slidenum . "#" . $name . "#" . $pos ."#".$text."\n";
                    replaceNewLine($oldline, $newline);
                } else {       // Not an image.     
                    $pos = $_POST['select_order'];
					$text = $_POST['add_text'];
                    $slider = getSlides();
                    $total_slides = count($slider);
                    $slidenum = $singlearr['slide_id'];
                    $oldline = $slidenum . "#" . $singlearr['image_name'] . "#" . $singlearr['slide_position']."#".$singlearr['slide_text'];
                    $newline = $slidenum . "#" . $singlearr['image_name'] . "#" . $pos ."#".$text."\n";
                    replaceNewLine($oldline, $newline);
                }
            }   // Else, the user didn't upload a file.  
        }
        $p++;
    }
}

function replaceNewLine($old, $new) {
    $myfile = PLUGIN_FOLDER_PATH . "libs/slider.txt";
    $contents = file_get_contents($myfile);
    $contents = str_replace($old, $new, $contents);
    file_put_contents($myfile, $contents);
    exit(wp_redirect(admin_url('admin.php?page=my-top-level-handle')));
}
?>
		
