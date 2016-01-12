var fs = require('fs');
var args = process.argv.slice(2);

var mode = "frontend";

var bundleName = args[0];
var bundleLowercase = bundleName.toLowerCase();
var bundleFirstUpper = bundleName.charAt(0).toUpperCase() + bundleName.toLowerCase().slice(1);
var controllers = 'application/controllers/'+mode+'/' + bundleFirstUpper + '.php' ;
var viewFolder = 'application/views/'+mode+'/' + bundleLowercase ;
var views = 'application/views/'+mode+'/' + bundleLowercase +'/'+ bundleLowercase + '_view.php';



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

/*
fs.unlink(controllers);
fs.unlink(views);
*/


/* Use sync*/
/*
fs.writeFileSync(controllers);
fs.mkdirSync(viewFolder);
fs.writeFileSync(views);
*/

fs.unlinkSync(views);
fs.unlinkSync(controllers);
fs.rmdirSync(viewFolder);
