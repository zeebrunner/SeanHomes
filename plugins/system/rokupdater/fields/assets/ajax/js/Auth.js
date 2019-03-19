/*
 * @version   $Id: Auth.js 9082 2013-04-03 00:11:49Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

;((function(){
    var Auth = new Class({

        initialize: function(){
            this.form = document.getElement('form');
            this.errorMsg = document.getElement('[data-error-msg]');
            this.spinner = document.getElement('[data-spinner]');
            this.xhr = new Request({
                url: this.form.get('action'),
                timeout: 120000,
                onRequest: this.request.bind(this),
                onSuccess: this.success.bind(this),
                onFailure: this.failure.bind(this),
                onTimeout: this.timeout.bind(this)
            });

            this.attach();
        },

        attach: function(){
            var queryStr, queryObj;

            var click = document.retrieve('rokupdater:auth', function(event, element){
                    if (event) event.preventDefault();

                    queryStr = this.form.toQueryString(),
                    queryObj = queryStr.cleanQueryString().parseQueryString();

                    this.xhr.cancel().send({data: queryObj});
                }.bind(this)),

                request = document.retrieve('rokupdater:request', function(event, element){
                    if (event.key != 'enter') return true;

                    click.call(this);
                }.bind(this));

            document.addEvents({
                'click:relay([data-auth])': click,
                'keyup:relay(input)': request
            });
        },

        detach: function(){
            var click = document.retrieve('rokupdater:auth'),
                request = document.retrieve('rokupdater:request');

            document.removeEvents({
                'click:relay([data-auth])': click,
                'keyup:relay(input)': request
            });
        },

        request: function(){
            this.spinner.setStyle('display', 'block');
            moofx(this.errorMsg).animate({opacity: 0, transform: 'scale(0)'}, {duration: '150ms', callback: function(){
                moofx(this.errorMsg).style({display: 'none'});
                this.errorMsg.set('text', '');
            }.bind(this)});
        },

        success: function(response){
            this.spinner.setStyle('display', 'none');
            if (!JSON.validate(response)) return this.failure('Invalid JSON: ' + response);
            response = JSON.decode(response);
            if (response.status && response.status != 'success') return this.failure(response.message ? response.message : response);

            moofx(this.errorMsg.addClass('success')).style({display: 'block', opacity: 0, transform: 'scale(0.5)'});
            this.errorMsg.set('text', response.message);
            moofx(this.errorMsg).animate({opacity: 1, transform: 'scale(1)'}, {duration: '150ms', callback: function(){
                window.parent.location.reload();
                //window.parent.SqueezeBox.close();
            }});
        },

        failure: function(message){
            this.spinner.setStyle('display', 'none');
            message = message == this.xhr.xhr ? 'error' : message;

            moofx(this.errorMsg).style({display: 'block', opacity: 0, transform: 'scale(0.5)'});
            this.errorMsg.set('text', message);
            moofx(this.errorMsg).animate({opacity: 1, transform: 'scale(1)'}, {duration: '150ms'});
        },

        timeout: function(){
            this.spinner.setStyle('display', 'none');
            this.xhr.cancel();
            this.failure('The request timed out after ' + (120000 / 1000 / 60) + 'minutes. Please try again.');
        }

    });

    window.addEvent('domready', function(){

        new Auth();

    });

    String.implement({

        parseQueryString: function(decodeKeys, decodeValues){
            if (decodeKeys == null) decodeKeys = true;
            if (decodeValues == null) decodeValues = true;

            var vars = this.split(/[&;]/),
            object = {};
            if (!vars.length) return object;

            vars.each(function(val){
                var index = val.indexOf('=') + 1,
                value = index ? val.substr(index) : '',
                keys = index ? val.substr(0, index - 1).match(/([^\]\[]+|(\B)(?=\]))/g) : [val],
                obj = object;
                if (!keys) return;
                if (decodeValues) value = decodeURIComponent(value);
                keys.each(function(key, i){
                    if (decodeKeys) key = decodeURIComponent(key);
                    var current = obj[key];

                    if (i < keys.length - 1) obj = obj[key] = current || {};
                    else if (typeOf(current) == 'array') current.push(value);
                    else obj[key] = current != null ? [current, value] : value;
                });
            });

            return object;
        },

        cleanQueryString: function(method){
            return this.split('&').filter(function(val){
                var index = val.indexOf('='),
                key = index < 0 ? '' : val.substr(0, index),
                value = val.substr(index + 1);

                return method ? method.call(null, key, value) : (value || value === 0);
            }).join('&');
        }

    });

})());
