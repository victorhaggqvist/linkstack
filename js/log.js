/**
 * @author Victor Häggqvist
 * @since 3/1/16
 */

export var log = require('loglevel');

if (BUILD_DEV) log.setDefaultLevel(log.levels.TRACE);
else  log.setDefaultLevel(log.levels.SILENT);
