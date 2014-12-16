bliss.service("bliss.App", ["$resource", function($resource) {
	return $resource("./bliss/app.json");
}]);