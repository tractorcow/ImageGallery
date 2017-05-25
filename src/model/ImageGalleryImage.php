<?php

namespace TractorCow\ImageGallery\Model;


use GD;
use SilverStripe\ORM\DataExtension;


/**
 * Class \TractorCow\ImageGallery\Model\ImageGalleryImage
 *
 * @see Image
 * @property \SilverStripe\Assets\Image|\TractorCow\ImageGallery\Model\ImageGalleryImage $owner
 */
class ImageGalleryImage extends DataExtension
{

    private static $table_name = 'ImageGalleryImage';

    public function generateRotateClockwise(GD $gd)
    {
        return $gd->rotate(90);
    }

    public function generateRotateCounterClockwise(GD $gd)
    {
        return $gd->rotate(270);
    }

    public function Landscape()
    {
        return $this->owner->getWidth() > $this->owner->getHeight();
    }

    public function Portrait()
    {
        return $this->owner->getWidth() < $this->owner->getHeight();
    }

    public function BackLinkTracking()
    {
        return false;
    }
}
