$(document).ready(function(){

    // =============================
    // Imbricated form for date in new event
    var $datecontainer = $('div#wizem_eventbundle_event_date');

    var $addDateLink = $('<a href="#" id="add_date" class="">Ajouter une date</a>');
    $datecontainer.append($addDateLink).addClass('row');

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $addDateLink.click(function(e) {
        addDate($datecontainer);
        e.preventDefault();
        return false;
    });

    var dateindex = $datecontainer.find(':input').length;

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un
    if (dateindex == 0) {
        addDate($datecontainer);
    } 
    // La fonction qui ajoute un formulaire
    function addDate($datecontainer) {
        var $prototype = $($datecontainer.attr('data-prototype').replace(/__name__label__/g, 'Date ' + (dateindex+1))
            .replace(/__name__/g, dateindex)).addClass('block-date').addClass('col m4');

       // var $dateicon = $('<i class="material-icons prefix">today</i>');

        // On ajoute le prototype modifié à la fin de la balise <div>
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

    // La fonction qui ajoute un lien de suppression
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
    // =============================

	// =============================
	// Imbricated form for place in new event
    var $placecontainer = $('div#wizem_eventbundle_event_place');

    var $addPlaceLink = $('<a href="#" id="add_date" class="">Ajouter une adresse</a>');
    $placecontainer.append($addPlaceLink).addClass('row');

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $addPlaceLink.click(function(e) {
        addPlace($placecontainer);
        e.preventDefault();
        return false;
    });

    var placeindex = $placecontainer.find(':input').length;

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un
    if (placeindex == 0) {
      addPlace($placecontainer);
    } 
    // La fonction qui ajoute un formulaire
    function addPlace($placecontainer) {
      	var $prototype = $($placecontainer.attr('data-prototype').replace(/__name__label__/g, 'Adresse ' + (placeindex+1))
      		.replace(/__name__/g, placeindex)).addClass('block-date').addClass('col m4');

		// On ajoute le prototype modifié à la fin de la balise <div>
      	$prototype.children('label').wrap('<div class="titre-date"></div>');
      	$addPlaceLink.before($prototype);
 
      	placeindex++;

      	if(placeindex > 1){
    		addDeletePlaceLink($prototype);
      	}

      	if(placeindex == 3){
	        $addPlaceLink.toggleClass('hide');
      	}
    }

    // La fonction qui ajoute un lien de suppression
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
	// =============================

});


// angular.module( "ngAutocomplete", [])
//     .directive('ngAutocomplete', function($parse) {
//         return {
//             scope: {
//                 details: '=',
//                 ngAutocomplete: '=',
//                 options: '='
//             },

//         link: function(scope, element, attrs, model) {

//             //options for autocomplete
//             var opts

//             //convert options provided to opts
//             var initOpts = function() {
//                 opts = {}
//                     if (scope.options) {
//                         if (scope.options.types) {
//                             opts.types = []
//                             opts.types.push(scope.options.types)
//                         }
//                         if (scope.options.bounds) {
//                             opts.bounds = scope.options.bounds
//                         }
//                         if (scope.options.country) {
//                             opts.componentRestrictions = {
//                             country: scope.options.country
//                         }
//                     }
//                 }
//             }
//             initOpts()

//             //create new autocomplete
//             //reinitializes on every change of the options provided
//             var newAutocomplete = function() {
//                 scope.gPlace = new google.maps.places.Autocomplete(element[0], opts);
//                 google.maps.event.addListener(scope.gPlace, 'place_changed', function() {
//                     scope.$apply(function() {
//         //              if (scope.details) {
//                         scope.details = scope.gPlace.getPlace();
//         //              }
//                         scope.ngAutocomplete = element.val();
//                         });
//                 })
//             }
//             newAutocomplete()

//             //watch options provided to directive
//             scope.watchOptions = function () {
//                 return scope.options
//             };
//             scope.$watch(scope.watchOptions, function () {
//                 initOpts()
//                 newAutocomplete()
//                 element[0].value = '';
//                 scope.ngAutocomplete = element.val();
//             }, true);
//         }
//     };
// });

// angular.module( "GAutoCompl", ['ngAutocomplete'])
//     .controller("AutoComplCtrl",function ($scope) {
//         $scope.result = '';
//         $scope.options = {
//             country: 'fr',
//         };
//     });