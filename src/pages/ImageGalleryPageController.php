<?php

namespace TractorCow\ImageGallery\Pages;


use PageController;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;

/**
 * Class \TractorCow\ImageGallery\Pages\ImageGalleryPageController
 *
 * @property \TractorCow\ImageGallery\Pages\ImageGalleryPage dataRecord
 * @method \TractorCow\ImageGallery\Pages\ImageGalleryPage data()
 * @mixin \TractorCow\ImageGallery\Pages\ImageGalleryPage dataRecord
 */
class ImageGalleryPageController extends PageController
{

    private static $allowed_actions = ['album'];

    public function init()
    {
        parent::init();
        Requirements::themedCSS('ImageGallery');
    }

    public function index()
    {
        if ($this->SingleAlbumView()) {
            return $this->renderWith([$this->getModelClass() . '_album', 'ImageGalleryPage_album', 'Page']);
        } else {
            return $this->renderWith([$this->getModelClass(), ImageGalleryPage::class, 'Page']);
        }
    }

    private function getModelClass()
    {
        return str_replace("_Controller", "", $this->class);
    }

    private function getModel()
    {
        return DataObject::get_by_id($this->getModelClass(), $this->ID);
    }

    protected function adjacentAlbum($dir)
    {
        $currentAlbum = $this->CurrentAlbum();
        if (empty($currentAlbum)) {
            return null;
        }

        $direction = ($dir === "next") ? ">" : "<";
        $sort = ($dir === "next") ? "ASC" : "DESC";
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

    public function NextAlbum()
    {
        return $this->adjacentAlbum("next");
    }

    public function PrevAlbum()
    {
        return $this->adjacentAlbum("prev");
    }

    public function album()
    {
        if (!$this->CurrentAlbum()) {
            return $this->httpError(404);
        }
        return [];
    }
}

