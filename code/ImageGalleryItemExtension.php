 Class ImageGalleryItemExtension extends DataExtension {
    public function onBulkUpload(GridField $gridField) {
        $Album = $gridField->form->getRecord(); 
        if($Album) {
            $this->owner->ImageGalleryPageID = $Album->ImageGalleryPageID;
        }
    }
}
