<?php
use SilverStripe\View\Requirements;


class Lightbox extends ImageGalleryUI
{
	public static $link_to_demo = "http://leandrovieira.com/projects/jquery/lightbox/";
	public static $label = "LightBox";
	public $item_template = "TractorCow\\ImageGallery\\Items\\Lightbox_item";

	public function initialize()
	{
        Requirements::javascript('tractorcow/silverstripe-imagegallery: thirdparty/jquery/jquery.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/lightbox/javascript/jquery.lightbox-0.5.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/lightbox/javascript/lightbox_init.js');
		Requirements::css('tractorcow/silverstripe-imagegallery: gallery_ui/lightbox/css/jquery.lightbox-0.5.css');
	}

}
