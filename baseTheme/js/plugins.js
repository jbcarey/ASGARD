/*!
 * details-polyfill
 * removed css & classes
 */

void (function (root, factory) {
  if (typeof define === 'function' && define.amd) define(factory)
  else if (typeof exports === 'object') module.exports = factory()
  else factory()
}(this, (function () {
  var DETAILS = 'details'
  var SUMMARY = 'summary'

  var supported = checkSupport()
  if (supported) return

  window.addEventListener('click', clickHandler)

  /*
   * Click handler for `<summary>` tags
   */

  function clickHandler (e) {
    if (e.target.nodeName.toLowerCase() === 'summary') {
      var details = e.target.parentNode
      if (!details) return

      if (details.getAttribute('open')) {
        details.open = false
        details.removeAttribute('open')
      } else {
        details.open = true
        details.setAttribute('open', 'open')
      }
    }
  }

  /*
   * Checks for support for `<details>`
   */

  function checkSupport () {
    var el = document.createElement(DETAILS)
    if (!('open' in el)) return false

    el.innerHTML = '<' + SUMMARY + '>a</' + SUMMARY + '>b'
    document.body.appendChild(el)

    var diff = el.offsetHeight
    el.open = true
    var result = (diff != el.offsetHeight)

    document.body.removeChild(el)
    return result
  }

  /*
   * Injects styles (idempotent)
   */

  function injectStyle (id, style) {
    if (document.getElementById(id)) return

    var el = document.createElement('style')
    el.id = id
    el.innerHTML = style

    document.getElementsByTagName('head')[0].appendChild(el)
  }
}))); // eslint-disable-line semi

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












/*!
 * smooth-scroll v10.1.0: Animate scrolling to anchor links
 * (c) 2016 Chris Ferdinandi
 * MIT License
 * http://github.com/cferdinandi/smooth-scroll
 */

(function (root, factory) {
	if ( typeof define === 'function' && define.amd ) {
		define([], factory(root));
	} else if ( typeof exports === 'object' ) {
		module.exports = factory(root);
	} else {
		root.smoothScroll = factory(root);
	}
})(typeof global !== 'undefined' ? global : this.window || this.global, (function (root) {

	'use strict';

	//
	// Variables
	//

	var smoothScroll = {}; // Object for public APIs
	var supports = 'querySelector' in document && 'addEventListener' in root; // Feature test
	var settings, anchor, toggle, fixedHeader, headerHeight, eventTimeout, animationInterval;

	// Default settings
	var defaults = {
		selector: '[data-scroll]',
		selectorHeader: null,
		speed: 500,
		easing: 'easeInOutCubic',
		offset: 0,
		callback: function () {}
	};


	//
	// Methods
	//

	/**
	 * Merge two or more objects. Returns a new object.
	 * @private
	 * @param {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
	 * @param {Object}   objects  The objects to merge together
	 * @returns {Object}          Merged values of defaults and options
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
						extended[prop] = extend( true, extended[prop], obj[prop] );
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
	 * Get the height of an element.
	 * @private
	 * @param  {Node} elem The element to get the height of
	 * @return {Number}    The element's height in pixels
	 */
	var getHeight = function ( elem ) {
		return Math.max( elem.scrollHeight, elem.offsetHeight, elem.clientHeight );
	};

	/**
	 * Get the closest matching element up the DOM tree.
	 * @private
	 * @param  {Element} elem     Starting element
	 * @param  {String}  selector Selector to match against (class, ID, data attribute, or tag)
	 * @return {Boolean|Element}  Returns null if not match found
	 */
	var getClosest = function ( elem, selector ) {

		// Variables
		var firstChar = selector.charAt(0);
		var supports = 'classList' in document.documentElement;
		var attribute, value;

		// If selector is a data attribute, split attribute from value
		if ( firstChar === '[' ) {
			selector = selector.substr(1, selector.length - 2);
			attribute = selector.split( '=' );

			if ( attribute.length > 1 ) {
				value = true;
				attribute[1] = attribute[1].replace( /"/g, '' ).replace( /'/g, '' );
			}
		}

		// Get closest match
		for ( ; elem && elem !== document && elem.nodeType === 1; elem = elem.parentNode ) {

			// If selector is a class
			if ( firstChar === '.' ) {
				if ( supports ) {
					if ( elem.classList.contains( selector.substr(1) ) ) {
						return elem;
					}
				} else {
					if ( new RegExp('(^|\\s)' + selector.substr(1) + '(\\s|$)').test( elem.className ) ) {
						return elem;
					}
				}
			}

			// If selector is an ID
			if ( firstChar === '#' ) {
				if ( elem.id === selector.substr(1) ) {
					return elem;
				}
			}

			// If selector is a data attribute
			if ( firstChar === '[' ) {
				if ( elem.hasAttribute( attribute[0] ) ) {
					if ( value ) {
						if ( elem.getAttribute( attribute[0] ) === attribute[1] ) {
							return elem;
						}
					} else {
						return elem;
					}
				}
			}

			// If selector is a tag
			if ( elem.tagName.toLowerCase() === selector ) {
				return elem;
			}

		}

		return null;

	};

	/**
	 * Escape special characters for use with querySelector
	 * @private
	 * @param {String} id The anchor ID to escape
	 * @author Mathias Bynens
	 * @link https://github.com/mathiasbynens/CSS.escape
	 */
	var escapeCharacters = function ( id ) {

		// Remove leading hash
		if ( id.charAt(0) === '#' ) {
			id = id.substr(1);
		}

		var string = String(id);
		var length = string.length;
		var index = -1;
		var codeUnit;
		var result = '';
		var firstCodeUnit = string.charCodeAt(0);
		while (++index < length) {
			codeUnit = string.charCodeAt(index);
			// Note: there’s no need to special-case astral symbols, surrogate
			// pairs, or lone surrogates.

			// If the character is NULL (U+0000), then throw an
			// `InvalidCharacterError` exception and terminate these steps.
			if (codeUnit === 0x0000) {
				throw new InvalidCharacterError(
					'Invalid character: the input contains U+0000.'
				);
			}

			if (
				// If the character is in the range [\1-\1F] (U+0001 to U+001F) or is
				// U+007F, […]
				(codeUnit >= 0x0001 && codeUnit <= 0x001F) || codeUnit == 0x007F ||
				// If the character is the first character and is in the range [0-9]
				// (U+0030 to U+0039), […]
				(index === 0 && codeUnit >= 0x0030 && codeUnit <= 0x0039) ||
				// If the character is the second character and is in the range [0-9]
				// (U+0030 to U+0039) and the first character is a `-` (U+002D), […]
				(
					index === 1 &&
					codeUnit >= 0x0030 && codeUnit <= 0x0039 &&
					firstCodeUnit === 0x002D
				)
			) {
				// http://dev.w3.org/csswg/cssom/#escape-a-character-as-code-point
				result += '\\' + codeUnit.toString(16) + ' ';
				continue;
			}

			// If the character is not handled by one of the above rules and is
			// greater than or equal to U+0080, is `-` (U+002D) or `_` (U+005F), or
			// is in one of the ranges [0-9] (U+0030 to U+0039), [A-Z] (U+0041 to
			// U+005A), or [a-z] (U+0061 to U+007A), […]
			if (
				codeUnit >= 0x0080 ||
				codeUnit === 0x002D ||
				codeUnit === 0x005F ||
				codeUnit >= 0x0030 && codeUnit <= 0x0039 ||
				codeUnit >= 0x0041 && codeUnit <= 0x005A ||
				codeUnit >= 0x0061 && codeUnit <= 0x007A
			) {
				// the character itself
				result += string.charAt(index);
				continue;
			}

			// Otherwise, the escaped character.
			// http://dev.w3.org/csswg/cssom/#escape-a-character
			result += '\\' + string.charAt(index);

		}

		return '#' + result;

	};

	/**
	 * Calculate the easing pattern
	 * @private
	 * @link https://gist.github.com/gre/1650294
	 * @param {String} type Easing pattern
	 * @param {Number} time Time animation should take to complete
	 * @returns {Number}
	 */
	var easingPattern = function ( type, time ) {
		var pattern;
		if ( type === 'easeInQuad' ) pattern = time * time; // accelerating from zero velocity
		if ( type === 'easeOutQuad' ) pattern = time * (2 - time); // decelerating to zero velocity
		if ( type === 'easeInOutQuad' ) pattern = time < 0.5 ? 2 * time * time : -1 + (4 - 2 * time) * time; // acceleration until halfway, then deceleration
		if ( type === 'easeInCubic' ) pattern = time * time * time; // accelerating from zero velocity
		if ( type === 'easeOutCubic' ) pattern = (--time) * time * time + 1; // decelerating to zero velocity
		if ( type === 'easeInOutCubic' ) pattern = time < 0.5 ? 4 * time * time * time : (time - 1) * (2 * time - 2) * (2 * time - 2) + 1; // acceleration until halfway, then deceleration
		if ( type === 'easeInQuart' ) pattern = time * time * time * time; // accelerating from zero velocity
		if ( type === 'easeOutQuart' ) pattern = 1 - (--time) * time * time * time; // decelerating to zero velocity
		if ( type === 'easeInOutQuart' ) pattern = time < 0.5 ? 8 * time * time * time * time : 1 - 8 * (--time) * time * time * time; // acceleration until halfway, then deceleration
		if ( type === 'easeInQuint' ) pattern = time * time * time * time * time; // accelerating from zero velocity
		if ( type === 'easeOutQuint' ) pattern = 1 + (--time) * time * time * time * time; // decelerating to zero velocity
		if ( type === 'easeInOutQuint' ) pattern = time < 0.5 ? 16 * time * time * time * time * time : 1 + 16 * (--time) * time * time * time * time; // acceleration until halfway, then deceleration
		return pattern || time; // no easing, no acceleration
	};

	/**
	 * Calculate how far to scroll
	 * @private
	 * @param {Element} anchor The anchor element to scroll to
	 * @param {Number} headerHeight Height of a fixed header, if any
	 * @param {Number} offset Number of pixels by which to offset scroll
	 * @returns {Number}
	 */
	var getEndLocation = function ( anchor, headerHeight, offset ) {
		var location = 0;
		if (anchor.offsetParent) {
			do {
				location += anchor.offsetTop;
				anchor = anchor.offsetParent;
			} while (anchor);
		}
		location = Math.max(location - headerHeight - offset, 0);
		return Math.min(location, getDocumentHeight() - getViewportHeight());
	};

	/**
	 * Determine the viewport's height
	 * @private
	 * @returns {Number}
	 */
	var getViewportHeight = function() {
		return Math.max( document.documentElement.clientHeight, root.innerHeight || 0 );
	};

	/**
	 * Determine the document's height
	 * @private
	 * @returns {Number}
	 */
	var getDocumentHeight = function () {
		return Math.max(
			document.body.scrollHeight, document.documentElement.scrollHeight,
			document.body.offsetHeight, document.documentElement.offsetHeight,
			document.body.clientHeight, document.documentElement.clientHeight
		);
	};

	/**
	 * Convert data-options attribute into an object of key/value pairs
	 * @private
	 * @param {String} options Link-specific options as a data attribute string
	 * @returns {Object}
	 */
	var getDataOptions = function ( options ) {
		return !options || !(typeof JSON === 'object' && typeof JSON.parse === 'function') ? {} : JSON.parse( options );
	};

	/**
	 * Get the height of the fixed header
	 * @private
	 * @param  {Node}   header The header
	 * @return {Number}        The height of the header
	 */
	var getHeaderHeight = function ( header ) {
		return !header ? 0 : ( getHeight( header ) + header.offsetTop );
	};

	/**
	 * Bring the anchored element into focus
	 * @private
	 */
	var adjustFocus = function ( anchor, endLocation, isNum ) {

		// Don't run if scrolling to a number on the page
		if ( isNum ) return;

		// Otherwise, bring anchor element into focus
		anchor.focus();
		if ( document.activeElement.id !== anchor.id ) {
			anchor.setAttribute( 'tabindex', '-1' );
			anchor.focus();
			anchor.style.outline = 'none';
		}
		root.scrollTo( 0 , endLocation );

	};

	/**
	 * Start/stop the scrolling animation
	 * @public
	 * @param {Node|Number} anchor  The element or position to scroll to
	 * @param {Element}     toggle  The element that toggled the scroll event
	 * @param {Object}      options
	 */
	smoothScroll.animateScroll = function ( anchor, toggle, options ) {

		// Options and overrides
		var overrides = getDataOptions( toggle ? toggle.getAttribute('data-options') : null );
		var animateSettings = extend( settings || defaults, options || {}, overrides ); // Merge user options with defaults

		// Selectors and variables
		var isNum = Object.prototype.toString.call( anchor ) === '[object Number]' ? true : false;
		var anchorElem = isNum || !anchor.tagName ? null : anchor;
		if ( !isNum && !anchorElem ) return;
		var startLocation = root.pageYOffset; // Current location on the page
		if ( animateSettings.selectorHeader && !fixedHeader ) {
			// Get the fixed header if not already set
			fixedHeader = document.querySelector( animateSettings.selectorHeader );
		}
		if ( !headerHeight ) {
			// Get the height of a fixed header if one exists and not already set
			headerHeight = getHeaderHeight( fixedHeader );
		}
		var endLocation = isNum ? anchor : getEndLocation( anchorElem, headerHeight, parseInt(animateSettings.offset, 10) ); // Location to scroll to
		var distance = endLocation - startLocation; // distance to travel
		var documentHeight = getDocumentHeight();
		var timeLapsed = 0;
		var percentage, position;

		/**
		 * Stop the scroll animation when it reaches its target (or the bottom/top of page)
		 * @private
		 * @param {Number} position Current position on the page
		 * @param {Number} endLocation Scroll to location
		 * @param {Number} animationInterval How much to scroll on this loop
		 */
		var stopAnimateScroll = function ( position, endLocation, animationInterval ) {
			var currentLocation = root.pageYOffset;
			if ( position == endLocation || currentLocation == endLocation || ( (root.innerHeight + currentLocation) >= documentHeight ) ) {

				// Clear the animation timer
				clearInterval(animationInterval);

				// Bring the anchored element into focus
				adjustFocus( anchor, endLocation, isNum );

				// Run callback after animation complete
				animateSettings.callback( anchor, toggle );

			}
		};

		/**
		 * Loop scrolling animation
		 * @private
		 */
		var loopAnimateScroll = function () {
			timeLapsed += 16;
			percentage = ( timeLapsed / parseInt(animateSettings.speed, 10) );
			percentage = ( percentage > 1 ) ? 1 : percentage;
			position = startLocation + ( distance * easingPattern(animateSettings.easing, percentage) );
			root.scrollTo( 0, Math.floor(position) );
			stopAnimateScroll(position, endLocation, animationInterval);
		};

		/**
		 * Set interval timer
		 * @private
		 */
		var startAnimateScroll = function () {
			clearInterval(animationInterval);
			animationInterval = setInterval(loopAnimateScroll, 16);
		};

		/**
		 * Reset position to fix weird iOS bug
		 * @link https://github.com/cferdinandi/smooth-scroll/issues/45
		 */
		if ( root.pageYOffset === 0 ) {
			root.scrollTo( 0, 0 );
		}

		// Start scrolling animation
		startAnimateScroll();

	};

	/**
	 * Handle has change event
	 * @private
	 */
	var hashChangeHandler = function (event) {

		// Get hash from URL
		var hash = root.location.hash;

		// Only run if there's an anchor element to scroll to
		if ( !anchor ) return;

		// Reset the anchor element's ID
		anchor.id = anchor.getAttribute( 'data-scroll-id' );

		// Scroll to the anchored content
		smoothScroll.animateScroll( anchor, toggle );

		// Reset anchor and toggle
		anchor = null;
		toggle = null;

	};

	/**
	 * If smooth scroll element clicked, animate scroll
	 * @private
	 */
	var clickHandler = function (event) {

		// Don't run if right-click or command/control + click
		if ( event.button !== 0 || event.metaKey || event.ctrlKey ) return;

		// Check if a smooth scroll link was clicked
		toggle = getClosest( event.target, settings.selector );
		if ( !toggle || toggle.tagName.toLowerCase() !== 'a' ) return;

		// Only run if link is an anchor and points to the current page
		if ( toggle.hostname !== root.location.hostname || toggle.pathname !== root.location.pathname || !/#/.test(toggle.href) ) return;

		// Get the sanitized hash
		var hash = escapeCharacters( toggle.hash );

		// If the hash is empty, scroll to the top of the page
		if ( hash === '#' ) {

			// Prevent default link behavior
			event.preventDefault();

			// Set the anchored element
			anchor = document.body;

			// Save or create the ID as a data attribute and remove it (prevents scroll jump)
			var id = anchor.id ? anchor.id : 'smooth-scroll-top';
			anchor.setAttribute( 'data-scroll-id', id );
			anchor.id = '';

			// If no hash change event will happen, fire manually
			// Otherwise, update the hash
			if ( root.location.hash.substring(1) === id ) {
				hashChangeHandler();
			} else {
				root.location.hash = id;
			}

			return;

		}

		// Get the anchored element
		anchor = document.querySelector( hash );

		// If anchored element exists, save the ID as a data attribute and remove it (prevents scroll jump)
		if ( !anchor ) return;
		anchor.setAttribute( 'data-scroll-id', anchor.id );
		anchor.id = '';

		// If no hash change event will happen, fire manually
		if ( toggle.hash === root.location.hash ) {
			event.preventDefault();
			hashChangeHandler();
		}

	};

	/**
	 * On window scroll and resize, only run events at a rate of 15fps for better performance
	 * @private
	 * @param  {Function} eventTimeout Timeout function
	 * @param  {Object} settings
	 */
	var resizeThrottler = function (event) {
		if ( !eventTimeout ) {
			eventTimeout = setTimeout((function() {
				eventTimeout = null; // Reset timeout
				headerHeight = getHeaderHeight( fixedHeader ); // Get the height of a fixed header if one exists
			}), 66);
		}
	};

	/**
	 * Destroy the current initialization.
	 * @public
	 */
	smoothScroll.destroy = function () {

		// If plugin isn't already initialized, stop
		if ( !settings ) return;

		// Remove event listeners
		document.removeEventListener( 'click', clickHandler, false );
		root.removeEventListener( 'resize', resizeThrottler, false );

		// Reset varaibles
		settings = null;
		anchor = null;
		toggle = null;
		fixedHeader = null;
		headerHeight = null;
		eventTimeout = null;
		animationInterval = null;
	};

	/**
	 * Initialize Smooth Scroll
	 * @public
	 * @param {Object} options User settings
	 */
	smoothScroll.init = function ( options ) {

		// feature test
		if ( !supports ) return;

		// Destroy any existing initializations
		smoothScroll.destroy();

		// Selectors and variables
		settings = extend( defaults, options || {} ); // Merge user options with defaults
		fixedHeader = settings.selectorHeader ? document.querySelector( settings.selectorHeader ) : null; // Get the fixed header
		headerHeight = getHeaderHeight( fixedHeader );

		// When a toggle is clicked, run the click handler
		document.addEventListener( 'click', clickHandler, false );

		// Listen for hash changes
		root.addEventListener('hashchange', hashChangeHandler, false);

		// If window is resized and there's a fixed header, recalculate its size
		if ( fixedHeader ) {
			root.addEventListener( 'resize', resizeThrottler, false );
		}

	};


	//
	// Public APIs
	//

	return smoothScroll;

}));
/*!
 * gumshoe v3.3.1: A simple, framework-agnostic scrollspy script.
 * (c) 2016 Chris Ferdinandi
 * MIT License
 * http://github.com/cferdinandi/gumshoe
 */

(function (root, factory) {
	if ( typeof define === 'function' && define.amd ) {
		define([], factory(root));
	} else if ( typeof exports === 'object' ) {
		module.exports = factory(root);
	} else {
		root.gumshoe = factory(root);
	}
})(typeof global !== 'undefined' ? global : this.window || this.global, (function (root) {

	'use strict';

	//
	// Variables
	//

	var gumshoe = {}; // Object for public APIs
	var supports = 'querySelector' in document && 'addEventListener' in root && 'classList' in document.createElement('_'); // Feature test
	var navs = []; // Array for nav elements
	var settings, eventTimeout, docHeight, header, headerHeight, currentNav;

	// Default settings
	var defaults = {
		selector: '[data-gumshoe] a',
		selectorHeader: '[data-gumshoe-header]',
		container: root,
		offset: 0,
		activeClass: 'active',
		callback: function () {}
	};


	//
	// Methods
	//

	/**
	 * A simple forEach() implementation for Arrays, Objects and NodeLists.
	 * @private
	 * @author Todd Motto
	 * @link   https://github.com/toddmotto/foreach
	 * @param {Array|Object|NodeList} collection Collection of items to iterate
	 * @param {Function}              callback   Callback function for each iteration
	 * @param {Array|Object|NodeList} scope      Object/NodeList/Array that forEach is iterating over (aka `this`)
	 */
	var forEach = function ( collection, callback, scope ) {
		if ( Object.prototype.toString.call( collection ) === '[object Object]' ) {
			for ( var prop in collection ) {
				if ( Object.prototype.hasOwnProperty.call( collection, prop ) ) {
					callback.call( scope, collection[prop], prop, collection );
				}
			}
		} else {
			for ( var i = 0, len = collection.length; i < len; i++ ) {
				callback.call( scope, collection[i], i, collection );
			}
		}
	};

	/**
	 * Merge two or more objects. Returns a new object.
	 * @private
	 * @param {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
	 * @param {Object}   objects  The objects to merge together
	 * @returns {Object}          Merged values of defaults and options
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
						extended[prop] = extend( true, extended[prop], obj[prop] );
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
	 * Get the height of an element.
	 * @private
	 * @param  {Node} elem The element to get the height of
	 * @return {Number}    The element's height in pixels
	 */
	var getHeight = function ( elem ) {
		return Math.max( elem.scrollHeight, elem.offsetHeight, elem.clientHeight );
	};

	/**
	 * Get the document element's height
	 * @private
	 * @returns {Number}
	 */
	var getDocumentHeight = function () {
		return Math.max(
			document.body.scrollHeight, document.documentElement.scrollHeight,
			document.body.offsetHeight, document.documentElement.offsetHeight,
			document.body.clientHeight, document.documentElement.clientHeight
		);
	};

	/**
	 * Get an element's distance from the top of the Document.
	 * @private
	 * @param  {Node} elem The element
	 * @return {Number}    Distance from the top in pixels
	 */
	var getOffsetTop = function ( elem ) {
		var location = 0;
		if (elem.offsetParent) {
			do {
				location += elem.offsetTop;
				elem = elem.offsetParent;
			} while (elem);
		} else {
			location = elem.offsetTop;
		}
		location = location - headerHeight - settings.offset;
		return location >= 0 ? location : 0;
	};

	/**
	 * Determine if an element is in the viewport
	 * @param  {Node}    elem The element
	 * @return {Boolean}      Returns true if element is in the viewport
	 */
	var isInViewport = function ( elem ) {
		var distance = elem.getBoundingClientRect();
		return (
			distance.top >= 0 &&
			distance.left >= 0 &&
			distance.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
			distance.right <= (window.innerWidth || document.documentElement.clientWidth)
		);
	};

	/**
	 * Arrange nagivation elements from furthest from the top to closest
	 * @private
	 */
	var sortNavs = function () {
		navs.sort( ((function (a, b) {
			if (a.distance > b.distance) {
				return -1;
			}
			if (a.distance < b.distance) {
				return 1;
			}
			return 0;
		})));
	};

	/**
	 * Calculate the distance of elements from the top of the document
	 * @public
	 */
	gumshoe.setDistances = function () {

		// Calculate distances
		docHeight = getDocumentHeight(); // The document
		headerHeight = header ? ( getHeight(header) + getOffsetTop(header) ) : 0; // The fixed header
		forEach(navs, (function (nav) {
			nav.distance = getOffsetTop(nav.target); // Each navigation target
		}));

		// When done, organization navigation elements
		sortNavs();

	};

	/**
	 * Get all navigation elements and store them in an array
	 * @private
	 */
	var getNavs = function () {

		// Get all navigation links
		var navLinks = document.querySelectorAll( settings.selector );

		// For each link, create an object of attributes and push to an array
		forEach( navLinks, (function (nav) {
			if ( !nav.hash ) return;
			var target = document.querySelector( nav.hash );
			if ( !target ) return;
			navs.push({
				nav: nav,
				target: target,
				parent: nav.parentNode.tagName.toLowerCase() === 'li' ? nav.parentNode : null,
				distance: 0
			});
		}));

	};


	/**
	 * Remove the activation class from the currently active navigation element
	 * @private
	 */
	var deactivateCurrentNav = function () {
		if ( currentNav ) {
			currentNav.nav.classList.remove( settings.activeClass );
			if ( currentNav.parent ) {
				currentNav.parent.classList.remove( settings.activeClass );
			}
		}
	};

	/**
	 * Add the activation class to the currently active navigation element
	 * @private
	 * @param  {Node} nav The currently active nav
	 */
	var activateNav = function ( nav ) {

		// If a current Nav is set, deactivate it
		deactivateCurrentNav();

		// Activate the current target's navigation element
		nav.nav.classList.add( settings.activeClass );
		if ( nav.parent ) {
			nav.parent.classList.add( settings.activeClass );
		}

		settings.callback( nav ); // Callback after methods are run

		// Set new currentNav
		currentNav = {
			nav: nav.nav,
			parent: nav.parent
		};

	};

	/**
	 * Determine which navigation element is currently active and run activation method
	 * @public
	 * @returns {Object} The current nav data.
	 */
	gumshoe.getCurrentNav = function () {

		// Get current position from top of the document
		var position = root.pageYOffset;

		// If at the bottom of the page and last section is in the viewport, activate the last nav
		if ( (root.innerHeight + position) >= docHeight && isInViewport( navs[0].target ) ) {
			activateNav( navs[0] );
			return navs[0];
		}

		// Otherwise, loop through each nav until you find the active one
		for (var i = 0, len = navs.length; i < len; i++) {
			var nav = navs[i];
			if ( nav.distance <= position ) {
				activateNav( nav );
				return nav;
			}
		}

		// If no active nav is found, deactivate the current nav
		deactivateCurrentNav();
		settings.callback();

	};

	/**
	 * If nav element has active class on load, set it as currently active navigation
	 * @private
	 */
	var setInitCurrentNav = function () {
		forEach(navs, (function (nav) {
			if ( nav.nav.classList.contains( settings.activeClass ) ) {
				currentNav = {
					nav: nav.nav,
					parent: nav.parent
				};
			}
		}));
	};

	/**
	 * Destroy the current initialization.
	 * @public
	 */
	gumshoe.destroy = function () {

		// If plugin isn't already initialized, stop
		if ( !settings ) return;

		// Remove event listeners
		settings.container.removeEventListener('resize', eventThrottler, false);
		settings.container.removeEventListener('scroll', eventThrottler, false);

		// Reset variables
		navs = [];
		settings = null;
		eventTimeout = null;
		docHeight = null;
		header = null;
		headerHeight = null;
		currentNav = null;

	};

	/**
	 * On window scroll and resize, only run events at a rate of 15fps for better performance
	 * @private
	 * @param  {Function} eventTimeout Timeout function
	 * @param  {Object} settings
	 */
	var eventThrottler = function (event) {
		if ( !eventTimeout ) {
			eventTimeout = setTimeout((function() {

				eventTimeout = null; // Reset timeout

				// If scroll event, get currently active nav
				if ( event.type === 'scroll' ) {
					gumshoe.getCurrentNav();
				}

				// If resize event, recalculate distances and then get currently active nav
				if ( event.type === 'resize' ) {
					gumshoe.setDistances();
					gumshoe.getCurrentNav();
				}

			}), 66);
		}
	};

	/**
	 * Initialize Plugin
	 * @public
	 * @param {Object} options User settings
	 */
	gumshoe.init = function ( options ) {

		// feature test
		if ( !supports ) return;

		// Destroy any existing initializations
		gumshoe.destroy();

		// Set variables
		settings = extend( defaults, options || {} ); // Merge user options with defaults
		header = document.querySelector( settings.selectorHeader ); // Get fixed header
		getNavs(); // Get navigation elements

		// If no navigation elements exist, stop running gumshoe
		if ( navs.length === 0 ) return;

		// Run init methods
		setInitCurrentNav();
		gumshoe.setDistances();
		gumshoe.getCurrentNav();

		// Listen for events
		settings.container.addEventListener('resize', eventThrottler, false);
		settings.container.addEventListener('scroll', eventThrottler, false);

	};


	//
	// Public APIs
	//

	return gumshoe;

}));

/*!
 * Modals v9.2.0: Simple modal dialogue pop-up windows
 * (c) 2016 Chris Ferdinandi
 * MIT License
 * http://github.com/cferdinandi/modals
 */

(function (root, factory) {
    if ( typeof define === 'function' && define.amd ) {
        define([], factory(root));
    } else if ( typeof exports === 'object' ) {
        module.exports = factory(root);
    } else {
        root.modals = factory(root);
    }
})(typeof global !== 'undefined' ? global : this.window || this.global, (function (root) {

    'use strict';

    //
    // Variables
    //

    var publicApi = {}; // Object for public APIs
    var supports = 'querySelector' in document && 'addEventListener' in root && 'classList' in document.createElement('_'); // Feature test
    var state = 'closed';
    var scrollbarWidth, placeholder, settings;

    // Default settings
    var defaults = {
        selectorToggle: '[data-modal]',
        selectorWindow: '[data-modal-window]',
        selectorClose: '[data-modal-close]',
        modalActiveClass: 'active',
        modalBGClass: 'modal-bg',
        preventBGScroll: false,
        preventBGScrollHtml: true,
        preventBGScrollBody: true,
        backspaceClose: true,
        stopVideo: true,
        callbackOpen: function () {},
        callbackClose: function () {}
    };


    //
    // Methods
    //

    /**
     * A simple forEach() implementation for Arrays, Objects and NodeLists.
     * @private
     * @author Todd Motto
     * @link   https://github.com/toddmotto/foreach
     * @param {Array|Object|NodeList} collection Collection of items to iterate
     * @param {Function}              callback   Callback function for each iteration
     * @param {Array|Object|NodeList} scope      Object/NodeList/Array that forEach is iterating over (aka `this`)
     */
    var forEach = function ( collection, callback, scope ) {
        if ( Object.prototype.toString.call( collection ) === '[object Object]' ) {
            for ( var prop in collection ) {
                if ( Object.prototype.hasOwnProperty.call( collection, prop ) ) {
                    callback.call( scope, collection[prop], prop, collection );
                }
            }
        } else {
            for ( var i = 0, len = collection.length; i < len; i++ ) {
                callback.call( scope, collection[i], i, collection );
            }
        }
    };

    /**
     * Merge two or more objects. Returns a new object.
     * @private
     * @param {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
     * @param {Object}   objects  The objects to merge together
     * @returns {Object}          Merged values of defaults and options
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
                        extended[prop] = extend( true, extended[prop], obj[prop] );
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
     * Get the closest matching element up the DOM tree.
     * @private
     * @param  {Element} elem     Starting element
     * @param  {String}  selector Selector to match against (class, ID, data attribute, or tag)
     * @return {Boolean|Element}  Returns null if not match found
     */
    var getClosest = function ( elem, selector ) {

        // Variables
        var firstChar = selector.charAt(0);
        var attribute, value;

        // If selector is a data attribute, split attribute from value
        if ( firstChar === '[' ) {
            selector = selector.substr(1, selector.length - 2);
            attribute = selector.split( '=' );

            if ( attribute.length > 1 ) {
                value = true;
                attribute[1] = attribute[1].replace( /"/g, '' ).replace( /'/g, '' );
            }
        }

        // Get closest match
        for ( ; elem && elem !== document; elem = elem.parentNode ) {

            // If selector is a class
            if ( firstChar === '.' ) {
                if ( elem.classList.contains( selector.substr(1) ) ) {
                    return elem;
                }
            }

            // If selector is an ID
            if ( firstChar === '#' ) {
                if ( elem.id === selector.substr(1) ) {
                    return elem;
                }
            }

            // If selector is a data attribute
            if ( firstChar === '[' ) {
                if ( elem.hasAttribute( attribute[0] ) ) {
                    if ( value ) {
                        if ( elem.getAttribute( attribute[0] ) === attribute[1] ) {
                            return elem;
                        }
                    } else {
                        return elem;
                    }
                }
            }

            // If selector is a tag
            if ( elem.tagName.toLowerCase() === selector ) {
                return elem;
            }

        }

        return null;

    };

    /**
     * Stop YouTube, Vimeo, and HTML5 videos from playing when leaving the slide
     * @private
     * @param  {Element} content The content container the video is in
     * @param  {String} activeClass The class asigned to expanded content areas
     */
    var stopVideos = function ( content, settings ) {

        // Check if stop video enabled
        if ( !settings.stopVideo ) return;

        // Only run if content container was open
        if ( !content.classList.contains( settings.modalActiveClass ) ) return;

        // Check if the video is an iframe or HTML5 video
        var iframe = content.querySelector( 'iframe');
        var video = content.querySelector( 'video' );

        // Stop the video
        if ( iframe ) {
            var iframeSrc = iframe.src;
            iframe.src = iframeSrc;
        }
        if ( video ) {
            video.pause();
        }

    };

    /**
     * Get the width of the scroll bars
     * @private
     */
    var getScrollbarWidth = function () {

        // Setup div
        var outer = document.createElement('div');
        outer.style.visibility = 'hidden';
        outer.style.width = '100px';
        outer.style.msOverflowStyle = 'scrollbar'; // needed for WinJS apps
        document.body.appendChild(outer);

        // Force scrollbars
        var widthNoScroll = outer.offsetWidth;
        outer.style.overflow = 'scroll';

        // Add innerdiv
        var inner = document.createElement('div');
        inner.style.width = '100%';
        outer.appendChild(inner);
        var widthWithScroll = inner.offsetWidth;

        // Remove divs
        outer.parentNode.removeChild(outer);

        return widthNoScroll - widthWithScroll;

    };

    /**
     * Create the modal background and append it to the DOM
     * @private
     */
    var createModalBg = function () {

        // If modal BG already exists, don't create another one
        if ( document.querySelector('[data-modal-bg]') ) return;

        // Define the modal background
        var modalBg = document.createElement('div');
        modalBg.setAttribute('data-modal-bg', true);
        modalBg.classList.add( settings.modalBGClass );

        // Append the modal background to the page
        document.body.appendChild(modalBg);

    };

    /**
     * Remove the modal background from the DOM
     * @private
     */
    var removeModalBg = function () {
        var modalBg = document.querySelector( '[data-modal-bg]' );
        if ( !modalBg ) return;
        document.body.removeChild( modalBg );
    };

    /**
     * Close open modal window
     * @public
     * @param  {Object} options
     * @param  {Event} event
     */
    publicApi.closeModal = function (options) {

        // Selectors and variables
        var localSettings = extend( settings || defaults, options || {} ); // Merge user options with defaults
        var modal = document.querySelector( localSettings.selectorWindow + '.' + localSettings.modalActiveClass ); // Get open modal

        // Sanity check
        if ( !modal ) return;

        // Stop videos from playing
        stopVideos( modal, localSettings );

        // Close the modal
        modal.classList.remove( localSettings.modalActiveClass );

        // Remove the modal background from the DOM
        removeModalBg();

        // Set state to closed
        state = 'closed';

        // Reallow background scrolling
        if ( localSettings.preventBGScroll ) {
            document.documentElement.style.overflowY = '';
            document.body.style.overflowY = '';
            document.body.style.paddingRight = '';
        }

        // Run callbacks after closing a modal
        localSettings.callbackClose( placeholder, modal );

        // Bring focus back to the button that toggles the modal
        if ( placeholder ) {
            placeholder.focus();
            placeholder = null;
        }

    };

    /**
     * Open the target modal window
     * @public
     * @param  {Element} toggle The element that toggled the open modal event
     * @param  {String} modalID ID of the modal to open
     * @param  {Object} options
     * @param  {Event} event
     */
    publicApi.openModal = function (toggle, modalID, options) {

        // Define the modal
        var localSettings = extend( settings || defaults, options || {} );  // Merge user options with defaults
        var modal = document.querySelector(modalID);

        // If a modal is already open, close it first
        if ( state === 'open' ) {
            publicApi.closeModal( localSettings );
        }

        // Save the visitor's spot on the page
        if ( toggle ) {
            placeholder = toggle;
        }

        // Activate the modal
        modal.classList.add( localSettings.modalActiveClass );
        createModalBg();
        state = 'open';

        // Bring modal into focus
        modal.setAttribute( 'tabindex', '-1' );
        modal.focus();

        // Prevent background scrolling
        if ( localSettings.preventBGScroll ) {
            if ( localSettings.preventBGScrollHtml ) {
                document.documentElement.style.overflowY = 'hidden';
            }
            if ( localSettings.preventBGScrollBody ) {
                document.body.style.overflowY = 'hidden';
            }
            document.body.style.paddingRight = scrollbarWidth + 'px';
        }

        localSettings.callbackOpen( toggle, modal ); // Run callbacks after opening a modal

    };

    /**
     * Handle toggle click events
     * @private
     */
    var eventHandler = function (event) {
        var toggle = event.target;
        var open = getClosest(toggle, settings.selectorToggle);
        var close = getClosest(toggle, settings.selectorClose);
        var modal = getClosest(toggle, settings.selectorWindow);
        var key = event.keyCode;

        if ( key && state === 'open' ) {
            if ( key === 27 || ( settings.backspaceClose && ( key === 8 || key === 46 ) ) ) {
                publicApi.closeModal();
            }
        } else if ( toggle ) {
            if ( modal && !close ) {
                return;
            } else if ( open && ( !key || key === 13 ) ) {
                event.preventDefault();
                publicApi.openModal( open, open.getAttribute('data-modal'), settings );
            } else if ( state === 'open' ) {
                event.preventDefault();
                publicApi.closeModal();
            }
        }
    };

    /**
     * Destroy the current initialization.
     * @public
     */
    publicApi.destroy = function () {
        if ( !settings ) return;
        document.removeEventListener('click', eventHandler, false);
        document.removeEventListener('touchstart', eventHandler, false);
        document.removeEventListener('keydown', eventHandler, false);
        document.documentElement.style.overflowY = '';
        document.body.style.overflowY = '';
        document.body.style.paddingRight = '';
        scrollbarWidth = null;
        placeholder = null;
        settings = null;
    };

    /**
     * Initialize Modals
     * @public
     * @param {Object} options User settings
     */
    publicApi.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        publicApi.destroy();

        // Merge user options with defaults
        settings = extend( defaults, options || {} );

        // Get scrollbar width
        scrollbarWidth = getScrollbarWidth();

        // Listen for events
        document.addEventListener('click', eventHandler, false);
        document.addEventListener('touchstart', eventHandler, false);
        document.addEventListener('keydown', eventHandler, false);

    };


    //
    // Public APIs
    //

    return publicApi;

}));