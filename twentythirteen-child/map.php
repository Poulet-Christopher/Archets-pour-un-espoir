<div id="map" style="height:380px;width:500px"></div>
<script type="text/javascript">
	map = L.map('map', {
		center: [46, 0.8],
		zoom: 5
	});
	
L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
</script>

<?php
	echo '<script type="text/javascript">';
	$marque = getMarkerList();
	echo $marque;
	echo '</script>';
?>

<script type="text/javascript">
map.on('popupopen',function(e){
	var post_id = e.popup.post_id;
	var nonce ='<?php print wp_create_nonce("popup_content"); ?>';
	jQuery.post("<?php print admin_url('admin-ajax.php') ?>",{action: 'popup_content',
	post_id: post_id, nonce: nonce},function(response){
		$args = array
		response = get_posts();
		console.log("resp",response);
		e.popup.setContent(response);
	});
});
</script>