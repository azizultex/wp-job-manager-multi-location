window.wp = window.wp || {};

(function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;

    var api = wp.listifyResults || {};

    // add extra markers
    $(document).on( 'listifyDataServiceLoaded', function() {
        // var canvas = api.controllers.mapService.getCanvas();
        var mainListings = api.controllers.dataService.response.listings,
            per_page     = FWP_JSON.preload_data.settings.pager.per_page,
            newMarkers = [];
        console.log('mainListing', mainListings);
        console.log('expPgAddiLoc', expPgAddiLoc);
        mainListings.forEach(function(ml){
            expPgAddiLoc.forEach(exl => {
                if( ( exl["id"] === ml["id"] ) && exl["location"]){
                    ml["location"]["lat"] = exl["location"]["lat"];
                    ml["location"]["lng"] = exl["location"]["lng"];
                    ml["location"]["address"] = exl["location"]["address"];
                   newMarkers.push(ml);
                }
            });
        });

        console.log('newMarkers', newMarkers)
        // for(var i=0; i<per_page; i++){
        //     if((expPgAddiLoc[i]["id"] === mainListings[i]["id"]) && expPgAddiLoc[i]["location"] ){
        //         console.log($.extend(mainListings[i],expPgAddiLoc[i] ));
        //     }
        // }
        api.controllers.mapService.addMarkers(newMarkers);
    });

    // $(document).on( 'listifyMapServiceLoaded', function() {
    //     var mainListings = api.controllers.dataService.response.listings,
    //     per_page     = FWP_JSON.preload_data.settings.pager.per_page,
    //     newMarkers = [];
    //     // console.log('mainListing', mainListings);
    //     // console.log('expPgAddiLoc', expPgAddiLoc);
    //     mainListings.forEach(function(ml){
    //         expPgAddiLoc.forEach(exl => {
    //             if( ( exl["id"] === ml["id"] ) && exl["location"]){
    //                 ml["location"]["lat"] = exl["location"]["lat"];
    //                 ml["location"]["lng"] = exl["location"]["lng"];
    //                 ml["location"]["address"] = exl["location"]["address"];
    //             newMarkers.push(ml);
    //             }
    //         });
    //     });

    //     api.controllers.mapService.addMarkers(newMarkers);
    // });

})(window);

