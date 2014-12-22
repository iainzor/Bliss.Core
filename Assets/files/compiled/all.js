/* Module: bliss */
/* E:\Development\Bliss\bliss/core\Bliss/assets/js\app.js */
var bliss = angular.module("bliss", [
	"ngRoute",
	"ngResource"
]);

bliss.config(["$locationProvider", "$routeProvider", function($locationProvider, $routeProvider) {
	$locationProvider.html5Mode(true);
	
	$routeProvider.otherwise({
		templateUrl: "./bliss/welcome.html"
	});
}]);

/* E:\Development\Bliss\bliss/core\Bliss/assets/js\app\controllers\bliss.AppCtrl.js */
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

/* E:\Development\Bliss\bliss/core\Bliss/assets/js\app\services\bliss.App.js */
bliss.service("bliss.App", ["$resource", function($resource) {
	return $resource("./bliss/app.json");
}]);



/* Module: unifiedui */
/* E:\Development\Bliss\bliss/web\UnifiedUI/assets/js\app.js */


/* E:\Development\Bliss\bliss/web\UnifiedUI/assets/js\app\controllers\unifiedUI.LayoutCtrl.js */
bliss.controller("unifiedUI.LayoutCtrl", ["$scope", function($scope) {
	$scope.toggleMenu = function() {
		$scope.menuOpen = !$scope.menuOpen;
	};
	
	$scope.$on("$locationChangeStart", function() {
		$scope.menuOpen = false;
	});
}]);



/* Module: docs */
/* E:\Development\Bliss/bliss/development\Docs/assets/js\app.js */
bliss.config(["$routeProvider", function($routeProvider) {
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
		controller: "docs.module.IndexCtrl",
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
		controller: "docs.module.IndexCtrl",
		resolve: {
			module: _moduleResolve,
			modules: _moduleListResolve
		}
	});
}]);

/* E:\Development\Bliss/bliss/development\Docs/assets/js\app\controllers\IndexCtrl.js */
bliss.controller("docs.IndexCtrl", ["$scope", "modules", function($scope, modules) {
	$scope.modules = modules;
}]);

/* E:\Development\Bliss/bliss/development\Docs/assets/js\app\controllers\module\IndexCtrl.js */
bliss.controller("docs.module.IndexCtrl", ["$scope", "modules", "module", function($scope, modules, module) {
	$scope.modules = modules;
	$scope.module = module;
}]);



/* Module: tests */
/* E:\Development\Bliss/bliss/development\Tests/assets/js\app.js */
bliss.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/tests", {
		templateUrl: "./tests.html",
		controller: "tests.ResultCtrl",
		resolve: {
			result: ["tests.Result", function(Result) {
				return Result.get().$promise;
			}]
		}
	});
}]);

/* E:\Development\Bliss/bliss/development\Tests/assets/js\app\controllers\tests.ResultCtrl.js */
bliss.controller("tests.ResultCtrl", ["$scope", "$sce", "result", function($scope, $sce, result) {
	result.response = $sce.trustAsHtml(result.response);
	
	$scope.result = result;
}]);

/* E:\Development\Bliss/bliss/development\Tests/assets/js\app\services\tests.Result.js */
bliss.service("tests.Result", ["$resource", function($resource) {
	return $resource("./tests.json");
}]);



