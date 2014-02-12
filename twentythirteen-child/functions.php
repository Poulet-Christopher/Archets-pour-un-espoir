<?php

	add_action('pre_get_posts','display_concerts');
	
	function display_concerts($query){
		
		if($query->is_front_page() && $query->is_main_query())
		{
			$query->set('post_type', array('concert'));
			
			//10 dernieres annees
			//$query->set('date_query', array('year' => getdate()['year']-10, 'compare' => '>='));
			
			//le lieu n'est pas specifie
			//$query->set('meta_query', array(array('key'=>'wpcf-lieu','valu' => false, 'type' => BOOLEAN)));
			
			//qui possede une image à la une
			//$query->set('meta_query', array(array('key'=>'_thumbnail_id','compare' => 'EXISTS')));
			
			//entre 2006 et 2008
			$query->set('date_query', array('year' => '2006', 'compare' => '>='));
			$query->set('date_query', array('year' => '2008', 'compare' => '<='));
			
			return;
		}
		
	}
	
	function add_dashboard_function(){
		echo 'Actions non localisé: ';/*
		$args = array('post_type'=>"action", 'tax_query'=> array(array('key'=>'wpcf-lieu','valu' => false, 'type' => BOOLEAN)));		
		$query = new WP_Query($args);
		$count=$query->post_count;		
		echo $count;*/
		
		echo '<br>Concerts non localisé: ';		
		$args = array('post_type'=>"concert", 'meta_query'=> array(array('key'=>'wpcf-lieu','valu' => false, 'type' => BOOLEAN)));		
		$query = new WP_Query($args);
		$count=$query->post_count;		
		echo $count;
		
	}
	
	function add_dashboard_widgets(){
		wp_add_dashboard_widget('dashboard_widget', 'Actions et Concerts sans lieu', 'add_dashboard_function');
	}
	
	add_action('wp_dashboard_setup', 'add_dashboard_widgets');
	
	
	function geolocalize($post_id){
		if(wp_is_post_revision($post_id))
			return;
		$post = get_post($post_id);
		if(!in_array($post->post_type, array('concert')))
			return;
		$lieu = get_post_meta($post_id, 'wpcf-lieu', true);
		if(empty($lieu))
			return;
		$lat=get_post_meta($post_id,'lat', true);
		if(empty($lat))
		{
			$address = $lieu.',France';
			$result=doGeolocation($address);
			if(false === $result)
				return;
			try{
				$location=$result[0]['geometry']['location'];
				add_post_meta($post_id, 'lat', $location["lat"]);
				add_post_meta($post_id, 'lng', $location["lng"]);
			}
			catch(Exception $e)
			{
				return;
			}
		}
	}
	
	add_action('save_post','geolocalize');
	
	function doGeolocation($address){
		$url="http://maps.google.com/maps/api/geocode/json?sensor=false&"."address=".urlencode($address);
		if($json = file_get_contents($url)){
			$data = json_decode($json, TRUE);
			if($data['status']=="OK"){
				return $data['results'];
			}
		}
		return false;
	}
	
function load_scripts(){
	if(!is_post_type_archive('concert') && ! is_post_type_archive('action'))
		return;
	
	wp_register_script(
		'leaflet-js',
		'http://cdn.leafletjs.com/leaflet-0.7.1/leaflet.js'
		)
	;
	wp_enqueue_script('leaflet-js');
}
add_action('wp_enqueue_scripts', 'load_scripts');

function load_css(){
	if(!is_post_type_archive('concert') && ! is_post_type_archive('action'))
		return;
	
	wp_register_style(
		'leaflet-css',
		'http://cdn.leafletjs.com/leaflet-0.7.1/leaflet.css'
		)
	;
	wp_enqueue_style('leaflet-css');
}
add_action('wp_enqueue_scripts', 'load_css');
	
function getPostWithLatLon($post_type = "concert")
{
	global $wpdb;
	$query = "
	SELECT
	ID, post_title, post_date, p1.meta_value as lat, p2.meta_value as lng
	FROM wp_archet__posts, wp_archet__postmeta as p1, wp_archet__postmeta as p2
	WHERE wp_archet__posts.post_type = 'concert'
	AND p1.post_id = wp_archet__posts.ID
	AND p2.post_id = wp_archet__posts.ID
	AND p1.meta_key = 'lat'
	AND p2.meta_key = 'lng'";
	$results = $wpdb -> get_results($query);
	return $results;
}

function getMarkerList($post_type = "concert")
{
	$results = getPostWithLatLon($post_type);
	$array = array();
	foreach($results as $result)
	{
		$array[] = "var marker_".$result->ID." = L.marker([".$result->lat.", ".$result->lng."]).addTo(map);";
		$array[] = "var popup_".$result->ID." = L.popup().setContent('chargement...')";
		$array[] = "L.popup().setContent('".$result->post_title." ".$result->post_date."');";
		$array[] = "marker_".$result->ID.".bindPopup('".$result->post_title." ".$result->post_date."');";
	}
	return implode(PHP_EOL, $array);
}

add_action("wp_ajax_popup_content","get_content");
add_action("wp_ajax_nopriv_popup_content","get_content");

function get_content(){
	if(!wp_verify_nonce($_REQUEST['nonce'],"popup_content")){
		exit("d'ou vien cette requete?");
	}
	else
	{
		$post_id = $_REQUEST["post_id"];
		print $post_id;
	}
	die();
}

?>
