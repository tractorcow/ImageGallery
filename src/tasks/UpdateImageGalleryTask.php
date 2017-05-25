<?php

namespace TractorCow\ImageGallery\Tasks;


use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\Debug;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use TractorCow\ImageGallery\Model\ImageGalleryItem;
use TractorCow\ImageGallery\Pages\ImageGalleryPage;


/**
 * This task searches the folders belonging to image galleries and adds any missing images.
 * This means you can upload images using sftp or rsync etc, then automatically add them in
 * without going through the web interface which is a hassle with 100s of images.
 */
class UpdateImageGalleryTask extends BuildTask
{
    protected $title = 'Update Image Gallery Task';

    protected $description = "Updates the image gallery with all the extra images that have 
		been manually uploaded to the gallery's folder";

    public function run($request)
    {

        // Migrate old ImageGalleryImage class to use Image object
        DB::query('UPDATE "File" SET "File"."ClassName" = \'Image\' WHERE "File"."ClassName" = \'ImageGalleryImage\'');

        // check that galleries exist
        $galleries = DataObject::get(ImageGalleryPage::class);
        if (!$galleries || $galleries->count() === 0) {
            user_error('No image gallery pages found', E_USER_ERROR);
            return;
        }

        // check each gallery
        $count = 0;
        Debug::message("Importing, please wait....");
        foreach ($galleries as $gallery) {
            $albums = $gallery->Albums();
            if (!$albums || $albums->count() === 0) {
                Debug::message("Warning: no album found in gallery '{$gallery->Title}'");
                continue;
            }

            // Check each album in each gallery
            foreach ($albums as $album) {
                $album->write(); // Ensures folder, URLSegment, etc are all prepared
                $folder = $album->Folder();
                $existing = $album->GalleryItems()->column('ImageID');
                foreach ($folder->Children() as $image) {
                    if (in_array($image->ID, $existing)) {
                        continue;
                    }

                    //Add to the album
                    $item = ImageGalleryItem::create();
                    $item->ImageGalleryPageID = $gallery->ID;
                    $item->AlbumID = $album->ID;
                    $item->ImageID = $image->ID;
                    $item->write();
                    $count++;
                }
            }
        }
        Debug::message("Imported $count images into galleries");
    }
}
