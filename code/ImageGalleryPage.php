<?php

class ImageGalleryPage extends Page {
	
	protected $currentAlbum = null;
	
	private static $icon = 'image_gallery/images/image-gallery-icon.png';

	private static $db = array(
		'GalleryUI' => "Varchar(50)",
		'CoverImageWidth' => 'Int',
		'CoverImageHeight' => 'Int',
		'ThumbnailSize' => 'Int',
		'MediumSize' => 'Int',
		'Square' => 'Boolean',
		'NormalSize' => 'Int',
		'NormalHeight' => 'Int',
		'MediaPerPage' => 'Int',
		'UploadLimit' => 'Int'
	);

	private static $has_one = array(
		'RootFolder' => 'Folder'
	);

	private static $defaults = array(
		'CoverImageWidth' => '128',
		'CoverImageHeight' => '128',
		'ThumbnailSize' => '128',
		'Square' => '1',
		'MediumSize' => '400',
		'NormalSize' => '600',
		'MediaPerPage' => '30',
		'MediaPerLine' => '6',
		'UploadLimit' => '20',
		'GalleryUI' => 'Lightbox'
	);

	private static $has_many = array(
		'Albums' => 'ImageGalleryAlbum',
		'GalleryItems' => 'ImageGalleryItem'
	);

	/**
	 * @config
	 * @var string
	 */
	private static $item_class = "ImageGalleryItem";

	/**
	 * @config
	 * @var string
	 */
	private static $album_class = "ImageGalleryAlbum";

	public $UI;

	public function getItemClass() {
		return self::config()->item_class;
	}

	public function getAlbumClass() {
		return self::config()->album_class;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->checkFolder();
	}

	function onBeforeDelete() {
		// check if Page still exists in live mode
		$className = $this->ClassName;
		$livePage = Versioned::get_one_by_stage($className, "Live", "\"{$className}_Live\".\"ID\" = {$this->ID}");
		// check if Page still exists in stage mode
		$stagePage = Versioned::get_one_by_stage($className, "Stage", "\"{$className}\".\"ID\" = {$this->ID}");

		// if Page only exists in Live OR Stage mode -> Page will be deleted completely
		if (!($livePage && $stagePage)) {
			// delete existing Albums 
			$this->Albums()->removeAll();
		}

		parent::onBeforeDelete();
	}

	function checkFolder() {
		// Ensure root folder exists, but avoid saving folders like "new-image-gallery-page"
		if ($this->exists()
			&& !(($folder = $this->RootFolder()) && $folder->exists())
			&& $this->URLSegment
		) {
			$folder = Folder::find_or_make("image-gallery/{$this->URLSegment}");
			$this->RootFolderID = $folder->ID;
		}
	}

	public function getCMSFields() {
		
		// Get list of UI options
		$popupMap = array();
		foreach(ClassInfo::subclassesFor("ImageGalleryUI") as $ui) {
			if ($ui == "ImageGalleryUI") continue;
			
			$uiLabel = $ui::$label;
			$demoURL = $ui::$link_to_demo;
			$demoLink = !empty($demoURL)
					? sprintf('<a href="%s" target="_blank">%s</a>', $demoURL, _t('ImageGalleryPage.VIEWDEMO', 'view demo'))
					: "";
			$popupMap[$ui] = "$uiLabel $demoLink";
		}

		$fields = parent::getCMSFields();
		
		// Build configuration fields
		$fields->addFieldToTab('Root', $configTab = new Tab('Configuration'));
		$configTab->setTitle(_t('ImageGalleryPage.CONFIGURATION', 'Configuration'));
		$fields->addFieldsToTab("Root.Configuration", array(	
			$coverImages = new FieldGroup(
				new NumericField('CoverImageWidth', _t('ImageGalleryPage.WIDTH', 'Width')), 
				new NumericField('CoverImageHeight', _t('ImageGalleryPage.HEIGHT', 'Height'))
			),
			new NumericField('ThumbnailSize', _t('ImageGalleryPage.THUMBNAILHEIGHT', 'Thumbnail height (pixels)')),
			new CheckboxField('Square', _t('ImageGalleryPage.CROPTOSQUARE', 'Crop thumbnails to square')),
			new NumericField('MediumSize', _t('ImageGalleryPage.MEDIUMSIZE', 'Medium size (pixels)')),
			new NumericField('NormalSize', _t('ImageGalleryPage.NORMALSIZE', 'Normal width (pixels)')),
			new NumericField('NormalHeight', _t('ImageGalleryPage.NORMALHEIGHT', 'Normal height (pixels)')),
			new NumericField('MediaPerPage', _t('ImageGalleryPage.IMAGESPERPAGE', 'Number of images per page')),
			new OptionsetField('GalleryUI', _t('ImageGalleryPage.POPUPSTYLE', 'Popup style'), $popupMap),
			new NumericField('UploadLimit', _t('ImageGalleryPage.MAXFILES', 'Max files allowed in upload queue'))
		));
		$coverImages->setTitle(_t('ImageGalleryPage.ALBUMCOVERIMAGES', 'Album cover images'));

		// Build albums tab
		$fields->addFieldToTab('Root', $albumTab = new Tab('Albums'));
		$albumTab->setTitle(_t('ImageGalleryPage.ALBUMS', 'Albums'));
		if ($rootFolder = $this->RootFolder()) {
			$albumConfig = GridFieldConfig_RecordEditor::create();
			// Enable bulk image loading if necessary module is installed
			// @see composer.json/suggests
			if(class_exists('GridFieldBulkManager')) {
				$albumConfig->addComponent(new GridFieldBulkManager());
			}
			// Enable album sorting if necessary module is installed
			// @see composer.json/suggests
			if(class_exists('GridFieldSortableRows')) {
				$albumConfig->addComponent(new GridFieldSortableRows('SortOrder'));
			}
			$albumField = new GridField('Albums', 'Albums', $this->Albums(), $albumConfig);
			$fields->addFieldToTab("Root.Albums", $albumField);
		} else {
			$fields->addFieldToTab(
				"Root.Albums",
				new HeaderField(
					_t("ImageGalleryPage.ALBUMSNOTSAVED", "You may add albums to your gallery once you have saved the page for the first time."),
					$headingLevel = "3"
				)
			);
		}

		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}

	public function CurrentAlbum() {
		if ($this->currentAlbum) return $this->currentAlbum;
		$params = Controller::curr()->getURLParams();
		if (!empty($params['ID'])) {
			return DataObject::get($this->AlbumClass)->filter(array(
				"URLSegment" => $params['ID'],
				"ImageGalleryPageID" => $this->ID
			))->first();
		}
		return false;
	}

	public function AlbumTitle() {
		return $this->CurrentAlbum()->AlbumName;
	}

	public function AlbumDescription() {
		return $this->CurrentAlbum()->Description;
	}

	public function SingleAlbumView() {
		if ($this->Albums()->Count() == 1) {
			$this->currentAlbum = $this->Albums()->First();
			return true;
		}
		return false;
	}

	private static function get_default_ui() {
		$classes = ClassInfo::subclassesFor("ImageGalleryUI");
		foreach ($classes as $class) {
			if ($class != "ImageGalleryUI") return $class;
		}
		return false;
	}

	public function GalleryUI() {
		return $this->GalleryUI
				? $this->GalleryUI
				: self::get_default_ui();
	}

	public function includeUI() {
		if (($ui = $this->GalleryUI()) && ClassInfo::exists($ui)) {
			Requirements::javascript("image_gallery/javascript/imagegallery_init.js");
			$this->UI = Object::create($ui);
			$this->UI->setImageGalleryPage($this);
			$this->UI->initialize();
		}
	}

	protected function Items($limit = null) {
		if ($limit === null && $this->MediaPerPage) {
			if(isset($_REQUEST['start']) && is_numeric($_REQUEST['start'])) {
				$start = $_REQUEST['start'];
			} else {
				$start = 0;
			}

			$limit = "{$start},{$this->MediaPerPage}";
		}

		$items = DataObject::get($this->ItemClass)->sort('"SortOrder" ASC')->limit($limit);
		if($album = $this->CurrentAlbum()) {
			$items = $items->filter('AlbumID', $album->ID);
		}
		return $items;
	}

	public function GalleryItems($limit = null, $items = null) {

		// Check items and UI are ready
		if (empty($items)) $items = new ArrayList($this->Items($limit)->toArray());
		$this->includeUI();
		
		// Prepare each item
		foreach ($items as $item) {
			
			// Thumbnail details
			$thumbImg = $item->Thumbnail();
			$item->ThumbnailURL = $thumbImg->URL;
			$item->ThumbnailWidth = $thumbImg->getWidth();
			$item->ThumbnailHeight = $thumbImg->getHeight();

			// Normal details
			$normalImg = $item->Large();
			$item->ViewLink = $normalImg->URL;
			
			// Propegate UI
			$item->setUI($this->UI);
		}
		return $this->UI->updateItems($items);
	}

	public function PreviousGalleryItems() {
		if (isset($_REQUEST['start']) && is_numeric($_REQUEST['start']) && $this->MediaPerPage) {
			return $this->GalleryItems("0, " . $_REQUEST['start']);
		}
		return false;
	}

	public function NextGalleryItems() {
		if (isset($_REQUEST['start']) && is_numeric($_REQUEST['start']) && ($_REQUEST['start'] > 0) && $this->MediaPerPage) {
			return $this->GalleryItems($_REQUEST['start'] + $this->MediaPerPage . ",999");
		}
		return $this->GalleryItems($this->MediaPerPage . ",999");
	}

	public function AllGalleryItems() {
		return $this->GalleryItems("0,999");
	}

	public function GalleryLayout() {
		return $this->customise(array(
			'GalleryItems' => $this->GalleryItems(),
			'PreviousGalleryItems' => $this->PreviousGalleryItems(),
			'NextGalleryItems' => $this->NextGalleryItems()
		))->renderWith(array($this->UI->layout_template));
	}

}

class ImageGalleryPage_Controller extends Page_Controller {
	
	private static $allowed_actions = array('album');

	public function init() {

		parent::init();
		Requirements::themedCSS('ImageGallery');
	}

	public function index() {
		if ($this->SingleAlbumView()) {
			return $this->renderWith(array($this->getModelClass() . '_album', 'ImageGalleryPage_album', 'Page'));
		} else {
			return $this->renderWith(array($this->getModelClass(), 'ImageGalleryPage', 'Page'));
		}
	}

	private function getModelClass() {
		return str_replace("_Controller", "", $this->class);
	}

	private function getModel() {
		return DataObject::get_by_id($this->getModelClass(), $this->ID);
	}

	protected function adjacentAlbum($dir) {
		$currentAlbum = $this->CurrentAlbum();
		if(empty($currentAlbum)) return null;
		
		$direction = ($dir == "next") ? ">" : "<";
		$sort = ($dir == "next") ? "ASC" : "DESC";
		$parentID = Convert::raw2sql($this->ID);
		$adjacentID = Convert::raw2sql($currentAlbum->ID);
		$adjacentSort = Convert::raw2sql($currentAlbum->SortOrder);
		// Get next/previous album by sort (or ID if sort values haven't been set)
		$filter =
			"\"ImageGalleryAlbum\".\"ImageGalleryPageID\" = '$parentID' AND
			\"ImageGalleryAlbum\".\"SortOrder\" {$direction} '$adjacentSort' OR (
				\"ImageGalleryAlbum\".\"SortOrder\" = '$adjacentSort'
				AND \"ImageGalleryAlbum\".\"ID\" {$direction} '$adjacentID'
			)";
		return DataObject::get_one($this->AlbumClass, $filter, false, "\"SortOrder\" $sort, \"ID\" $sort");
	}

	public function NextAlbum() {
		return $this->adjacentAlbum("next");
	}

	public function PrevAlbum() {
		return $this->adjacentAlbum("prev");
	}

	public function album() {
		if (!$this->CurrentAlbum()) {
			return $this->httpError(404);
		}
		return array();
	}

}
