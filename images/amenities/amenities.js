// JavaScript Document
// JavaScript Document
var map;
var markersList = [];
var sitemarker;
var amenities_infowindow;
var infoWindowWidth = "auto";
var infoWindowHeght = "auto";
var initVis;
var nav = [];
var colnumber = 1;
var htmlappend = "";
var mapStyles =[
  {
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "featureType": "landscape",
    "stylers": [
      { "visibility": "on" },
      { "lightness": -100 }
    ]
  },{
    "featureType": "water",
    "stylers": [
      { "visibility": "on" }
    ]
  },{
    "featureType": "administrative.locality",
    "elementType": "labels.text.fill",
    "stylers": [
      { "visibility": "on" }
    ]
  },{
    "featureType": "administrative.locality",
    "elementType": "labels.text.fill",
    "stylers": [
      { "color": "#f8b100" }
    ]
  },{
    "featureType": "administrative.locality",
    "elementType": "labels.text.stroke",
    "stylers": [
      { "visibility": "on" }
    ]
  },{
    "featureType": "administrative.locality",
    "elementType": "labels.text.stroke",
    "stylers": [
      { "lightness": -100 }
    ]
  },{
    "featureType": "road.highway",
    "stylers": [
      { "visibility": "on" }
    ]
  },{
    "featureType": "road.highway",
    "elementType": "geometry.fill",
    "stylers": [
      { "lightness": 100 }
    ]
  },{
    "featureType": "road.highway",
    "elementType": "geometry.stroke",
    "stylers": [
      { "lightness": 100 }
    ]
  },{
    "featureType": "road.highway",
    "elementType": "labels.text.fill",
    "stylers": [
      { "lightness": -100 }
    ]
  },{
    "featureType": "road.highway",
    "elementType": "labels.text.stroke",
    "stylers": [
      { "lightness": 100 }
    ]
  },{
    "featureType": "road.arterial",
    "stylers": [
      { "visibility": "on" }
    ]
  },{
    "featureType": "road.arterial",
    "elementType": "labels.text.fill",
    "stylers": [
      { "lightness": -100 }
    ]
  },{
    "featureType": "road.arterial",
    "elementType": "labels.text.stroke",
    "stylers": [
      { "lightness": 100 }
    ]
  }
];
var localpath = "images/amenities/";
jQuery(document).ready(function(){
	
	console.log('call');
	// Set Google Map Canvas and Legend Initial Height for Sean only
	/*
	if(jQuery('#map_canvas').attr('id')){
		if(jQuery(window).width() >= 768) {
			var mapheight = jQuery('.itemListCategoryInner').height() - jQuery('.itemListCategoryInner h2').height() - parseFloat(jQuery('.itemListCategoryInner h2').css('paddingBottom'));
			jQuery('#map_canvas').css('height',mapheight +'px');
			jQuery('#map_legend').css('height',mapheight+'px');
		} else {
			jQuery('#map_canvas').css('height',jQuery(window).width() +'px');
			jQuery('#map_legend').removeAttr('style');
		}
	};
	*/
	jQuery.get(localpath+'amenities.xml', function(data){
		var initialZoom = Number(jQuery(data).find("InitialZoom").text());
		var centerLatLng = jQuery(data).find("coordinates").first().text();
		centerLatLng = centerLatLng.split(',');
		//initialise a map
		var latlng = new google.maps.LatLng(centerLatLng[0],centerLatLng[1]);
		var myOptions = {
			zoom: initialZoom,
			center: latlng,
			mapTypeControl: true,
			overviewMapControl: true,
			overviewMapControlOptions: {
				opened: false
			},
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scrollwheel: true
		};
		map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
		//map.setOptions({styles: mapStyles});
		// Loop thru sites
		jQuery(data).find("Site").each(function(index, value){
			sitemarker = createMarker(this);
			sitemarker.infowindow.open(map, sitemarker);
		});
		
		initVis = jQuery(data).find("visibility").first().text();
		appendNav();
		var folderindex=0;
		
		
		if(jQuery(window).width() >= 768) {
			//loop through folder tags
			jQuery(data).find("Folder").each(function(index, value){
				//get the name of the Folder
				name = jQuery(this).find("name").first().text();
				icon = localpath + jQuery(this).find("Icon").first().text();
				visibility = jQuery(this).find("visibility").first().text();
				fopen = jQuery(this).find("open").first().text();
				//store as JSON
				nav.push({
					"folder": name,
					"icon": icon,
					"visibility": visibility,
					"copen": fopen,
					"places": []
				})
				appendNav(folderindex);
				//loop through placemark tags
				var navindex=0;
				jQuery(this).find("Placemark").each(function(index, value){
					marker = createMarker(this,icon,visibility);
					var coords = jQuery(this).find("coordinates").text();
					coords = coords.split(',');
					//store as JSON
					nav[folderindex].places.push({
						"place": marker.name,
						"lat": coords[0],
						"lng": coords[1],
						"descr": marker.descr,
						"icon": marker.icon,
						"visibility": marker.visibility,
						"w_w": marker.w_w,
						"w_h": marker.w_h
					})
					
					nav[folderindex].places[navindex]["marker"] = marker;
					appendNav(folderindex,navindex);
					navindex++;
				})
				appendNav(-1);
				folderindex++;
			})
			appendNav(-2);
			jQuery("#map_legend div.category span.controlButton").bind("click", categoryMenu);
			jQuery("#map_legend div.category span.controlButton").bind("click", showhideMarkers);
			jQuery("#map_legend ul li").bind("click", showInfoandPan);
			jQuery("#map_legend ul.categoryItemsList div.placeControlButton").bind("click", showhideMarkers);
			jQuery("#map_legend div.controlButton").bind("click", showhideMarkers);
		}
	});
});

function createMarker(obj,parenticon,parentvisibility){	

	//get sites visibility
	visibility = jQuery(obj).find("visibility").first().text();
	if(!visibility)visibility=parentvisibility;
	
	// get place name
	var name = jQuery(obj).find("name").text();
	//get place description
	var descr = jQuery(obj).find("description").text();
	if(descr) {
		var cdatafix = descr.indexOf("]]>") ;
		if(cdatafix != -1) descr = descr.substr(0,cdatafix);
	}
						
	//get place coordinates
	var coords = jQuery(obj).find("coordinates").text();
	coords = coords.split(',');
	var latlng = new google.maps.LatLng(coords[1],coords[0]);
	
	//get place link
	var linkedpage = jQuery(obj).find("link").first().text();
	
	//get marker image
	var markerimage = jQuery(obj).find("marker").first().text();
	if(!markerimage) {
		if(parenticon) {
			markerimage = parenticon;
		} else {
			markerimage = localpath + "marker.png";
		}
	} else {
		markerimage = localpath + markerimage;
	}
	var ws = jQuery(obj).find("windowsize");
	if(ws.length!=0){
		var w= ws.text().split(",");
		var w_w = w[0];
		var w_h = w[1];
	} else {
		var w_w = infoWindowWidth;
		var w_h = infoWindowHeght;
	}
	var marker = new google.maps.Marker({
		draggable: false,
		clickable: false,
		raiseOnDrag: false,
		animation: google.maps.Animation.DROP,
		position: latlng,
		icon: markerimage,
		map: map,
		name: name,
		descr: descr,
		w_w: w_w,
		w_h: w_h,
		amenitylink: linkedpage
	});
	if(!visibility || visibility=="hide") {
		marker.setMap(null);
	}
	if(name || descr) {
		marker.clickable = true;
		var infowrapperstyle = 'style="';
		if(w_w) {
			infowrapperstyle +='width:'+w_w;
			if(w_w!='auto') infowrapperstyle +='px';
			infowrapperstyle +=';';
		}	
		
		if(w_h) {
			infowrapperstyle +='height:'+w_h;
			if(w_h!='auto') infowrapperstyle +='px';
			infowrapperstyle +=';';
		}	
		infowrapperstyle += '"'; 
		var info = '<div class="infowindowContent"';
		if(infowrapperstyle != 'style=""') info += infowrapperstyle;
		info += '>';
		if(name) info += '<div class="infowindowTitle">'+name+'</div>';
		if(descr) info += '<div class="infowindowDescription">'+descr+'</div>';
		info += '</div>';
		marker.infowindow = new google.maps.InfoWindow({
			content: info
		});
		//marker.infowindow.open(map, marker);
		google.maps.event.addListener(marker, 'click', function() {
			hideallInfoWindows();
			this.infowindow.open(map, marker);
		});
	}
	if(linkedpage) {
		marker.clickable = true;	
		google.maps.event.addListener(marker, 'click', function() {
			window.location.href = this.amenitylink;  
		});
	}
	return marker;
}
function appendNav(folderindex, navindex){
	if(arguments.length == 0){
		//htmlappend += "<div id='toggleall' class='controlButton navigationbutton"+((initVis == "show")?" active":"")+"'>"+((initVis == "show")?"Hide all":"Show all")+"</div>";
		htmlappend += "<div id='amenitieswrapper'>";
	} else if(arguments.length == 1) {
		if(folderindex == -1){
			htmlappend += "</ul></div>"
		} else if(folderindex == -2) {
			htmlappend += "<div style='clear:both;'></div>";
			jQuery("#map_legend").append(htmlappend);	
		} else {
			var catVisible = (nav[folderindex].visibility!="hide" && initVis!="hide")?"active":"";
			var catOpen = (nav[folderindex].copen!="0")?" active":"";
			htmlappend += "<div class='categoryWrapper col"+(folderindex%colnumber+1)+"'><div id='cat_"+folderindex+"' class='controlButton"+" "+catVisible+"'>"+((catVisible == "active")?"Hide":"Show")+"</div><div class='category'><span class='controlButton"+catOpen+"' id='catcontrol_"+folderindex+"'><img src='"+nav[folderindex].icon+"' class='foldericon' /><span>"  + nav[folderindex].folder+"</span></span></div>";
			htmlappend += "<ul id='cat_"+folderindex+"_Items' class='categoryItemsList"+catOpen+"'>";
		}
	} else if(arguments.length == 2) {
			var checkboxChecked = (nav[folderindex].places[navindex].visibility!="hide")?"active":"";
			htmlappend += "<li id='"+folderindex+"_"+navindex+"' class='col"+(navindex%colnumber+1)+"'><div class='placeControlButton "+checkboxChecked+"' "+" id='place_"+folderindex+"_"+navindex+"'></div><img  src='"+nav[folderindex].places[navindex].icon+"' class='listicon' /><span id='placename_"+folderindex+"_"+navindex+"'>"+nav[folderindex].places[navindex].place+"</span></li>";
	}
}
function showInfoandPan() {
	var placemarkindex = jQuery(this).attr("id");
	if(placemarkindex){
		var placemarkindexes = placemarkindex.split("_");
		var marker =nav[parseInt(placemarkindexes[0])].places[parseInt(placemarkindexes[1])].marker;
		if(marker.getMap() != null) {
			hideallInfoWindows();
			marker.infowindow.open(map, marker);
			var panToPoint = new google.maps.LatLng(nav[parseInt(placemarkindexes[0])].places[parseInt(placemarkindexes[1])].lng, nav[parseInt(placemarkindexes[0])].places[parseInt(placemarkindexes[1])].lat);
			map.panTo(panToPoint);
		}
	}
}
function categoryMenu() {
	jQuery(this).parent().next('ul.categoryItemsList').toggle('slow');
}
function showhideMarkers(){
	var thisId = jQuery(this).attr("id");
	var thisIdSplit = thisId.split("_");
	if(thisIdSplit[0]=="cat"){
		jQuery(this).text(jQuery(this).hasClass('active')?"Hide":"Show");
		jQuery(this).toggleClass('active');
		var category = nav[parseInt(thisIdSplit[1])].places;
		for(var j=0; j < category.length; j++){
			var marker = category[j].marker;
			if(jQuery(this).hasClass("active")) {
				if(marker.getMap()==null){
					marker.setMap(map);
					jQuery("li#"+thisIdSplit[1]+"_"+j).bind("click", showInfoandPan);
					jQuery("li#"+thisIdSplit[1]+"_"+j+" .placeControlButton").addClass("active");
				}
			} else {
				if(marker.getMap()!=null){
					marker.infowindow.close();
					marker.setMap(null);
					jQuery("li#"+thisIdSplit[1]+"_"+j).unbind("click", showInfoandPan)
					jQuery("li#"+thisIdSplit[1]+"_"+j+" .placeControlButton").removeClass("active");
				}
			}
		}
	} else if(thisIdSplit[0]=="catcontrol") {
		jQuery(this).toggleClass('active');
		/*
		hideallMarkers();
		var category = nav[parseInt(thisIdSplit[1])].places;
		for(var j=0; j < category.length; j++){
			var marker = category[j].marker;
			if(marker.getMap()==null){
				marker.setMap(map);
			}
			jQuery("li#"+thisIdSplit[1]+"_"+j).bind("click", showInfoandPan);
			jQuery("li#"+thisIdSplit[1]+"_"+j+" .placeControlButton").addClass("active");
		}
		*/
	} else if(thisIdSplit[0]=="place"){
		var category = nav[parseInt(thisIdSplit[1])].places;
		var marker = category[parseInt(thisIdSplit[2])].marker;
		if(marker.getMap() == null) {
			marker.setMap(map);
			jQuery(this).addClass("active");
			jQuery("li#"+thisIdSplit[1]+"_"+thisIdSplit[2]).bind("click", showInfoandPan)
		} else {
			marker.infowindow.close();
			marker.setMap(null);
			jQuery(this).removeClass("active"); 
			jQuery("li#"+thisIdSplit[1]+"_"+thisIdSplit[2]).unbind("click", showInfoandPan)
		}
	}
}

function homeMenu() {
	jQuery('#map_legend ul.categoryItemsList').css("left","100%");
	jQuery('#map_legend ul.categoryItemsList').css("display","none");
	jQuery('div.categoryWrapper div.controlButton, div.categoryWrapper div.category').css('display','block');
	jQuery('div.categoryWrapper div.controlButton, div.categoryWrapper div.category').removeClass("active");
	hideallMarkers();
	jQuery("#map_legend #toggleall").unbind("click", homeMenu);
	jQuery("#map_legend #toggleall").bind("click", showhideMarkers);
	jQuery("#map_legend #toggleall").text('Show all');
	jQuery("#map_legend h1,#map_legend p.sitedescription ").css('display','block');

}
function hideallMarkers() {
	for(var i=0; i < nav.length; i++){ //hide all markers
		var folder = nav[i].places;
		for(var j=0; j < folder.length; j++){
			var marker = folder[j].marker;
			marker.setMap(null);
			jQuery("li#"+i+"_"+j).unbind("click", showInfoandPan);
			jQuery("li#"+i+"_"+j+" input").removeAttr("checked");
		}
	}
}
function hideallInfoWindows() {
	for(var i=0; i < nav.length; i++){ //hide all markers
		var folder = nav[i].places;
		for(var j=0; j < folder.length; j++){
			var marker = folder[j].marker;
			marker.infowindow.close();
		}
	}
}

function amenities_resetMap() {
	map.setMapTypeId(map_type_id);
	map.setCenter(amenities_centerLatLng);
	map.setZoom(map_zoom);
}
function amenities_zoomIn() {
	var currentZoom = map.getZoom();
	(currentZoom < 18)?++currentZoom:currentZoom;
	map.setZoom(currentZoom);
}
function amenities_zoomOut() {
	var currentZoom = map.getZoom();
	(currentZoom > 9)?--currentZoom:currentZoom;
	map.setZoom(currentZoom);
}
