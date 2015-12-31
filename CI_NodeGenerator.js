/*
node ci.js add frontend homepage
node ci.js add backend dashboard
node ci.js remove frontend homepage
node ci.js remove backend dashboard
*/

"use strict"
var fs = require('fs');


var args = process.argv.slice(2);

if(args[0] == 'add'){
	if(args.length == 3){
		var controllerDirectory = 'application/controllers/' + args[1] ;
		var viewDirectory = 'application/views/' + args[1] ;
		var bundleName = args[2];

		if (!fs.existsSync(controllerDirectory)){
			fs.mkdirSync(controllerDirectory);
		}

		if (!fs.existsSync(viewDirectory)){
			fs.mkdirSync(viewDirectory);
		}
	}
	else if(args.length == 2){
		var controllerDirectory = 'application/controllers';
		var viewDirectory = 'application/views';
		var bundleName = args[1];
	}
	else{
		console.log("ERROR ARGS !");return false;
	}


	var bundleLowercase = bundleName.toLowerCase();
	var bundleFirstUpper = bundleName.charAt(0).toUpperCase() + bundleName.toLowerCase().slice(1);

	var controllerFilename = controllerDirectory + '/' + bundleFirstUpper + '.php';
	var viewDirectoryBundle = viewDirectory + '/' + bundleLowercase;
	var viewFilename = viewDirectoryBundle + '/' + bundleLowercase +'_view.php';


	/* Create controller */
	fs.writeFile(controllerFilename, '', function (err) {
		console.log(controllerFilename + ' created !');
		return false;
	});

	/* create model */
	var modelFilename =  'application/models/' + bundleFirstUpper + '_model.php';
	fs.writeFile(modelFilename, '', function (err) {
		console.log(modelFilename + ' created !');
	});

	/* Create View */
	if (!fs.existsSync(viewDirectoryBundle)){
		fs.mkdirSync(viewDirectoryBundle);
	}
	else{
		console.log('Error : ' + viewDirectoryBundle + ' exist !');
		return false;
	}
	fs.writeFile(viewFilename, '', function (err) {
		console.log(viewFilename + ' created !');
		return false;
	});
}
else if(args[0] == 'remove'){
	if(args.length == 3){
		var bundleName = args[2];
		var bundleLowercase = bundleName.toLowerCase();
		var bundleFirstUpper = bundleName.charAt(0).toUpperCase() + bundleName.toLowerCase().slice(1);
		
		var controllerDirectory = 'application/controllers/' + args[1] ;
		var controllerFilename = controllerDirectory + '/' + bundleFirstUpper + '.php';
		if (fs.existsSync(controllerFilename)) {
			fs.unlinkSync(controllerFilename);
		}
		
		var viewDirectory = 'application/views/' + args[1] ;
		var viewDirectoryBundle = viewDirectory + '/' + bundleLowercase;
		var viewFilename = viewDirectoryBundle + '/' + bundleLowercase +'_view.php';
		fs.unlink(viewFilename);
		fs.rmdir(viewDirectoryBundle);

		var modelFilename =  'application/models/' + bundleFirstUpper + '_model.php';
		fs.unlink(modelFilename);
	}
	else if(args.length == 2){
		var controllerDirectory = 'application/controllers';
		var viewDirectory = 'application/views';
		var bundleName = args[1];

		var modelFilename =  'application/models/' + bundleFirstUpper + '_model.php';
		fs.unlink(modelFilename);
	}
	else{
		console.log("ERROR ARGS !");return false;
	}
}
else{
	console.log('ERROR');return false;
}
