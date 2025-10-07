<?php
	extract(shortcode_atts(array(
		'latmap' => '',
		'longmap' => '',
		'icon_local' => '',
	),$attr));
	$icon_image = wp_get_attachment_image_src($icon_local,'');
	if(isset($icon_image) && !empty($icon_image)){
		$icon = $icon_image[0];
	} else{
		$icon = get_template_directory_uri().'/v2/images/assets/Group.png';
	}
?>
<div id="st_single_map" style="width: 100%; height: 400px"></div>
<script type="text/javascript">
	jQuery(function($){
		$( document ).ready(function() {
			if($('#st_single_map').length){
		        initMapContactPage($('#st_single_map'));
		    }

		    function initMapContactPage(mapEl){
			    var mapStylesContact = [
			        {
			            "elementType": "labels",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        },
			        {
			            "featureType": "administrative",
			            "elementType": "geometry",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        },
			        {
			            "featureType": "administrative.land_parcel",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        },
			        {
			            "featureType": "administrative.neighborhood",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        },
			        {
			            "featureType": "poi",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        },
			        {
			            "featureType": "road",
			            "elementType": "labels.icon",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        },
			        {
			            "featureType": "transit",
			            "stylers": [
			                {
			                    "visibility": "off"
			                }
			            ]
			        }
			    ];
			    var mapLat = <?php echo esc_attr($latmap);?>;
			    var mapLng = <?php echo esc_attr($longmap);?>;

			    var map = new google.maps.Map(mapEl.get(0), {
			        zoom            : 10,
			        center          : {lat: parseFloat(mapLat), lng: parseFloat(mapLng)},
			        disableDefaultUI: true,
			        // styles          : mapStylesContact
			    });

			    new google.maps.Marker({
			        position: new google.maps.LatLng(mapLat, mapLng),
			        icon    : '<?php echo esc_url($icon);?>',
			        map     : map,
			    });
			}
		});
	})
</script>