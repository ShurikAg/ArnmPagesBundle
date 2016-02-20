'use strict';

/* App Module */

var pageApp = angular.module('pageApp', [ 'ngRoute', 'pageControllers', 'ui.sortable', 'ui.tinymce', 'ui.bootstrap', 'ui.bootstrap.modal']);

pageApp.config([ '$routeProvider', function($routeProvider) {
	$routeProvider.
		when('/overview', {
			templateUrl : '/araneum/view/page/manage-page.html',
			controller : 'PageOverviewCtrl'
		}).
		when('/header', {
			templateUrl : '/araneum/view/page/manage-page.html',
			controller : 'PageHeaderCtrl'
		}).
		when('/layout', {
			templateUrl : '/araneum/view/page/manage-page.html',
			controller : 'PageLayoutCtrl'
		}).
		when('/template', {
			templateUrl : '/araneum/view/page/manage-page.html',
			controller : 'PageTemplateCtrl'
		}).
		otherwise({
			redirectTo : '/overview'
		});

	tinyMCE.baseURL = '/bower_components/tinymce';
    tinyMCE.suffix = '.min';
} ]);