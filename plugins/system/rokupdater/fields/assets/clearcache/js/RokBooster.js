/*!
 * @version   $Id: RokBooster.js 10080 2013-05-06 21:53:21Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){

	var RokBooster = this.RokBooster = {

		init: function() {
			RokBooster.ClearCache();
		},

		ClearCache: function(){
			// get the clearCache button from the dom
			var button = document.getElement('[data-action=clearCache]');

			// storing the Request instance for reusing/being able to cancel/etc
			button.store('ajax', new Request({
				url: '../plugins/system/rokupdater/ajax.php',
					onRequest: function(response){ RokBooster.ClearCacheRequest(this, button, response); },
					onSuccess: function(response){ RokBooster.ClearCacheSuccess(this, button, response); }
			}));

			// attaching the click event to the button for sending the ajax call
			button.addEvent('click', function(e){
				e.preventDefault();
				var ajax = this.retrieve('ajax');

				if (!ajax.isRunning()) ajax.send();
			});
		},

		// before making the ajax request, let's add the spinner
		ClearCacheRequest: function(ajax, button, response){
			button.addClass('boost-spinner').set('title', '');
		},

		// ajax successfully went through, removing the spinner
		ClearCacheSuccess: function(ajax, button, response){
			button.removeClass('boost-spinner');

			response = response.clean();
			if (!response.length || !JSON.validate(response)){
				//button.getElement('.count').set('text', '!');
				button.set('title', 'Invalid JSON response: ' + response);
				throw new Error('RokBooster: Invalid JSON response: "'+response+'"');
			} else {
				response = JSON.decode(response);
				if (response.status == 'error'){
					//button.getElement('.count').set('text', '!');
					button.set('title', 'Unable to purge cache: ' + response.message);
					throw new Error('RokBooster: Error while purging the cache: "'+response.message || 'no_message'+'"');
				}

				if (response.status == 'success'){
					//button.getElement('.count').set('text', response.message);
				}
			}
		}

	};



	window.addEvent('domready', RokBooster.init);

})();
