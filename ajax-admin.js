//And immediately invoked function expression.  
(function($) {
	
	$(document).ready(function() {
		
		//Attaching an on submit event handler with a callback 
		//to send the client request to the server. 
		$('.ajax-form').on( 'submit', function(event) {

			//Prevent default form submission preventing a page reload.
			event.preventDefault();
			
			//Add loading message or spinner graphic
			$('.ajax-response').html('Loading...');
			
			//Extracting search term from search field.
			var query = $('#searchTerm').val();

			//Inserting the query into the url string.
			var url = 'http://news.google.com/news?q=' + query + '&output=rss';

			//Debugging URL string to make sure query was inserted that the url is correctly formed.
			console.log("The url is: " + url);

			//Submit the data to the server. 
			//The first parameter, 'ajaxurl', for ajax in the admin area, this variable is defined automatically,
			//by WordPress, and it automatically adds this variable to all pages in the admin area. 
			//The second parameter is an object literal that contains the data we want to send with the 
			//post request to the server to the php page that processes this data. Included is the 
			//nonce value, the action hook, and the submitted url. The third parameter is the callback function
			//that handles the server response. 
			$.post(ajaxurl, {
				
				nonce:  ajax_admin.nonce,
				action: 'admin_hook',//this is what connects the request to the function in the PHP file.
				url:    url
				
			}, function(data) {//This is a callback function that handles the response data. 
				
     /******HERE IS WHAT I WOULD LIKE TO DO WITH THE DATA RETURNED******************/

			// 	$.each(data.items, function(item){
			//   var html = "<ul><li><a href="item.link">item.title</a></li>
			//   $('.ajax-response').html(html);
			// 	});
			// },
			// error: function(e){
			// 	console.log(e.message);
			// }

				//Debugging returned data
				console.log(data);
				
				//Inserting data into page.
				$('.ajax-response').html(data);
				
			});
			
		});
		
	});
	
})( jQuery );
