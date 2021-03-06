function NewsCtrl($anchorScroll, $stateParams, $state, ApiService, MetadataService) {
    var vm = this;
    var apiCallFunction;

    vm.posts = [];
    vm.loaded = false;
         vm.totalItems = 1000;
    vm.subtitle = '';

    MetadataService.setMetadata({
        title: 'News',
        description: 'Latest news from THS'
    });

    if (typeof $stateParams.tags !== 'undefined') {
        apiCallFunction = ApiService.allPostsByTag($stateParams.tag);
        vm.subtitle = 'tagged with "' + $stateParams.tag + '"';
    } else if (typeof $stateParams.searchTerm !== 'undefined') {
        console.log("searchBlog");
        apiCallFunction = ApiService.allPostsBySearchTerm($stateParams.searchTerm);
        vm.subtitle = 'searching "' + $stateParams.searchTerm + '"';
    } else {
        apiCallFunction = ApiService.allPosts();
    }



 vm.pageChanged = function(newPage) {
     var valtosend = vm.searchCategory;
     if (valtosend === undefined ){
        ApiService.allPostsBySearchCategory(newPage).then(function(posts) {
		vm.posts = posts;
	});
     } else{  
         ApiService.postsBySearchCategory(newPage, valtosend).then(function(posts) {
		    vm.posts = posts;
	});
     }

    };



vm.filterCategory = function(searchResult) { 
  var valtosend = vm.searchCategory;
  var newPage = 1;
       vm.totalItems = 20;
         apiCallFunction = ApiService.postsBySearchCategory(newPage, valtosend);
         apiCallFunction.then(function(results) {
          vm.posts = results;
      });
}



    vm.search = function(term) {
       apiCallFunction = ApiService.allPostsBySearchTerm(term);
        
       apiCallFunction.then(function(posts) {
            vm.posts = posts;
            vm.searchTrue = true;
        });
    };

    vm.clearSearch = function() {
        ApiService.allPosts().then(function(posts) {
            vm.posts = posts;
            vm.searchTrue = false;
        });
    };


    apiCallFunction.then(function(posts) {
        vm.posts = posts;
        vm.loaded = true;
    });

    vm.scrollToTop = function() {
        $anchorScroll();
    };

    vm.search = function(term) {
        $state.go('root.newsBySearch', { searchTerm: term });
    };
}

function DocumentCtrl($stateParams, $anchorScroll, $timeout, $location, ApiService, MetadataService, SearchService) {
    var vm = this;
    var apiCallFunction;
    vm.searchResults = false;
    vm.post = {};

    ApiService.postByURL('/documents').then(function(page) {
        vm.page = page;
        
        MetadataService.setMetadata({
            title: vm.page.title,
            description: page.excerpt
        });
    });

 if (typeof $stateParams.title !== 'undefined') {
    ApiService.documentBySlug($stateParams.title).then(function(post) {
        vm.post = post[0];

         MetadataService.setMetadata({
            title: vm.post.title,
            description: post.excerpt
        });
         
    });
 } else{
     apiCallFunction = ApiService.allDocuments();
        apiCallFunction.then(function(posts) {
        vm.posts = posts;
        vm.loaded = true;
    });
 }

vm.search = function(searchText) {
  apiCallFunction = SearchService.allSearchCat(searchText, 'documents');
        apiCallFunction.then(function(results) {
          vm.searchResults = results;
        });
}

    vm.tab = 1;

    vm.setTab = function(newTab){
      vm.tab = newTab;
    };

    vm.isSet = function(tabNum){
      return vm.tab === tabNum;
    };

    
}



angular
    .module('app')
    .controller('NewsCtrl', NewsCtrl)
    .controller('DocumentCtrl', DocumentCtrl);