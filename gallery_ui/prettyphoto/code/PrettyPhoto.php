<?php

use SilverStripe\View\Requirements;

class PrettyPhoto extends ImageGalleryUI
{
	public static $link_to_demo = "http://www.no-margin-for-errors.com/projects/prettyPhoto-jquery-lightbox-clone/#image-gallery-demo";
	public static $label = "Pretty Photo";
	public $item_template = "TractorCow\\ImageGallery\\Items\\PrettyPhoto_item";

	public function initialize()
	{
        Requirements::javascript('silverstripe-admin/thirdparty/jquery/jquery.js');
		Requirements::javascript('image_gallery/gallery_ui/prettyphoto/javascript/jquery.prettyPhoto.js');
		Requirements::javascript('image_gallery/gallery_ui/prettyphoto/javascript/prettyphoto_init.js');
		Requirements::css('image_gallery/gallery_ui/prettyphoto/css/prettyPhoto.css');

	}

}
