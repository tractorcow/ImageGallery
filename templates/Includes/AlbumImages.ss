<h2>$AlbumTitle</h2>
$GalleryLayout
<div class="album-nav">
	<ul>
	<% if $PrevAlbum %>
		<% with PrevAlbum %>
			<li class="prev">
				<div class="album-nav-img"><a href="$Link" title="<% sprintf(_t('GOTOALBUM','Go to the %s album'),$AlbumName) %>">$CoverImage.SetWidth(50)</a></div>
				<div class="album-nav-desc">
					<h4><% _t('PREVIOUSALBUM','Previous Album') %>:</h4>
					<h5><a href="$Link">$AlbumName</a></h5>
				</div>
			</li>
		<% end_with %>
	<% end_if %>
	<% if $NextAlbum %>
		<% with $NextAlbum %>
			<li class="next">
				<div class="album-nav-img"><a href="$Link" title="<% sprintf(_t('GOTOALBUM','Go to the %s album'),$AlbumName) %>">$CoverImage.SetWidth(50)</a></div>
				<div class="album-nav-desc">
					<h4><% _t('NEXTALBUM','Next Album') %>:</h4>
					<h5><a href="$Link">$AlbumName</a></h5>
				</div>
			</li>
		<% end_with %>
	<% end_if %>
	</ul>
</div>
<% with $GalleryItems %>
	<% if $MoreThanOnePage %>
		<ul id="pagination-imagegallery">
			<% if $NotFirstPage %>
				<li class="previous"><a title="<% _t('VIEWPREVIOUSPAGE','View the previous page') %>" href="$PrevLink">&laquo;<% _t('PREVIOUS','Previous') %></a></li>
			<% else %>
				<li class="previous-off">&laquo;<% _t('PREVIOUS','Previous') %></li>
			<% end_if %>

			<% loop $Pages %>
				<% if $CurrentBool %>
					<li class="active">$PageNum</li>
				<% else %>
					<li><a href="$Link" title="<% sprintf(_t('VIEWPAGENUMBER','View page number %s'),$PageNum) %>">$PageNum</a></li>
				<% end_if %>
			<% end_loop %>

			<% if $NotLastPage %>
				<li class="next"><a title="<% _t('VIEWNEXTPAGE', 'View the next page') %>" href="$NextLink"><% _t('NEXT','Next') %> &raquo;</a></li>
			<% else %>
				<li class="next-off"><% _t('NEXT','Next') %> &raquo;</li>
			<% end_if %>
		</ul>
	<% end_if %>
<% end_with %>
