bliss.controller("bliss.AppCtrl", ["$rootScope", "bliss.App", function($scope, App) {
	$scope.app = bliss.app 
		? angular.extend(bliss.app, {ready:true})
		: App.get({}, function(app) {
			app.ready = true;
		});
	
	$scope.loading = true;
	$scope.pageError = false;
	$scope.pageTitle = function() { return $scope.app.name; };
	$scope.clearPageError = function() { $scope.pageError = false; };
	
	$scope.$on("$locationChangeStart", function() {
		$scope.pageError = false;
		$scope.app.loading = true;
	});
	$scope.$on("$routeChangeSuccess", function() {
		$scope.app.loading = false;
	});
	$scope.$on("$routeChangeError", function() {
		$scope.app.loading = false;
		$scope.pageError = {
			message: "An error occurred while loading the page"
		};
	});
}]);