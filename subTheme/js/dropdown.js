/*
 * DropDown menus
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory(root));
    } else if (typeof exports === 'object') {
        module.exports = factory(root);
    } else {
        root.dropdownMenu = factory(root);
    }
})(typeof global !== 'undefined' ? global : this.window || this.global, (function (root) {

    'use strict';

    /**
     * Variables
     * @private
     */
    var dropdownMenu = {}; // object for public APIs
    var supports = 'querySelector' in document && 'addEventListener' in root && 'classList' in document.createElement('_'); // feature test
    var settings;
    
    // default settings
    var defaults = {
        selector: 'dropdown-menu',
        activeClass: 'active',
        openClass: 'open'
    }


    /**
     * A simple forEach() implementation for Arrays, Objects and NodeLists
     * @private
     * @param {Array|Object|NodeList} collection Collection of items to iterate
     * @param {Function} callback Callback function for each iteration
     * @param {Array|Object|NodeList} scope Object/NodeList/Array that forEach is iterating over (aka `this`)
     */
    var forEach = function (collection, callback, scope) {
      if (Object.prototype.toString.call(collection) === '[object Object]') {
        for (var prop in collection) {
          if (Object.prototype.hasOwnProperty.call(collection, prop)) {
            callback.call(scope, collection[prop], prop, collection);
          }
        }
      } else {
        for (var i = 0, len = collection.length; i < len; i++) {
          callback.call(scope, collection[i], i, collection);
        }
      }
    };


    /**
     * Merge defaults with user options
     * @private
     * @param {Object} defaults Default settings
     * @param {Object} options User options
     * @returns {Object} Merged values of defaults and options
     */
    var extend = function () {

        // Variables
        var extended = {};
        var deep = false;
        var i = 0;
        var length = arguments.length;

        // Check if a deep merge
        if ( Object.prototype.toString.call( arguments[0] ) === '[object Boolean]' ) {
            deep = arguments[0];
            i++;
        }

        // Merge the object into the extended object
        var merge = function (obj) {
            for ( var prop in obj ) {
                if ( Object.prototype.hasOwnProperty.call( obj, prop ) ) {
                    // If deep merge and property is an object, merge properties
                    if ( deep && Object.prototype.toString.call(obj[prop]) === '[object Object]' ) {
                        extended[prop] = buoy.extend( true, extended[prop], obj[prop] );
                    } else {
                        extended[prop] = obj[prop];
                    }
                }
            }
        };

        // Loop through each object and conduct a merge
        for ( ; i < length; i++ ) {
            var obj = arguments[i];
            merge(obj);
        }

        return extended;

    };


    /**
     * Handle document click/touch events
     * @param {event} event
     * @private
     */
    var eventHandler = function(event) {

        // toggle classes when event target is dropdown trigger, else close all dropdowns
        if (event.target.parentElement.classList.contains(settings.selector)) {
            event.preventDefault();
            toggleClasses(event.target);
        } else if (event.type == 'click') {
            dropdownMenu.close();
        }
    };


    /**
     * Toggle dropdown classes
     * @param {elem} trigger element 'dropdown > a'
     * @private
     */
    var toggleClasses = function(elem) {

        // toggle trigger class
        elem.classList.toggle(settings.activeClass);

        // toggle dropdown class
        elem.parentElement.classList.toggle(settings.openClass);
    };


    /**
     * Close all active dropdownmenu's'.
     * @public
     */
    dropdownMenu.close = function() {

        // get all open dropdown triggers
        var dropdownMenus = document.querySelectorAll('.' + settings.selector + '.' + settings.openClass + ' > a');

    // Close all the dropdowns
    forEach(dropdownMenus, (function (dropdownMenu) {
      toggleClasses(dropdownMenu);
    }));

    };


    /**
     * Destroy the current initialization.
     * @public
     */
    dropdownMenu.destroy = function () {

        // Remove event listeners
        document.removeEventListener('click', eventHandler, false);
        document.removeEventListener('touchstart', eventHandler, false);
        window.removeEventListener('resize', dropdownMenu.close, false);

        // Close all dropdowns
        dropdownMenu.close;

    };


    // public method init
    dropdownMenu.init = function(options) {

        // feature test
        if (!supports) return;

        // Destroy any existing initializations
        dropdownMenu.destroy();

        // merge user options with defaults
        settings = extend( defaults, options || {} );

        // add event listeners
        document.addEventListener('click', eventHandler, false);
        document.addEventListener('touchstart', eventHandler, false);
        window.addEventListener('resize', dropdownMenu.close, false);
    };

    return dropdownMenu;

}));











