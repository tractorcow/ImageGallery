<table>
	<% loop $CurrentGalleryItems %>
		<% if $First || $FirstItemLine %>
			<tr>
		<% end_if %>
		<td>
			<a id="ViewLink-$ID" rel="$RelAttr" class="$ClassAttr" title="$Caption" href="$ViewLink"><img src="$ThumbnailURL" alt="$Title"/></a>
		</td>
		<% if $Last || $LastItemLine %>
			</tr>
		<% end_if %>
	<% end_loop %>
</table>
