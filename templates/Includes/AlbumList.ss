<div id="album-list">
    <ul>
        <% loop $Albums %>
            <li>
                <div class="defaultImage">
                    <a href="$Link" title="$Title">
                        <% if $CoverImage %>
                            <% with $FormattedCoverImage %>
                                <img src="$URL" alt=""/>
                            <% end_with %>
                        <% else %>
                            <span class="no-image"></span>
                        <% end_if %>
                    </a>
                </div>
                <div class="galleryDetails">
                    <h4><a href="$Link" title="$Title">$AlbumName</a>
                        (<% sprintf(_t('AlbumList.IMAGECOUNT','%s photos'), $ImageCount) %>)</h4>
                    <div class="galleryDescription">$Description.LimitWordCount(60)</div>
                </div>
            </li>
        <% end_loop %>
    </ul>
</div>
