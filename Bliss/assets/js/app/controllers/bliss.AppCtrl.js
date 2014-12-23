bliss.controller("bliss.AppCtrl", ["$scope", "$location", "bliss.App", function($scope, $location, App) {
	$scope.loading = true;
	$scope.pageError = false;
	$scope.app = App.get({}, function() { $scope.loading = false; });
	
	$scope.clearPageError = function() { $scope.pageError = false; };
	
	$scope.$on("$routeChangeStart", function() {
		$scope.pageError = false;
	});
	$scope.$on("$routeChangeError", function(e, route) {
		$scope.pageError = {
			message: "An error occurred while loading the page"
		};
	});
	$scope.$on("$locationChangeSuccess", function() {
		if ($scope.app.pages) {
			var people = $scope.app.pages[2];
			var person = people.pages[1];
			var r = new RegExp(person.match);
			
			if (r.test($location.path())) {
				console.log("Yes!");
			}
		}
	}, true);
}]);