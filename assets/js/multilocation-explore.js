window.wp = window.wp || {};

(function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;

    var api = wp.listifyResults || {};

    // add extra markers
    // $(document).on( 'listifyDataServiceLoaded', function() {
    //     // var canvas = api.controllers.mapService.getCanvas();
    //     var mainListings = api.controllers.dataService.response.listings,
    //         per_page     = FWP_JSON.preload_data.settings.pager.per_page,
    //         newMarkers = [];
    //     console.log('mainListing', mainListings);
    //     console.log('expPgAddiLoc', expPgAddiLoc);
    //     mainListings.forEach(function(ml){
    //         expPgAddiLoc.forEach(exl => {
    //             var newMl = $.extend(true, {}, ml);
    //             if( ( exl.id === ml.id ) && exl.location ){
    //                 newMl.location.lat = exl.location.lat;
    //                 newMl.location.lng = exl.location.lng;
    //                 newMl.location.address = exl.location.address || exl.location.name;
    //                 newMarkers.push(newMl);
    //             }
    //         });
    //     });

    //     console.log('newMarkers', newMarkers)
    //     api.controllers.mapService.addMarkers(newMarkers);
    // });

    // load the markers to map
    // not sure why 2 events doesn't work on Google Map and Mapbox so tripe one based on condition
    var multiLocationLoadMarkers = api.settings.mapService.service == 'mapbox' ? 'listifyDataServiceLoaded' : 'listifyMapServiceLoaded';
    $(document).on( multiLocationLoadMarkers, function() {
        var mainListings = api.controllers.dataService.response.listings,
        per_page     = FWP_JSON.preload_data.settings.pager.per_page,
        newMarkers = [];
        mainListings.forEach(function(ml){
            expPgAddiLoc.forEach(exl => {
                var newMl = $.extend(true, {}, ml);
                if( ( exl.id === ml.id ) && exl.location ){
                    newMl.location.lat = exl.location.lat;
                    newMl.location.lng = exl.location.lng;
                    newMl.location.address = exl.location.address || exl.location.name;
                    newMl.location.raw = exl.location.address || exl.location.name;
                    newMarkers.push(newMl);
                }
            });
        });
        api.controllers.mapService.addMarkers(newMarkers);
    });

})(window);

