<div class="gallery-layout-wrapper">
    <% if $GalleryItems %><% with $GalleryItems %>
        <ul class="gallery-layout" id="gallery-list">
            <% if $NotFirstPage %>
                <% loop $PreviousGalleryItems  %>
                    <li style="display:none;">$GalleryItem</li>
                <% end_loop %>
            <% end_if %>
            <% loop $Me %>
                <li>$GalleryItem</li>
            <% end_loop %>
            <% if $NotLastPage %>
                <% loop $NextGalleryItems %>
                    <li style="display:none;">$GalleryItem</li>
                <% end_loop %>
            <% end_if %>
        </ul>
    <% end_with %><% end_if %>
</div>
