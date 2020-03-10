(function ($) {
  	$(document).ready(function () {

		var y = $('#map').data('y'),
			x = $('#map').data('x');


			ymaps.ready(init);

			function init(){
				var myMap = new ymaps.Map("map", {
					center: [x, y],
					zoom: 7
				});
				myGeoObject = new ymaps.GeoObject({
					geometry: {
						type: "Point",
						coordinates: [x, y]
					},
				}, {
					preset: 'islands#blackStretchyIcon',
				});

				myMap.geoObjects.add(myGeoObject);
			}


  	});
})(jQuery);