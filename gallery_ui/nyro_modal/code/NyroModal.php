<?php

use SilverStripe\View\Requirements;

class NyroModal extends ImageGalleryUI
{
	public static $link_to_demo = "http://nyromodal.nyrodev.com/";
	public static $label = "NyroModal";
	public $item_template = "TractorCow\\ImageGallery\\Items\\NyroModal_item";

	public function initialize()
	{
        Requirements::javascript('silverstripe-admin/thirdparty/jquery/jquery.js');
		Requirements::javascript('image_gallery/gallery_ui/nyro_modal/javascript/jquery.nyroModal.js');
		Requirements::javascript('image_gallery/gallery_ui/nyro_modal/javascript/nyro_modal_init.js');
		Requirements::css('image_gallery/gallery_ui/nyro_modal/css/nyroModal.css');

	}

}
