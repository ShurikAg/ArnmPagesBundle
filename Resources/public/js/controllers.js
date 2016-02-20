'use strict';

/* Controllers */

var pageControllers = angular.module('pageControllers', []);

pageControllers.controller('PageOverviewCtrl', [ '$scope', function($scope) {
	$scope.contentTmpl = '/bundles/arnmpages/tmpl/overview.html'
} ]);

pageControllers.controller('PageStatusCtrl', [ '$scope', '$http', function($scope, $http) {
	$http.get($scope.restBaseUrl).success(function(data) {
      $scope.page = data;
    });

	$scope.triggerStatus = function(){
		$http.put($scope.restBaseUrl + '/status').success(function(data) {
	      $scope.page = data;
	    });
	};
} ]);


pageControllers.controller('PageHeaderCtrl', [ '$scope', '$http', function($scope, $http) {
	$scope.contentTmpl = '/araneum/view/page/header.html';

	$scope.reset = function() {
		$http.get($scope.restBaseUrl).success(function(data) {
	      $scope.page = data;
	    });
    };

    $scope.update = function(page) {
    	//prepare the data for put request
    	var data = {
    			title: page.title,
    			description: page.description,
    			keywords: page.keywords
    	}
    	$http.put($scope.restBaseUrl+'/header', data).success(function(data) {
  	      $scope.page = data;
  	    });
    }

    $scope.reset();
} ]);


pageControllers.controller('PageLayoutCtrl', [ '$scope', '$http', '$log', function($scope, $http, $log) {
	$scope.contentTmpl = '/araneum/view/page/layout.html';
//	$scope.selectedLayout = null;

	$scope.reset = function() {
		$http.get($scope.restBaseUrl).success(function(data) {
	      $scope.page = data;
	      $scope.selectedLayout = $scope.page.layout.id;
	    });
    };

	$scope.update = function() {
		$log.debug("Selected Layout: ", $scope.selectedLayout);
    	//prepare the data for put request
    	var data = {
    			layout: $scope.selectedLayout
    	}
    	$http.put($scope.restBaseUrl+'/layout', data).success(function(data) {
  	      $scope.page = data;
  	      $scope.selectedLayout = $scope.page.layout.id;
  	    });
    };

    $scope.reset();
} ]);

/**
 * Template organizer controller
 */
pageControllers.controller('PageTemplateCtrl', [ '$scope', '$rootScope', '$http', '$uibModal', function($scope, $rootScope, $http, $uibModal) {
	$scope.contentTmpl = '/araneum/view/page/template.html';
	$scope.templateTmpl = '';
	$scope.widgetsList = [];
	$scope.pageWidgets = null;
	var originalWidgetsList = [];
	
//	$scope.selectedTemplate = null;

	$scope.init = function() {
		$http.get($scope.restBaseUrl).success(function(data) {
	      $scope.page = data;
	      $scope.widgetList
	      if($scope.page) {
	    	  $scope.loadTmpl($scope.page);
	      };
	    });
		$http.get('/araneum/rest/page/widgets').success(function(data) {
			originalWidgetsList = data;
		    $scope.initWidgetList();
		});
    };

    $scope.initWidgetList = function() {
    	$scope.widgetsList = originalWidgetsList.slice();
    };

	$scope.update = function(page) {
    	//prepare the data for put request
    	var data = {
    			template: page.template.id
    	}
    	$http.put($scope.restBaseUrl+'/template', data).success(function(data) {
  	      $scope.page = data;
  	      if($scope.page) {
	    	  $scope.loadTmpl($scope.page);
	      };
  	    });
    };

    $scope.loadTmpl = function(page) {
    	$scope.templateTmpl = '/araneum/view/page/templateOrganizer/'+page.id+'.html'+'#'+Math.random();
    	$http.get($scope.restBaseUrl+'/widgets'+'#'+Math.random()).success(function(data) {
  		  $scope.pageWidgets = data;
  	  	});
    };

    $scope.removePageWidget = function(widgetId, areaCode, index) {
    	if(!confirm('Are you sure?')) {
    		return;
    	}

    	$http.delete($scope.restBaseUrl+'/deleteWidget/'+widgetId)
			.success(function(data) {
				$scope.pageWidgets[areaCode].splice(index, 1);
	    	})
	    	.error(function(data, status, headers, config) {
	    		console.err("Failed to remove widget!");
		    });
    };

    $scope.configureWidget = function(widgetId) {
    	if ($rootScope.configModal == null) {
    		$rootScope.configModal = $uibModal.open({
	    	      animation: true,
	    	      templateUrl: 'widgetConfigModal.html',
	    	      controller: 'WidgetConfigCtrl',
	    	      size: 'lg',
	    	      backdrop: 'static',
	    	      resolve: {
	    	    	  widgetId: function () {
	    	    		  return widgetId;
	    	          }
    	          }
	    	});
    	}
    };

    /**
     * Widgets organization code
     */
    $scope.sortableOptions = {
	    connectWith: ".area",
	    update: function(e, ui) {
	    	var requestData = {
	    			'title': ui.item.data('widget-title'),
	    			'bundle': ui.item.data('widget-bundle'),
	    			'controller': ui.item.data('widget-controller'),
	    			'area': ui.item.sortable.droptarget.attr('id'),
	    			'index': ui.item.sortable.dropindex
	    	};

	    	var widgetId = ui.item.data('widget-id');
	    	//if the widget coming from source
	    	//we need to create an additional widget
	    	if(ui.item.sortable.received && $(ui.sender).hasClass('source')) {
	    		console.log("Adding widget");
	    		console.log("Data: ", requestData);
		    	$http.post($scope.restBaseUrl+'/addWidget', requestData)
		    		.success(function(data) {
		    			$scope.pageWidgets[data.areaCode][data.sequence].id = data.id;
			    	})
			    	.error(function(data, status, headers, config) {
			    		ui.item.sortable.cancel();
		    	    });
	    	} else if(angular.isNumber(widgetId) && widgetId > 0 && (ui.item.sortable.received || (!ui.item.sortable.received && !ui.sender && ui.item.sortable.index != ui.item.sortable.dropindex))) {
	    		//this one is probably moving existing widget from one place to another
	    		console.log("Sorting widget");
	    		$http.put($scope.restBaseUrl+'/sortWidget/'+widgetId, requestData)
		    		.success(function(data) {
			    		console.log("Response from sort:", data);
			    	})
			    	.error(function(data, status, headers, config) {
			    		ui.item.sortable.cancel();
		    	    });
	    	} else {
//	    		ui.item.sortable.cancel();
	    	}
	    },
	    stop: function(e, ui) {
	    	if($(e.target).hasClass('source')) {
	    		console.log("Source widgets restored");
	    		$scope.initWidgetList();
	    	}
	    }
	};

    $scope.init();
} ]);

/**
 * Controller that is responsible for widget configuration modal
 */
pageControllers.controller('WidgetConfigCtrl', [ '$scope', '$rootScope', '$http', '$uibModalInstance', 'widgetId', function($scope, $rootScope, $http, $uibModalInstance, widgetId) {
	$scope.widgetId = widgetId;

	$scope.widgetConfigFormTmpl = '';
	$scope.widgetConfigSubmitTarget = null;
	$scope.dataSource = null;
	$scope.widgetConfigFormTitle = 'Loading...';
	$scope.wConfigForm = {};

	$scope.init = function() {
		$http.get('/araneum/view/page/templateOrganizer/widget/'+$scope.widgetId+'/form.html').success(function(data) {
			$scope.widgetConfigFormTmpl = data.formTmpl;
			$scope.widgetConfigSubmitTarget = data.formSubmitTarget;
			$scope.dataSource = data.dataSource;
			$scope.widgetConfigFormTitle = data.formTitle;
			$http.get($scope.dataSource).success(function(data) {
				$scope.wConfigForm = data;
			});
	  	});
	};

	$scope.configSave = function () {
    	console.log("Form data: ", $scope.wConfigForm);
    	$http.put($scope.widgetConfigSubmitTarget, $scope.wConfigForm).success(function(data) {
    		$scope.resetwidgetConfigForm();
        	$rootScope.configModal.close();
    		$rootScope.configModal.dismiss();
    		$rootScope.configModal = null;
    	});
    };

	$scope.configCancel = function () {
		$scope.resetwidgetConfigForm();
    	$rootScope.configModal.close();
		$rootScope.configModal.dismiss();
		$rootScope.configModal = null;
    };

	$scope.resetwidgetConfigForm = function(){
    	$scope.wConfigForm = {};
    	$scope.widgetConfigFormTmpl = '';
    	$scope.widgetConfigSubmitTarget = null;
    	$scope.dataSource = null;
    	$scope.widgetConfigFormTitle = 'Loading...';
    };

    $scope.tinymceOptions = {
    		theme_url: '/bower_components/tinymce/themes/modern/theme.min.js',
    		skin_url: '/bower_components/tinymce/skins/lightgray',
    		document_base_url: "/bower_components/tinymce/",
    		height : 300,
    		external_plugins: {
    	        "advlist": "/bower_components/tinymce/plugins/advlist/plugin.min.js",
    	        "autolink": "/bower_components/tinymce/plugins/autolink/plugin.min.js",
    	        "link": "/bower_components/tinymce/plugins/link/plugin.min.js",
    	        "image": "/bower_components/tinymce/plugins/image/plugin.min.js",
    	        "lists": "/bower_components/tinymce/plugins/lists/plugin.min.js",
    	        "charmap": "/bower_components/tinymce/plugins/charmap/plugin.min.js",
    	        "print": "/bower_components/tinymce/plugins/print/plugin.min.js",
    	        "hr": "/bower_components/tinymce/plugins/hr/plugin.min.js",
    	        "anchor": "/bower_components/tinymce/plugins/anchor/plugin.min.js",
    	        "pagebreak": "/bower_components/tinymce/plugins/pagebreak/plugin.min.js",
    	        "spellchecker": "/bower_components/tinymce/plugins/spellchecker/plugin.min.js",
    	        "searchreplace": "/bower_components/tinymce/plugins/searchreplace/plugin.min.js",
    	        "wordcount": "/bower_components/tinymce/plugins/wordcount/plugin.min.js",
    	        "visualblocks": "/bower_components/tinymce/plugins/visualblocks/plugin.min.js",
    	        "visualchars": "/bower_components/tinymce/plugins/visualchars/plugin.min.js",
    	        "code": "/bower_components/tinymce/plugins/code/plugin.min.js",
    	        "fullscreen": "/bower_components/tinymce/plugins/fullscreen/plugin.min.js",
    	        "insertdatetime": "/bower_components/tinymce/plugins/insertdatetime/plugin.min.js",
    	        "media": "/bower_components/tinymce/plugins/media/plugin.min.js",
    	        "nonbreaking": "/bower_components/tinymce/plugins/nonbreaking/plugin.min.js",
    	        "save": "/bower_components/tinymce/plugins/save/plugin.min.js",
    	        "table": "/bower_components/tinymce/plugins/table/plugin.min.js",
    	        "contextmenu": "/bower_components/tinymce/plugins/contextmenu/plugin.min.js",
    	        "directionality": "/bower_components/tinymce/plugins/directionality/plugin.min.js",
    	        "emoticons": "/bower_components/tinymce/plugins/emoticons/plugin.min.js",
    	        "template": "/bower_components/tinymce/plugins/template/plugin.min.js",
    	        "paste": "/bower_components/tinymce/plugins/paste/plugin.min.js",
    	        "textcolor": "/bower_components/tinymce/plugins/textcolor/plugin.min.js",
    	    },
    		plugins: [
    		          "advlist autolink link image lists charmap print print hr anchor pagebreak spellchecker",
    		          "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    		          "save table contextmenu directionality emoticons template paste textcolor"
    		    ]
    };

    $scope.init();
}]);