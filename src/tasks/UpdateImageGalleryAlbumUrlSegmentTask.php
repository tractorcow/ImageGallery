<?php

namespace TractorCow\ImageGallery\Tasks;


use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\Debug;
use TractorCow\ImageGallery\Model\ImageGalleryAlbum;

class UpdateImageGalleryAlbumUrlSegmentTask extends BuildTask
{
    protected $title = 'Update Image Gallery Album URLSegment Task';

    protected $description = "Searches for Albums without URLSegment set and generates it";

    public function run($request)
    {
        $count = 0;

        /** @var DataList|ImageGalleryAlbum[] $albumsWithoutURLSegments */
        $albumsWithoutURLSegments = ImageGalleryAlbum::get()->where('"URLSegment" IS NULL');

        foreach ($albumsWithoutURLSegments as $album) {
            $album->write();

            Debug::message($album->Title . ' updated');

            $count++;
        }

        Debug::message("Updated $count albums");
    }
}
