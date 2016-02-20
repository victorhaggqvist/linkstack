/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Created by victor on 7/20/15.
	 */

	var log = __webpack_require__(1);

	if (false) log.setDefaultLevel(log.levels.TRACE);
	else  log.setDefaultLevel(log.levels.SILENT);

	var url = document.querySelector('#form_url');
	var title = document.querySelector('#form_title');
	var tags = document.querySelector('#form_tags');
	var save = document.querySelector('#form_save');
	var table = document.querySelector('#recentlist tbody');
	var status = document.querySelector('#status');
	var statusMsg = document.querySelector('#statusMsg');


	const METASERVICE = 'https://tool.stack.snilius.com';
	var fetchmutex = false;

	var loadinfo = () => {
	    if (fetchmutex) return;

	    if (url.value.length < 5) return;

	    if (!(title.value.length < 1 || tags.value.length < 1)) return;

	    fetchmutex = true;
	    var apiUrl = METASERVICE + '/info?url=' + url.value;
	    fetch(apiUrl)
	        .then(resp => resp.json(),
	            err => {
	                window.err = err;
	                fetchmutex = false;
	                log.debug('meta fetch fail');
	                log.debug(err);
	            })
	        .then(ret => {
	            if (ret === undefined) return;
	            fetchmutex = false;

	            if (title.value.length < 1) title.value = ret.title;

	            if (tags.value.length < 1) tags.value = ret.meta;

	            if (url.value !== ret.longurl) url.value = ret.longurl;
	        });
	};

	var putRecent = function(url, title, tags, id) {
	    const date = new Date();
	    var y = date.getFullYear();
	    var m = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
	    var day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
	    var hours = (date.getHours() < 10) ? '0' + date.getHours() : date.getHours();
	    var mins = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
	    var timestamp = y + '-' + m + '-' + day + ' ' + hours + ':' + mins;

	    var titleTags = tags.length > 25 ? 'title="' + tags.substr(0, 25) + '"' : '';
	    var displayTags = tags.length > 25 ? tags.substr(0, 25) + '...' : tags;
	    var titleTitle = title.length > 50 ? 'title="' + title.substr(0, 50) + '"' : '';
	    var displayTitle = title.length > 50 ? title.substr(0, 50) + '...' : title;

	    const item = document.createElement('tr');
	    item.innerHTML = '<td><a href="/stack/'+id+'">'+id+'</a></td>'+
	        '<td><img src="https://www.google.com/s2/favicons?domain=' + url + '" alt="fav">' +
	        '<a class="title" href="' + url + '">' + url + '</a></td>' +
	        '<td' + titleTitle + '>' + displayTitle + '</td>' +
	        '<td' + titleTags + '>' + displayTags + '</td>' +
	        '<td>' + timestamp + '</td>' +
	        '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn icon-button" data-id="' + id + '"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>';

	    table.insertBefore(item, table.firstChild);

	    bindDeletes();
	};

	save.onclick = event => {
	    event.preventDefault();

	    const body = {
	        title: title.value,
	        url: url.value,
	        tags: tags.value
	    };
	    save.innerHTML = 'Pushing...';
	    save.disabled = 'disabled';
	    fetch('/api/items', {
	        method: 'post',
	        body: JSON.stringify(body),
	        credentials: 'include'
	    })
	        .then(resp => resp.json())
	        .then(json => {
	            if ('message' in json) {
	                statusMsg.innerHTML = json.message;
	                status.style.display = 'block';
	                save.disabled = null;
	                save.innerHTML = 'Push';
	            } else {
	                log.info(json);
	                putRecent(body.url, body.title, body.tags, json.id);
	                url.value = '';
	                title.value = '';
	                tags.value = '';
	                save.disabled = null;
	                save.innerHTML = 'Push';
	            }
	        },err => log.error(err));
	};

	log.info('set hooks');
	url.onkeyup = loadinfo;
	url.onmouseout = loadinfo;
	url.onchange = loadinfo;

	const bindDeletes = () => {
	    var buttons = document.querySelectorAll('.delete-btn');
	    log.info('rebind deletes');
	    Array.prototype.forEach.call(buttons, element => {
	        element.onclick = () => {
	            log.debug(element);
	            var tempItem = element;
	            var id = element.getAttribute('data-id');

	            tempItem.classList.add('spinner');
	            fetch('/api/items/' + id, {
	                method: 'delete',
	                credentials: 'include'
	            }).then(resp => {
	                if (resp.status === 204) tempItem.parentNode.parentNode.remove();

	                log.info(resp);
	            }, err => log.error(err));
	        };
	    });
	};

	bindDeletes();


/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*
	* loglevel - https://github.com/pimterry/loglevel
	*
	* Copyright (c) 2013 Tim Perry
	* Licensed under the MIT license.
	*/
	(function (root, definition) {
	    "use strict";
	    if (typeof module === 'object' && module.exports && "function" === 'function') {
	        module.exports = definition();
	    } else if (true) {
	        !(__WEBPACK_AMD_DEFINE_FACTORY__ = (definition), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	    } else {
	        root.log = definition();
	    }
	}(this, function () {
	    "use strict";
	    var noop = function() {};
	    var undefinedType = "undefined";

	    function realMethod(methodName) {
	        if (typeof console === undefinedType) {
	            return false; // We can't build a real method without a console to log to
	        } else if (console[methodName] !== undefined) {
	            return bindMethod(console, methodName);
	        } else if (console.log !== undefined) {
	            return bindMethod(console, 'log');
	        } else {
	            return noop;
	        }
	    }

	    function bindMethod(obj, methodName) {
	        var method = obj[methodName];
	        if (typeof method.bind === 'function') {
	            return method.bind(obj);
	        } else {
	            try {
	                return Function.prototype.bind.call(method, obj);
	            } catch (e) {
	                // Missing bind shim or IE8 + Modernizr, fallback to wrapping
	                return function() {
	                    return Function.prototype.apply.apply(method, [obj, arguments]);
	                };
	            }
	        }
	    }

	    // these private functions always need `this` to be set properly

	    function enableLoggingWhenConsoleArrives(methodName, level, loggerName) {
	        return function () {
	            if (typeof console !== undefinedType) {
	                replaceLoggingMethods.call(this, level, loggerName);
	                this[methodName].apply(this, arguments);
	            }
	        };
	    }

	    function replaceLoggingMethods(level, loggerName) {
	        /*jshint validthis:true */
	        for (var i = 0; i < logMethods.length; i++) {
	            var methodName = logMethods[i];
	            this[methodName] = (i < level) ?
	                noop :
	                this.methodFactory(methodName, level, loggerName);
	        }
	    }

	    function defaultMethodFactory(methodName, level, loggerName) {
	        /*jshint validthis:true */
	        return realMethod(methodName) ||
	               enableLoggingWhenConsoleArrives.apply(this, arguments);
	    }

	    var logMethods = [
	        "trace",
	        "debug",
	        "info",
	        "warn",
	        "error"
	    ];

	    function Logger(name, defaultLevel, factory) {
	      var self = this;
	      var currentLevel;
	      var storageKey = "loglevel";
	      if (name) {
	        storageKey += ":" + name;
	      }

	      function persistLevelIfPossible(levelNum) {
	          var levelName = (logMethods[levelNum] || 'silent').toUpperCase();

	          // Use localStorage if available
	          try {
	              window.localStorage[storageKey] = levelName;
	              return;
	          } catch (ignore) {}

	          // Use session cookie as fallback
	          try {
	              window.document.cookie =
	                encodeURIComponent(storageKey) + "=" + levelName + ";";
	          } catch (ignore) {}
	      }

	      function getPersistedLevel() {
	          var storedLevel;

	          try {
	              storedLevel = window.localStorage[storageKey];
	          } catch (ignore) {}

	          if (typeof storedLevel === undefinedType) {
	              try {
	                  var cookie = window.document.cookie;
	                  var location = cookie.indexOf(
	                      encodeURIComponent(storageKey) + "=");
	                  if (location) {
	                      storedLevel = /^([^;]+)/.exec(cookie.slice(location))[1];
	                  }
	              } catch (ignore) {}
	          }

	          // If the stored level is not valid, treat it as if nothing was stored.
	          if (self.levels[storedLevel] === undefined) {
	              storedLevel = undefined;
	          }

	          return storedLevel;
	      }

	      /*
	       *
	       * Public API
	       *
	       */

	      self.levels = { "TRACE": 0, "DEBUG": 1, "INFO": 2, "WARN": 3,
	          "ERROR": 4, "SILENT": 5};

	      self.methodFactory = factory || defaultMethodFactory;

	      self.getLevel = function () {
	          return currentLevel;
	      };

	      self.setLevel = function (level, persist) {
	          if (typeof level === "string" && self.levels[level.toUpperCase()] !== undefined) {
	              level = self.levels[level.toUpperCase()];
	          }
	          if (typeof level === "number" && level >= 0 && level <= self.levels.SILENT) {
	              currentLevel = level;
	              if (persist !== false) {  // defaults to true
	                  persistLevelIfPossible(level);
	              }
	              replaceLoggingMethods.call(self, level, name);
	              if (typeof console === undefinedType && level < self.levels.SILENT) {
	                  return "No console available for logging";
	              }
	          } else {
	              throw "log.setLevel() called with invalid level: " + level;
	          }
	      };

	      self.setDefaultLevel = function (level) {
	          if (!getPersistedLevel()) {
	              self.setLevel(level, false);
	          }
	      };

	      self.enableAll = function(persist) {
	          self.setLevel(self.levels.TRACE, persist);
	      };

	      self.disableAll = function(persist) {
	          self.setLevel(self.levels.SILENT, persist);
	      };

	      // Initialize with the right level
	      var initialLevel = getPersistedLevel();
	      if (initialLevel == null) {
	          initialLevel = defaultLevel == null ? "WARN" : defaultLevel;
	      }
	      self.setLevel(initialLevel, false);
	    }

	    /*
	     *
	     * Package-level API
	     *
	     */

	    var defaultLogger = new Logger();

	    var _loggersByName = {};
	    defaultLogger.getLogger = function getLogger(name) {
	        if (typeof name !== "string" || name === "") {
	          throw new TypeError("You must supply a name when creating a logger.");
	        }

	        var logger = _loggersByName[name];
	        if (!logger) {
	          logger = _loggersByName[name] = new Logger(
	            name, defaultLogger.getLevel(), defaultLogger.methodFactory);
	        }
	        return logger;
	    };

	    // Grab the current global log variable in case of overwrite
	    var _log = (typeof window !== undefinedType) ? window.log : undefined;
	    defaultLogger.noConflict = function() {
	        if (typeof window !== undefinedType &&
	               window.log === defaultLogger) {
	            window.log = _log;
	        }

	        return defaultLogger;
	    };

	    return defaultLogger;
	}));


/***/ }
/******/ ]);