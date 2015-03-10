<?php

class ImageGalleryAlbum extends DataObject {

	private static $db = array(
		'AlbumName' => 'Varchar(255)',
		'Description' => 'Text',
		'SortOrder' => 'Int',
		'URLSegment' => 'Varchar(255)'
	);

	private static $has_one = array(
		'CoverImage' => 'Image',
		'ImageGalleryPage' => 'ImageGalleryPage',
		'Folder' => 'Folder'
	);

	private static $has_many = array(
		'GalleryItems' => 'ImageGalleryItem'
	);
	
	private static $summary_fields = array(
		'CoverImage.CMSThumbnail' => 'Cover Image',
		'AlbumName' => 'Album Name', 
		'Description' => 'Description'
	);
	
	private static $default_sort = '"SortOrder" ASC';
	
	public function getTitle() {
		if($this->AlbumName) return $this->AlbumName;
		return parent::getTitle();
	}

	public function getCMSFields() {
		$fields = new FieldList(new TabSet('Root'));
		
		// Details
		$thumbnailField = new UploadField('CoverImage',_t('ImageGalleryAlbum.COVERIMAGE','Cover Image'));
		$thumbnailField->getValidator()->setAllowedExtensions(File::config()->app_categories['image']);
		$fields->addFieldsToTab('Root.Main', array(
			new TextField('AlbumName', _t('ImageGalleryAlbum.ALBUMTITLE','Album Title'), null, 255),
			new TextareaField('Description', _t('ImageGalleryAlbum.DESCRIPTION','Description')),
			$thumbnailField
		));
		
		// Image listing
		$galleryConfig = GridFieldConfig_RecordEditor::create();
		
		// Enable bulk image loading if necessary module is installed
		// @see composer.json/suggests
		if(class_exists('GridFieldBulkManager')) {
			$galleryConfig->addComponent(new GridFieldBulkManager());
		}
		if(class_exists('GridFieldBulkUpload')) {
			$galleryConfig->addComponents($imageConfig = new GridFieldBulkUpload('Image'));
			if($uploadFolder = $this->Folder()) {
				// Set upload folder - Clean up 'assets' from target path
				$path = preg_replace('/(^'.ASSETS_DIR.'\/?)|(\/$)/i', '', $uploadFolder->RelativePath);
				$imageConfig->setUfSetup('setFolderName', $path);
			}
		}
		
		// Enable image sorting if necessary module is installed
		// @see composer.json/suggests
		if(class_exists('GridFieldSortableRows')) {
			$galleryConfig->addComponent(new GridFieldSortableRows('SortOrder'));
		}
		
		$galleryField = new GridField('GalleryItems', 'Gallery Items', $this->GalleryItems(), $galleryConfig);
		$fields->addFieldToTab('Root.Images', $galleryField);

		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	public function Link() {
		return Controller::join_links(
			$this->ImageGalleryPage()->Link('album'), 
			$this->URLSegment
		);
	}

	public function LinkingMode() {
		$params = Controller::curr()->getURLParams();
		return (!empty($params['ID']) && $params['ID'] == $this->URLSegment) ? "current" : "link";
	}

	public function ImageCount() {
		return $this->GalleryItems()->Count();
	}

	public function FormattedCoverImage() {
		$page = $this->ImageGalleryPage();
		return $this->CoverImage()->CroppedImage(
			$page->CoverImageWidth,
			$page->CoverImageHeight
		);
	}
	
	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->checkURLSegment();
		$this->checkFolder();
	}

	function checkFolder() {
		// Ensure the album folder exists
		if (!(($folder = $this->Folder()) && $folder->exists())
			&& $this->URLSegment
			&& (($page = $this->ImageGalleryPage()) && $page->exists()) 
			&& (($rootFolder = $page->RootFolder()) && $rootFolder->exists())
		) {
			$folder = Folder::find_or_make("image-gallery/{$rootFolder->Name}/{$this->URLSegment}");
			$this->FolderID = $folder->ID;
		}
	}
	
	public function checkURLSegment() {
		$filter = URLSegmentFilter::create();
		$this->URLSegment = $filter->filter($this->AlbumName);
	}

	function onBeforeDelete() {
		parent::onBeforeDelete();
		$this->GalleryItems()->removeAll();
	}

}
