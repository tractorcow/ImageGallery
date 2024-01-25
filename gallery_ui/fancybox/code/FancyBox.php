<?php

use SilverStripe\View\Requirements;

class FancyBox extends ImageGalleryUI {

	public static $link_to_demo = "http://fancybox.net/example";

	public static $label = "Fancy Box";

	public $item_template = "TractorCow\\ImageGallery\\Items\\FancyBox_item";

	public function initialize() {
		Requirements::javascript('tractorcow/silverstripe-imagegallery: thirdparty/jquery/jquery.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/fancybox/javascript/jquery.fancybox.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/fancybox/javascript/jquery.pngFix.pack.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/fancybox/javascript/fancybox_init.js');

		Requirements::css('tractorcow/silverstripe-imagegallery: gallery_ui/fancybox/css/fancy.css');
	}

}
