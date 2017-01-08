var constantFile = 'constants.js';
if (process.argv[2] !== undefined && process.argv[2].length)
	constantFile = process.argv[2];

var Constants = require('../' + constantFile);
var debugInfo  = require('debug')('dispatcher:info');
var debugTrace = require('debug')('dispatcher:trace');
var sprintf    = require('sprintf-js').sprintf;
var m_fs       = require('fs');
var m_iella    = require('iella');

var Dispatcher = function() {
	var iellaSecret = m_fs.readFileSync(Constants.IELLA_SECRET_FILE);
	this.iellaClient = new m_iella.Client();
	this.iellaClient.setHost(Constants.IELLA_HOST);
	this.iellaClient.setSecret(iellaSecret);
};

Dispatcher.prototype.dispatch = function(type, data) {
	var method = sprintf('stats/commit/%s', type);
	var request = this.iellaClient.createRequest(method);
	request.data = data;
	request.send(function(res, err, raw) {
		if (err) return console.error(err);
		if (!res) return console.error(raw.body);
		if (res.exception) return console.error(res.exception);
		debugInfo('dispatched: %d', res.count);
	});
};

module.exports = Dispatcher;