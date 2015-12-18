angular.module('myApp', [])
.run(function($rootScope, AuthService) { $rootScope.$on('$routeChangeStart',
function(evt, next, current) {
// If the user is NOT logged in
if (!AuthService.userLoggedIn()) {
if (next.templateUrl === "login.html") {
// Already heading to the login route so no need to redirect
} else {
$location.path('/login');
}
}
});
