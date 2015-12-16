angular.module('starter.controllers', [])

.controller('AppCtrl', function($scope, $ionicModal, $timeout) {
    $scope.loginData = {};

    $ionicModal.fromTemplateUrl('templates/login.html', {
        scope: $scope
    }).then(function(modal) {
        $scope.modal = modal;
    });

    $scope.closeLogin = function() {
        $scope.modal.hide();
    };

    $scope.login = function() {
        $scope.modal.show();
    };

    $scope.doLogin = function() {
        console.log('Doing login', $scope.loginData);

        $timeout(function() {
            $scope.closeLogin();
        }, 1000);
    };
})

.controller('PlaylistsCtrl', function($scope) {
    $scope.playlists = [
    { title: 'Reggae', id: 1 },
    { title: 'Chill', id: 2 },
    { title: 'Dubstep', id: 3 },
    { title: 'Indie', id: 4 },
    { title: 'Rap', id: 5 },
    { title: 'Cowbell', id: 6 }
    ];
})

.controller('PlaylistCtrl', function($scope, $stateParams, $cordovaSQLite) {



    $scope.datas = {};
    $scope.insert = function() {
        var firstname = $scope.datas.firstname;
        var lastname = $scope.datas.lastname;

        var query = "INSERT INTO people (firstname, lastname) VALUES (?,?)";
        $cordovaSQLite.execute(db, query, [firstname, lastname]).then(function(res) {
            $scope.load();
            console.log("INSERT ID -> " + res.insertId);
        }, function (err) {
            console.error(err);
        });
    }



    $scope.results=[]; 

    $scope.load = function() {
        $scope.results2 =[]; 
        var id = $scope.datas.id;
        var query = "SELECT id, firstname, lastname FROM people";
        $cordovaSQLite.execute(db, query).then(function(res) {
            var len = res.rows.length;
            for (var i = 0; i < len; i++) {
                $scope.results2.push({
                    id: res.rows.item(i).id,
                    firstname: res.rows.item(i).firstname,
                    lastname: res.rows.item(i).lastname
                });
            }

            setTimeout(function(){
                $scope.results = [];
                $scope.results = angular.copy($scope.results2);
                $scope.$apply();
            }, 100);
            
        }, function (err) {
            console.error(err);
        });
    }
    $scope.load();

    $scope.deletebyid = function(id){
         var query = "DELETE FROM people  WHERE id = "+id+";";
          $cordovaSQLite.execute(db, query)
          .then(function(res){
             $scope.load();
          });
    }

    $scope.select = function() {
        var id = $scope.datas.id;
        var query = "SELECT firstname, lastname FROM people";
        $cordovaSQLite.execute(db, query).then(function(res) {

            var len = res.rows.length;
            for (var i = 0; i < len; i++) {
                $scope.results.push({
                    firstname: res.rows.item(i).firstname,
                    lastname: res.rows.item(i).lastname
                });
            }

            if(res.rows.length > 0) {
                setTimeout(function(){
                    $scope.datas.result =  res.rows;
                    $scope.$apply();
                }, 1000);

                console.log("SELECTED -> " + res.rows.item(0).firstname + " " + res.rows.item(0).lastname);
            } else {
                console.log("No results found");
            }
        }, function (err) {
            console.error(err);
        });
    }


    $scope.delete = function(){
        $cordovaSQLite.deleteDB("todos");
    }
});
