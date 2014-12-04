/* Module: bliss-app */
/* E:\Development\Bliss/app\BlissApp/assets/js\app.js */
var blissApp = angular.module("blissApp", ["ngRoute", "docs"]);

blissApp.config(["$locationProvider", function($locationProvider) {
	$locationProvider.html5Mode(true);
}]);

/* E:\Development\Bliss/app\BlissApp/assets/js\app\routes.js */
blissApp.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/", {
		templateUrl: "./bliss-app/index/index.html"
	});
}]);

/* E:\Development\Bliss/app\BlissApp/assets/js\app\controllers\RootCtrl.js */
blissApp.controller("RootCtrl", ["$scope", "$location", function($scope, $location) {
	$scope.$watch(function() { return $location.path(); }, function(path) {
		$scope.path = path;
	});
}]);



/* Module: docs */
/* E:\Development\Bliss/bliss/development\Docs/assets/js\docs.js */
var docs = angular.module("docs", ["ngRoute", "ngResource"]);

/* E:\Development\Bliss/bliss/development\Docs/assets/js\docs\routes.js */
docs.config(["$routeProvider", function($routeProvider) {
	var _moduleResolve = ["$resource", "$route", function($resource, $route) {
		var id = $route.current.params.moduleId || "bliss";
		var action = $route.current.params.action;
		var path = "./docs/modules/"+ id;
		
		if (action) {
			path += "/"+ action;
		}
		
		var r = $resource(path +".json", {}, {
			get: {
				method: "GET",
				cache: true
			}
		});

		return r.get().$promise;
	}];

	var _moduleListResolve = ["$resource", function($resource) {
		var r = $resource("./docs/modules.json", {}, {
			query: {
				method: "GET",
				cache: true,
				isArray: true
			}
		});
		
		return r.query().$promise;
	}];
		
	$routeProvider.when("/docs", {
		templateUrl: "./docs/modules/bliss.html",
		controller: "module.IndexCtrl",
		resolve: {
			module: _moduleResolve,
			modules: _moduleListResolve
		}
	}).when("/docs/modules/:moduleId/:action?", {
		templateUrl: function(params) {
			var id = params.moduleId;
			var action = params.action || "index";
			
			return "./docs/modules/"+ id +"/"+ action +".html";
		},
		controller: "module.IndexCtrl",
		resolve: {
			module: _moduleResolve,
			modules: _moduleListResolve
		}
	});
}]);

/* E:\Development\Bliss/bliss/development\Docs/assets/js\docs\controllers\IndexCtrl.js */
docs.controller("IndexCtrl", ["$scope", "modules", function($scope, modules) {
	$scope.modules = modules;
}]);

/* E:\Development\Bliss/bliss/development\Docs/assets/js\docs\controllers\module\IndexCtrl.js */
docs.controller("module.IndexCtrl", ["$scope", "modules", "module", function($scope, modules, module) {
	$scope.modules = modules;
	$scope.module = module;
}]);



