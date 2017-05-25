<?php

namespace TractorCow\ImageGallery\Model;


use Colymba\BulkManager\BulkManager;
use Colymba\BulkUpload\BulkUploader;
use GridFieldSortableRows;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
use TractorCow\ImageGallery\Pages\ImageGalleryPage;


/**
 * Class \TractorCow\ImageGallery\Model\ImageGalleryAlbum
 *
 * @property string $AlbumName
 * @property string $Description
 * @property int $SortOrder
 * @property string $URLSegment
 * @property int $CoverImageID
 * @property int $ImageGalleryPageID
 * @property int $FolderID
 * @method \SilverStripe\Assets\Image CoverImage()
 * @method \TractorCow\ImageGallery\Pages\ImageGalleryPage ImageGalleryPage()
 * @method \SilverStripe\Assets\Folder Folder()
 * @method \SilverStripe\ORM\DataList|\TractorCow\ImageGallery\Model\ImageGalleryItem[] GalleryItems()
 */
class ImageGalleryAlbum extends DataObject
{

    private static $table_name = 'ImageGalleryAlbum';

    private static $db = [
        'AlbumName' => 'Varchar(255)',
        'Description' => 'Text',
        'SortOrder' => 'Int',
        'URLSegment' => 'Varchar(255)'
    ];

    private static $has_one = [
        'CoverImage' => Image::class,
        'ImageGalleryPage' => ImageGalleryPage::class,
        'Folder' => Folder::class
    ];

    private static $has_many = [
        'GalleryItems' => ImageGalleryItem::class
    ];

    private static $summary_fields = [
        'CoverImage.CMSThumbnail' => 'Cover Image',
        'AlbumName' => 'Album Name',
        'Description' => 'Description'
    ];

    private static $default_sort = '"SortOrder" ASC';

    public function getTitle()
    {
        if ($this->AlbumName) {
            return $this->AlbumName;
        }
        return parent::getTitle();
    }

    public function getCMSFields()
    {
        $fields = new FieldList(new TabSet('Root'));

        // Details
        $thumbnailField = new UploadField('CoverImage',
            _t('TractorCow\\ImageGallery\\Model\\ImageGalleryAlbum.COVERIMAGE', 'Cover Image'));
        $thumbnailField->getValidator()->setAllowedExtensions(File::config()->app_categories['image']);
        $fields->addFieldsToTab('Root.Main', [
            new TextField('AlbumName',
                _t('TractorCow\\ImageGallery\\Model\\ImageGalleryAlbum.ALBUMTITLE', 'Album Title'), null, 255),
            new TextareaField('Description',
                _t('TractorCow\\ImageGallery\\Model\\ImageGalleryAlbum.DESCRIPTION', 'Description')),
            $thumbnailField
        ]);

        // Image listing
        $galleryConfig = GridFieldConfig_RecordEditor::create();

        // Enable bulk image loading if necessary module is installed
        // @see composer.json/suggests
        if (class_exists(BulkManager::class)) {
            $galleryConfig->addComponent(new BulkManager());
        }
        if (class_exists(BulkUploader::class)) {
            $galleryConfig->addComponents($imageConfig = new BulkUploader(Image::class));
            if ($uploadFolder = $this->Folder()) {
                // Set upload folder - Clean up 'assets' from target path
                $path = preg_replace('/(^' . ASSETS_DIR . '\/?)|(\/$)/i', '', $uploadFolder->RelativePath);
                $imageConfig->setUfSetup('setFolderName', $path);
            }
        }

        // Enable image sorting if necessary module is installed
        // @see composer.json/suggests
        if (class_exists('GridFieldSortableRows')) {
            $galleryConfig->addComponent(new GridFieldSortableRows('SortOrder'));
        }

        $galleryField = new GridField('GalleryItems', 'Gallery Items', $this->GalleryItems(), $galleryConfig);
        $fields->addFieldToTab('Root.Images', $galleryField);

        return $fields;
    }

    public function Link()
    {
        return Controller::join_links(
            $this->ImageGalleryPage()->Link('album'),
            $this->URLSegment
        );
    }

    public function LinkingMode()
    {
        $params = Controller::curr()->getURLParams();
        return (!empty($params['ID']) && $params['ID'] == $this->URLSegment) ? "current" : "link";
    }

    public function ImageCount()
    {
        return $this->GalleryItems()->Count();
    }

    public function FormattedCoverImage()
    {
        $page = $this->ImageGalleryPage();
        return $this->CoverImage()->Fill(
            $page->CoverImageWidth,
            $page->CoverImageHeight
        );
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->checkURLSegment();
        $this->checkFolder();
    }

    public function checkFolder()
    {
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

    public function checkURLSegment()
    {
        $filter = URLSegmentFilter::create();
        $this->URLSegment = $filter->filter($this->AlbumName);
    }

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        $this->GalleryItems()->removeAll();
    }
}
