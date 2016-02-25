<?php

function slider_slider_shortcode() {

    return slider_slider_view();
}

add_shortcode('flat-slider', 'slider_slider_shortcode');

function slider_slider_view() {
    $myfile = fopen(plugin_dir_path(__FILE__) . "ffsb-slider.txt", "r") or die("Unable to open file!");
    // Output one line until end-of-file
    $slider = array();
    $i = 0;
    while (!feof($myfile)) {
        $keys = array();
			 $line_of_file =  fgets($myfile);
		 if (preg_match("/#/", $line_of_file , $matches)){
				$myslide = explode("#", $line_of_file);
			}
			else{
				continue;
			}
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
    <div id="slider-slider-id" class="carousel slide" data-ride="carousel">

        <div class="carousel-inner" role="listbox">
    <?php

    $upload_dir = wp_upload_dir();

    $slide_number = get_option('number_slides');
    if ($slide_number == '') {
        $slide_number = count($slider);
    }
    if ($slide_number > count($slider)) {
        $slide_number = count($slider);
    }

    for ($i = 0; $i < $slide_number; $i++) {
        ?>
                <div class="item <?php echo $i == 0 ? 'active' : ''; ?>">
                    <img src="<?php echo esc_url($upload_dir['baseurl'] . '/ffsb-slider/' . $slider[$i]['image_name']); ?>" />
					
					 	<div class="carousel-caption">
								<?php if(isset($slider[$i]['slide_text'])){ ?>
									<h2 class="slider-title-sm"><?php echo esc_html($slider[$i]['slide_text']); ?></h2>
								<?php }?>
						</div>
                </div> <!--  items -->
        <?php
    }
    //end while
    ?>
        </div>   <!--  carousel-inner -->
            <?php if (count($slides) > 1) { ?>
            <ol class="carousel-indicators">
            <?php foreach ($slides as $key => $slide) { ?>
                    <li data-target="#slider-slider-id" data-slide-to="<?php echo esc_attr($key); ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>></li>
            <?php } ?>
            </ol>
            <?php } ?>

        <a class="left carousel-control" href="#slider-slider-id" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#slider-slider-id" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>


    </div>


    <?php
    wp_reset_postdata();
    wp_reset_query();
}
?>
