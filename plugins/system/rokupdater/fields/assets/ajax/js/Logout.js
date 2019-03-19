/*
 * @version   $Id: Logout.js 9189 2013-04-09 00:44:45Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

;((function(){
    var Logout = new Class({

        initialize: function(){
            this.element = document.getElement('[data-rokupdater-logout]');
            if (!this.element) return;

            this.spinner = this.element.getElement('i');
            this.xhr = new Request({
                url: this.element.get('href'),
                timeout: 120000,
                onRequest: this.request.bind(this),
                onSuccess: this.success.bind(this),
                onFailure: this.failure.bind(this),
                onTimeout: this.timeout.bind(this)
            });

            this.attach();
        },

        attach: function(){
            var click = document.retrieve('rokupdater:auth:logout', function(event, element){
                    if (event) event.preventDefault();

                    this.xhr.cancel().send();
                }.bind(this));

            document.addEvents({
                'click:relay([data-rokupdater-logout])': click
            });
        },

        detach: function(){
            var click = document.retrieve('rokupdater:auth:logout');

            document.removeEvents({
                'click:relay([data-rokupdater-logout])': click
            });
        },

        request: function(){
            this.spinner.addClass('mini-spinner');
        },

        success: function(response){
            //this.spinner.removeClass('mini-spinner');
            window.location.reload();
        },

        failure: function(message){
            //this.spinner.removeClass('mini-spinner');
            window.location.reload();
        },

        timeout: function(){
            //this.spinner.removeClass('mini-spinner');
            this.xhr.cancel();
            window.location.reload();
        }

    });

    window.addEvent('domready', function(){

        new Logout();

    });


})());
