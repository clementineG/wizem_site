$(document).ready(function(){


	// =============================
	// Material responsive navbar
	$(".button-collapse").sideNav(); // Initialize collapse button
	$('.collapsible').collapsible(); // Initialize collapsible (uncomment the line if you use the dropdown variation)
	// =============================

	// =============================
	// Material parallax
	$('.parallax').parallax();
	// =============================


	// =============================
	// Material datepicker
	$(function() {
        $('.datepicker').pickadate({
            monthsFull: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Jan', 'Fév', 'Mar', 'Avril', 'Mai', 'Juim', 'Jui', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
            weekdaysFull: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            weekdaysLetter: [ 'D', 'L', 'M', 'M', 'J', 'V', 'S' ],
            today: 'Aujourd\'hui',
            clear: 'Effacer',
            close: 'Fermer',
            labelMonthNext: 'Mois suivent',
            labelMonthPrev: 'Mois prévédent',
            labelMonthSelect: 'Selectionnez un mois',
            labelYearSelect: 'Selectionnez une année',
            firstDay: 1,
            min: new Date(),
    	    selectMonths: true, 
            selectYears: 2, 
    	    format: 'dd/mm/yyyy',
            closeOnSelect: true,
      	});
    });
	// =============================


    // =============================
    // Google maps initialisation for show view event
    if(document.getElementById('map')){

        var map;
        var markers = [];
        var baseCenter = {
            lat: -34.397,
            lng: 150.644
        };

        var options = {
            zoom: 11,
            center: {
                lat: baseCenter.lat,
                lng: baseCenter.lng
            },
            options: {
                zoomControl: true,
                streetViewControl: false,
                mapTypeControl: false,
                scrollwheel: true
            },
            styles: [{"featureType": "water","elementType": "geometry","stylers": [{ "color": "#43B3FE" }]},
            {"featureType": "transit.line","stylers": [{ "visibility": "off" }]},
            {"featureType": "road","elementType": "geometry","stylers": [{ "color": "#FEFEFE" }]},
            {"featureType": "road.arterial","elementType": "labels.icon","stylers": [{ "visibility": "off" }]},
            {"featureType": "road.local","elementType": "labels.icon","stylers": [{ "visibility": "off" }]},
            {"featureType": "road.highway","elementType": "labels.icon","stylers": [{ "visibility": "off" }]}]
        };

        function initMap() {

            var mapId = $('#map');
            options.center.lat = mapId.attr("data-lat") ? parseFloat(mapId.attr("data-lat")) : baseCenter.lat;
            options.center.lng = mapId.attr("data-lng") ? parseFloat(mapId.attr("data-lng")) : baseCenter.lng;

            // TODO : plusieurs adresses

            map = new google.maps.Map(document.getElementById('map'), options);

            var addresses = document.getElementsByClassName("address");
            
            console.log(addresses);   
            $(".address").each(function(address){
               // console.log($(this).attr("data-lat"));
                createMarker($(this));
            });
        }


        /**
        * Création du marker en fonction de son association (positionnement, couleur, etc.)
        */
        function createMarker(address){

            // var soin = association.typesoinNom;
            // var couleur = association.typesoinCouleur;

            // var icone = "{{ asset('bundles/lchtemplate/img/marker-couleur.png')}}";
            // var iconeMarker = icone.replace('couleur', couleur);

            var myMarker = new google.maps.Marker({
                position: new google.maps.LatLng(address.attr("data-lat"), address.attr("data-lng")),
                map: map,
                title: address.attr("data-address"),
                //icon:iconeMarker
            });

          
            myMarker.content = ''+
                    '<div class="">' +
                    '</div>';


            google.maps.event.addListener(myMarker, 'click', function(){
                
            });
            // Tout le js concernant le marker est à mettre ici :
            google.maps.event.addListener(myMarker, 'domready', function() {
               
            });
            
            markers.push({'marker' : myMarker});
        }



        initMap();
    }
    // =============================




    // =============================
    // Imbricated form for place in new event
    if(document.getElementById('wizem_eventbundle_event_place')){
        // Add place field in form
        function addPlace($placecontainer) {
            var $prototype = $($placecontainer.attr('data-prototype').replace(/__name__label__/g, 'Adresse ' + (placeindex+1))
                .replace(/__name__/g, placeindex)).addClass('block-date').addClass('col m4');

            // Add modified prototype after <div> balise
            $prototype.children('label').wrap('<div class="titre-date"></div>');
            
            $addPlaceLink.before($prototype);
     
            placeindex++;

            if(placeindex > 1){
                addDeletePlaceLink($prototype);
            }

            if(placeindex == 3){
                $addPlaceLink.toggleClass('hide');
            }

            // Google autocomplete on created input
            var input = document.getElementById($prototype.find('input').attr("id"));
            var autocomplete = new google.maps.places.Autocomplete(input);
        }

        var $placecontainer = $('div#wizem_eventbundle_event_place');

        var $addPlaceLink = $('<a href="#" id="add_date" class="">Ajouter une adresse</a>');
        $placecontainer.append($addPlaceLink).addClass('row');

        // Add new field on click in link
        $addPlaceLink.click(function(e) {
            addPlace($placecontainer);
            e.preventDefault();
            return false;
        });

        var placeindex = $placecontainer.find(':input').length;

        // Add first place field
        if (placeindex == 0) {
          addPlace($placecontainer);
        } 

        // Add delete place link 
        function addDeletePlaceLink($prototype) {
            $deleteLink = $('<a href="#" class="btn">Supprimer</a>');
            $prototype.children('div.titre-date').prepend($deleteLink);
            $deleteLink.click(function(e) {
                $prototype.remove();
                e.preventDefault();
                placeindex--;
                if(placeindex == 2){
                    $addPlaceLink.toggleClass('hide');
                }
                return false;
            });
        }
    }
    // =============================


    // =============================
    // Imbricated form for date in new event
    if(document.getElementById('wizem_eventbundle_event_date')){

        function addDate($datecontainer) {
            var $prototype = $($datecontainer.attr('data-prototype').replace(/__name__label__/g, 'Date ' + (dateindex+1))
                .replace(/__name__/g, dateindex)).addClass('block-date').addClass('col m4');

           // var $dateicon = $('<i class="material-icons prefix">today</i>');
            $prototype.children('label').wrap('<div class="titre-date"></div>');
            $addDateLink.before($prototype);
            
           // $prototype.find('input').before($dateicon);
     
            dateindex++;

            if(dateindex > 1){
                addDeleteDateLink($prototype);
            }

            if(dateindex == 3){
                $addDateLink.toggleClass('hide');
            }
        }
        var $datecontainer = $('div#wizem_eventbundle_event_date');

        var $addDateLink = $('<a href="#" id="add_date" class="">Ajouter une date</a>');
        $datecontainer.append($addDateLink).addClass('row');

        $addDateLink.click(function(e) {
            addDate($datecontainer);
            e.preventDefault();
            return false;
        });
        var dateindex = $datecontainer.find(':input').length;

        if (dateindex == 0) {
            addDate($datecontainer);
        } 

        function addDeleteDateLink($prototype) {
            $deleteLink = $('<a href="#" class="btn">Supprimer</a>');
            $prototype.children('div.titre-date').prepend($deleteLink);
            $deleteLink.click(function(e) {
                $prototype.remove();
                dateindex--;
                if(dateindex == 2){
                    $addDateLink.toggleClass('hide');
                }
                e.preventDefault();
                return false;
            });
        }
    }
    // =============================




});