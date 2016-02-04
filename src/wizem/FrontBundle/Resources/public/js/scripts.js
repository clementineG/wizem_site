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



});