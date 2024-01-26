<?php

use SilverStripe\View\Requirements;

class NyroModal extends ImageGalleryUI
{
	public static $link_to_demo = "http://nyromodal.nyrodev.com/";
	public static $label = "NyroModal";
	public $item_template = "TractorCow\\ImageGallery\\Items\\NyroModal_item";

	public function initialize()
	{
        Requirements::javascript('tractorcow/silverstripe-imagegallery: thirdparty/jquery/jquery.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/nyro_modal/javascript/jquery.nyroModal.js');
		Requirements::javascript('tractorcow/silverstripe-imagegallery: gallery_ui/nyro_modal/javascript/nyro_modal_init.js');
		Requirements::css('tractorcow/silverstripe-imagegallery: gallery_ui/nyro_modal/css/nyroModal.css');

	}

}
