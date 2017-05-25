<div id="primaryContent">
	<div class="innerpad">
		<div class="typography">
			<% if $Albums %>
				<div id="Sidebar" class="typography">
					<div class="sidebarBox">
						<h3>$Title</h3>
						<ul id="Menu2">
						<% loop $Albums %>
							<li class="$LinkingMode"><a class="$LinkingMode" href="$Link" title="$AlbumName">$AlbumName</a></li>
						<% end_loop %>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="sidebarBottom"></div>
				</div>
				<div id="Content">
					<% include AlbumImages %>
				</div>
			<% else %>
				<% include AlbumImages %>
			<% end_if %>
		</div>
	</div>
</div>
