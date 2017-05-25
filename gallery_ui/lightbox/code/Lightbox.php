<?php
use SilverStripe\View\Requirements;


class Lightbox extends ImageGalleryUI
{
	public static $link_to_demo = "http://leandrovieira.com/projects/jquery/lightbox/";
	public static $label = "LightBox";
	public $item_template = "TractorCow\\ImageGallery\\Items\\Lightbox_item";

	public function initialize()
	{
        Requirements::javascript('silverstripe-admin/thirdparty/jquery/jquery.js');
		Requirements::javascript('image_gallery/gallery_ui/lightbox/javascript/jquery.lightbox-0.5.js');
		Requirements::javascript('image_gallery/gallery_ui/lightbox/javascript/lightbox_init.js');
		Requirements::css('image_gallery/gallery_ui/lightbox/css/jquery.lightbox-0.5.css');

	}

}
