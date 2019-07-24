jQuery(function($){
	
	var i = 0;

	if( $.fn.mapify ) {

		// initiate wp manager extended location default map after loading facetwp to get rid off conflict 
		$(document).on('facetwp-loaded', function() {
			$( wpjmel.input ).mapify( wpjmel );
		});

		// load all extra locations on edit job edit page 
		$.each(additionallocations, function(k, v){
			if(v) {
				wpjmelf = {};
				$('.fieldset-job_location').append('<div class="field"><input type="text" value="'+additionallocations[k]['name']+'" name="additionallocation['+i+'][name]" id="job_location'+ i +'"><p class="remove_location">Remove Location</p></div>');
				wpjmelf.input = '#job_location' + i;
				wpjmelf.lat_input = 'additionallocation['+i+'][geo_lat]';
				wpjmelf.lng_input = 'additionallocation['+i+'][geo_lng]';
				wpjmelf.lat = additionallocations[k]['geo_lat'];
				wpjmelf.lng = additionallocations[k]['geo_lng'];
				$( '#job_location' + i ).mapify(wpjmelf);
				i++;
			}
		});
	}

	// add new locations 
	$('.fieldset-job_location').after('<p class="addLocation"> Add another location</p>');
	$('body').on('click', '.addLocation', function(){
		$('.fieldset-job_location').append('<div class="field"><input type="text" class="input-text" name="additionallocation['+i+'][name]" id="job_location'+ i +'"><p class="remove_location">Remove Location</p></div>');
		wpjmel.input = '#job_location' + i;
		wpjmel.lat_input = 'additionallocation['+i+'][geo_lat]';
		wpjmel.lng_input = 'additionallocation['+i+'][geo_lng]';
		$( '#job_location' + i ).mapify(wpjmel);
		i++;
	});

	// remove a location 
	$('body').on('click', '.remove_location', function(){
		$(this).parent().remove();
	})
});