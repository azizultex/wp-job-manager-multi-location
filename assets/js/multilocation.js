( function() {
		/**
		 * Constructor.
		 */
		function MultiLocationMap() {
			console.log('mapSettings', mapSettings)
			// Get vars needed.
			this.options = mapSettings;
			this.additionallocations = additionallocations;
			this.canvas  = 'multi-location-listing-contact-map'; // #listing-contact-map .

			if ( ! document.getElementById( this.canvas ) ) {
				return;
			}

			// Setup map based on selected map provider.
			if ( 'googlemaps' === this.options.provider ) {
				return this.setupGoogleMaps();
			} else if ( 'mapbox' === this.options.provider ) {
				return this.setupMapBox();
			}
		}

		/**
		 * Google Maps Setup.
		 */
		MultiLocationMap.prototype.setupGoogleMaps = function() {
			var self = this;

			// Var:
			this.latlng = new google.maps.LatLng( this.options.lat, this.options.lng );

			// Set Map:
			this.map = new google.maps.Map( document.getElementById( this.canvas ), {
				zoom: parseInt( this.options.mapOptions.zoom ),
				center: this.latlng,
				scrollwheel: false,
				styles: this.options.mapOptions.styles,
				streetViewControl: false,
			} );

			// Remove other businesses.
			this.map.setOptions( { styles: [
				{
					featureType: "poi",
					stylers: [
						{
							visibility: "off",
						}
					],
				}
			] } );

			// Set Marker (using RichMarker Library):
			this.marker = new RichMarker( {
				position: this.latlng,
				flat: true,
				draggable: false,
				content: '<div class="map-marker marker-color-' + this.options.term + ' type-' + this.options.term + '"><i class="' + this.options.icon + '"></i></div>'
			} );
			this.marker.setMap( this.map );
		};

		/**
		 * MapBox Setup (Using Leaflet Library)
		 */
		MultiLocationMap.prototype.setupMapBox = function() {
			L.mapbox.accessToken = this.options.mapOptions.mapboxTileUrl;

			// Load Map:
			this.map = L.mapbox.map( this.canvas, 'mapbox.streets' ).setView( [
				this.options.lat,
				this.options.lng
			], parseInt( this.options.mapOptions.zoom ) );

			// Marker data:
			this.markerTemplate = wp.template( 'pinTemplate' ); // Loaded in footer.
			this.markerTemplateData = {
				mapMarker: {
					term: this.options.term,
					icon: this.options.icon,
				},
				status: {
					featured: false,
				}
			}
			this.markerIcon = L.divIcon( {
				iconSize: [30, 45],
				iconAnchor: [15, 45],
				className: '',
				html: this.markerTemplate( this.markerTemplateData ),
			} );

			// Add marker to map:
			this.marker = L.marker( [ this.options.lat, this.options.lng ], { icon: this.markerIcon } ).addTo( this.map );
		};

	// // Define Map.
	var MultiLocationMapInit = function() {
		return new MultiLocationMap();
	};

	// Load Map.
    MultiLocationMapInit();

}).call(this);