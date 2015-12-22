var fs = require('fs');
var request = require('request');
var mysql = require('mysql');
var cheerio = require('cheerio');
var requestPromise = require('request-promise');
const delay = require('delay');


var knex = require('knex')({
	client: 'mysql',
	connection: {
		host     : 'localhost',
		user     : 'root',
		password : 'admin123',
		database : 'scraper'
	}
});


// var URL = "http://www.dp-db.com/general-health?&pageID=4&page=";
var cat_id = 10;
var URL = "http://www.dp-db.com/pets?page=";
var max_page = 21;

scraper(0);
function scraper(page){
	var data = [];
	var options = {
		uri: URL+page,
		transform: function (html) {
			return cheerio.load(html);
		}
	};
	requestPromise(options)
	.then(function ($) {
		var res;
		$('tr').each(function(i, element){
			var content = $(this);
			var product = content.find('.prod_name');
			if(product.find('a') != ''){
				data.push({
					url: content.find('.prod_name').find('a').attr('href'),
					title: content.find('.prod_name').find('a').text(),
					description: content.find('.description').text(),
					price: content.find('.price').text(),
					votes: content.find('.votes').text(),
					author: content.find('.author').text(),
					image: content.find('img.imagecache').attr('src'),
					cat_id: cat_id
				});


			}
		});
		res = {
			data:data,
			p:page
		}
		return res;
	})
	.then(function(res){
		knex('products').insert(data)
		.then(function(ret){
			console.log(res.p, ret);
		});
		return res;
	})
	.then(function(res){
		var nextPage = res.p + 1;
		if(nextPage <= max_page){
			// console.log('............');
			delay(1000)
			.then(() => {
				scraper(nextPage);
			});
		}
		else{
			console.log('Done !');
		}
	})
	.catch(function (err) {
		console.log(err);
	});

}



