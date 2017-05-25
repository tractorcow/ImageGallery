<?php

use SilverStripe\View\Requirements;

class LightboxAlt extends ImageGalleryUI
{
	public static $link_to_demo = "http://www.balupton.com/sandbox/jquery_lightbox_bal/demo/";
	public static $label = "LightBox (Balupton edition)";
	public $item_template = "TractorCow\\ImageGallery\\Items\\LightboxAlt_item";

	public function initialize()
	{
        Requirements::javascript('silverstripe-admin/thirdparty/jquery/jquery.js');
		Requirements::javascript('image_gallery/gallery_ui/lightbox_alt/javascript/jquery.color.js');
		Requirements::javascript('image_gallery/gallery_ui/lightbox_alt/javascript/jquery.lightbox.js');
		Requirements::css('image_gallery/gallery_ui/lightbox_alt/css/jquery.lightbox.css');

	}

}
