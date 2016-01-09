var fs = require('fs');
var args = process.argv.slice(2);



var bundleName = args[0];
var bundleLowercase = bundleName.toLowerCase();
var bundleFirstUpper = bundleName.charAt(0).toUpperCase() + bundleName.toLowerCase().slice(1);
var controllers = 'application/controllers/frontend/' + bundleFirstUpper + '.php' ;
var views = 'application/views/frontend/' + bundleLowercase + '.php';


/*
fs.writeFile(controllers, '', function (err) {
	console.log(controllers + ' created !');
	return false;
});
fs.writeFile(views, '', function (err) {
	console.log(views + ' created !');
	return false;
});
*/

fs.unlink(controllers);
fs.unlink(views);
