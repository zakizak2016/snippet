/*
var fs = require('fs');
const delay = require('delay');
var request = require('request');
var cheerio = require('cheerio');
var requestPromise = require('request-promise');
var jsonfile = require('jsonfile')
*/

var mysql = require('mysql');
var forloop = require('forloop');
var Bing = require('node-bing-api')({ accKey: "TAUk8QyNNzcu8ruSuqRHNQgZlUbwhGUTInvbU3TcDnc" });
var knex = require('knex')({
	client: 'mysql',
	connection: {
		host     : 'localhost',
		user     : 'root',
		password : '',
		database : 'bing'
	}
});



var max = 50;
var offset = 0;

function doScrape(){
	
}

scrape();
function scrape(){
	var datas = [];
	Bing.images("car wallpaper", {
		top: max,
		skip: offset,
		imageFilters: {
			'size:width': '2560',
			'size:height': '1440',
    	}
	}, 
	function(error, res, body){
		forloop(0, body.d.results.length, 1,
			function(i) {
				var img = body.d.results[i];
				datas.push({
					Title: img.Title,
					MediaUrl: img.MediaUrl,
					SourceUrl: img.SourceUrl,
					DisplayUrl: img.DisplayUrl,
					Width: img.Width,
					Height: img.Height,
					FileSize: img.FileSize,
					ContentType: img.ContentType
				});
			},
			function() {
				knex('images').insert(datas)
				.then(function(ret){
					if(offset < 500){
						offset = offset + max;
						scrape();
						console.log(offset);
					}
					else{
						process.exit(0);
					}
				})
			}
		);
	});
}

 
