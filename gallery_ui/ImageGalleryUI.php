<?php

use TractorCow\ImageGallery\Pages\ImageGalleryPage;

abstract class ImageGalleryUI {

	static $link_to_demo;

	public $layout_template = "TractorCow\\ImageGallery\\Includes\\GalleryUI_layout";

	public $item_template = "TractorCow\\ImageGallery\\Items\\GalleryUI_item";

	protected $ImageGalleryPage;

	abstract public function initialize();

	public function setImageGalleryPage(ImageGalleryPage $page) {
		$this->ImageGalleryPage = $page;
	}

	public function updateItems( $items) {
		return $items;
	}

}
