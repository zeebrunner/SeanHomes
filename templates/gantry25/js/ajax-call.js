((function(){
    window.addEvent('domready', function(){
	// Request Object
    var request = new Request({
    url: GantryAjaxURL, // <r;- remember, this is the ajax url, explained in The Ajax URL section.
    onSuccess: function(response){
    alert(response);
    }
    });
    // Request post
    request.post({
    model: 'example', // <r;- the model must always be passed in
    animal: 'Cat',
    name: 'Pixel'
    });
    });
})());