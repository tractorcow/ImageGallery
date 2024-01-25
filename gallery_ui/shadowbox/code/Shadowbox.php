<?php

use SilverStripe\View\Requirements;

class Shadowbox extends ImageGalleryUI
{
	public static $label = "Shadowbox";
	public static $link_to_demo = "http://www.shadowbox-js.com/";
	public $item_template = "TractorCow\\ImageGallery\\Items\\Shadowbox_item";


	public function initialize()
	{
        Requirements::javascript('tractorcow/silverstripe-imagegallery: thirdparty/jquery/jquery.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/shadowbox/javascript/shadowbox.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/shadowbox/javascript/shadowbox_init.js');
		Requirements::css('tractorcow/silverstripe-imagegallery: gallery_ui/shadowbox/css/shadowbox.css');

	}

}
