bliss.service("bliss.App", ["$resource", function($resource) {
	var App = $resource("./app.json");
	var _config;
	
	App.init = function() {
		if (bliss.app) {
			_config = angular.extend(bliss.app, {
				ready: true
			});
		} else {
			_config = App.get({}, function(response) {
				response.ready = true;
			}, function(error) {
				console.error(error.data);
			});
		}
	};
	
	App.config = function(config) {
		if (typeof(config) !== "undefined") {
			angular.extend(_config, config);
		}
		return _config;
	};
	
	return App;
}]);