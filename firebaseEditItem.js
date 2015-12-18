var refProf = new Firebase(CONFIG.FURL+'/user/'+uid+'/profile');
		var profObj = $firebaseObject(refProf);

		refProf.child('email').set('email');
		refProf.child('fullname').set('fullname');
