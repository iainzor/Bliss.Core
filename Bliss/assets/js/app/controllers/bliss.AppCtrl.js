bliss.controller("bliss.AppCtrl", ["$scope", "$templateCache", "bliss.App", function($scope, $templateCache, App) {
	$scope.loading = true;
	$scope.pageError = false;
	$scope.app = App.get({}, function() { $scope.loading = false; });
	
	$scope.clearPageError = function() { $scope.pageError = false; }
	
	$scope.$on("$routeChangeStart", function() {
		$scope.pageError = false;
	});
	$scope.$on("$routeChangeError", function(e, route) {
		$scope.pageError = {
			message: "An error occurred while loading the page"
		};
	});
}]);