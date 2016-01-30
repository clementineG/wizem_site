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
  		$('.datepicker').pickadate({
		    selectMonths: true, // Creates a dropdown to control month
		    selectYears: 15 // Creates a dropdown of 15 years to control year
	  	});
	// =============================


/*

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

        var $dateicon = $('<i class="material-icons prefix">today</i>');

        // On ajoute le prototype modifié à la fin de la balise <div>
        $prototype.children('label').wrap('<div class="titre-date"></div>');
        $addDateLink.before($prototype);
        
        //$dateicon.before($prototype);
        //$dateicon.before($prototype.children('input'));
        $prototype.find('input').before($dateicon);
 
        dateindex++;

        if(dateindex > 1){
            addDeleteLink($prototype);
        }

        if(dateindex == 3){
            $addDateLink.toggleClass('hide');
        }
    }

    // La fonction qui ajoute un lien de suppression
    function addDeleteLink($prototype) {
        $deleteLink = $('<a href="#" class="btn">Supprimer</a>');
        $prototype.children('div.titre-date').prepend($deleteLink);
        $deleteLink.click(function(e) {
            $prototype.remove();
            e.preventDefault();
            dateindex--;
            if(dateindex == 2){
                $addDateLink.toggleClass('hide');
            }
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
      	var $prototype = $($placecontainer.attr('data-prototype').replace(/__name__label__/g, 'Date ' + (placeindex+1))
      		.replace(/__name__/g, placeindex)).addClass('block-date').addClass('col m4');

		// On ajoute le prototype modifié à la fin de la balise <div>
      	$prototype.children('label').wrap('<div class="titre-date"></div>');
      	$addPlaceLink.before($prototype);
 
      	placeindex++;

      	if(placeindex > 1){
    		addDeleteLink($prototype);
      	}

      	if(placeindex == 3){
	        $addPlaceLink.toggleClass('hide');
      	}
    }

    // La fonction qui ajoute un lien de suppression
    function addDeleteLink($prototype) {
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

*/




    var $typeevent = $('#wizem_eventbundle_event_typeevent');
    $typeevent.change(function() {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$typeevent.attr('name')] = $typeevent.val();
        // Submit data via AJAX to the form's action path.
        console.log(data);
        console.log($typeevent);
        console.log($typeevent.val());
        console.log($typeevent.attr('name'));
        console.log($form);
        console.log($form.attr('action'));
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            success: function(html) {
                // Replace current position field ...
                $('#formdateplace').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#formdateplace')
                );
            // Position field now displays the appropriate positions.
            }
        });
    });




});