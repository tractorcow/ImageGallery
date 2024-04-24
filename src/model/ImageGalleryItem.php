<?php

namespace TractorCow\ImageGallery\Model;


use ImageGalleryUI;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Image_Backend;
//use SilverStripe\Core\Object;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use TractorCow\ImageGallery\Pages\ImageGalleryPage;


/**
 * Class \TractorCow\ImageGallery\Model\ImageGalleryItem
 *
 * @property string $Caption
 * @property int $SortOrder
 * @property int $ImageGalleryPageID
 * @property int $AlbumID
 * @property int $ImageID
 * @method \TractorCow\ImageGallery\Pages\ImageGalleryPage ImageGalleryPage()
 * @method \TractorCow\ImageGallery\Model\ImageGalleryAlbum Album()
 * @method \SilverStripe\Assets\Image Image()
 */
class ImageGalleryItem extends DataObject
{

    private static $table_name = 'ImageGalleryItem';

    /**
     * User interface for gallery
     *
     * @var ImageGalleryUI
     */
    protected $UI;

    /**
     * @config
     * @var string
     */
    private static $delete_permission = "CMS_ACCESS_CMSMain";

    public function getTitle()
    {
        if ($this->Caption) {
            return $this->dbObject('Caption')->FirstSentence();
        }
        if ($image = $this->Image()) {
            return $image->Title;
        }
        return parent::getTitle();
    }

    private static $db = [
        'Caption' => 'Text',
        'SortOrder' => 'Int'
    ];

    private static $has_one = [
        'ImageGalleryPage' => ImageGalleryPage::class,
        'Album' => ImageGalleryAlbum::class,
        'Image' => Image::class
    ];

    private static $default_sort = '"SortOrder" ASC';

    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Caption' => 'Image Caption'
    ];

    public function getCMSFields()
    {
        $fields = new FieldList(new TabSet('Root'));

        // Details
        $fields->addFieldToTab('Root.Main',
            new TextareaField('Caption', _t('TractorCow\\ImageGallery\\Model\\ImageGalleryItem.CAPTION', 'Caption')));

        // Create image
        $imageField = new UploadField('Image');
        $imageField->getValidator()->setAllowedExtensions(File::config()->app_categories['image']);
        $fields->addFieldToTab('Root.Main', $imageField);

        return $fields;
    }

    public function Thumbnail()
    {
        $page = $this->ImageGalleryPage();
        $image = $this->Image();
        if (!$image) {
            return null;
        }

        if ($page->Square) {
            return $image->Fill($page->ThumbnailSize, $page->ThumbnailSize);
        }

        return $image->ScaleHeight($page->ThumbnailSize);

    }

    public function Medium()
    {
        $page = $this->ImageGalleryPage();
        $image = $this->Image();
        if (!$image) {
            return null;
        }

        return $image->Fit($page->MediumSize, $page->MediumSize);
    }

    public function Large()
    {
        $page = $this->ImageGalleryPage();
        $image = $this->Image();
        if (!$image) {
            return null;
        }

        if ($image->getOrientation() === Image_Backend::ORIENTATION_LANDSCAPE) {
            return $image->ScaleWidth($this->ImageGalleryPage()->NormalSize);
        }

        $height = $page->NormalHeight > 0
            ? $page->NormalHeight
            : $page->NormalSize;
        return $image->ScaleHeight($height);

    }

    public function setUI(ImageGalleryUI $ui)
    {
        $this->UI = $ui;
    }

    /**
     * @todo: make UI a global config setting
     *
     * @return bool|\SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function GalleryItem()
    {
        $this->UI = Injector::inst()->create($this->ImageGalleryPage()->GalleryUI());

        if ($this->UI) {
            return $this->renderWith([$this->UI->item_template]);
        }
        return false;
    }

    public function canDelete($member = null)
    {
        return Permission::check(self::config()->delete_permission, 'any', $member);
    }
}
