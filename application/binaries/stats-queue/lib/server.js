var constantFile = 'constants.js';
if (process.argv[2] !== undefined && process.argv[2].length)
	constantFile = process.argv[2];

var Constants = require('../' + constantFile);
var m_fs      = require('fs');
var m_http    = require('http');
var m_sys     = require('sys');
var m_url     = require('url');
var Router    = require('router');

var PAYLOAD_GIF = m_fs.readFileSync('./static/payload.gif');
var PAYLOAD_JS  = m_fs.readFileSync('./static/payload.js');

var preventPayloadCache = function(req, res) {
	// ensure the payload is not cached in any browser/proxy
	res.setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
	res.setHeader('Pragma', 'no-cache');
	res.setHeader('Expires', '0');
};

var deliverPayloadJS = function(req, res) {
	preventPayloadCache(req, res);
	res.setHeader('Content-Type', 'text/javascript');
	res.writeHead(200);
	res.write(PAYLOAD_JS);
	res.end();
};

var deliverPayloadGIF = function(req, res) {
	preventPayloadCache(req, res);
	res.setHeader('Content-Type', 'image/gif');
	res.writeHead(200);
	res.write(PAYLOAD_GIF);
	res.end();
};

var deliverPayload = function(req, res) {
	if (req.params.payload === 'js') 
		return deliverPayloadJS(req, res);
	if (req.params.payload === 'im') 
		return deliverPayloadGIF(req, res);
	if (req.params.payload === undefined)
		return deliverPayloadJS(req, res);
	res.writeHead(404);
	res.end();
};

var Server = function(port) {

	var router = new Router();
	var server = this;

	// handler for testing if up
	router.get('/echo', function(req, res) {
		res.end('/echo');
	});

	// handler for hit events
	router.get('/:type/:payload?', function(req, res) {

		// protect against memory fill DoS
		if (req.url.length > 4096) return res.end();
		var query = m_url.parse(req.url, true).query;

		// explode compact params
		for (var ix in query) {
			var value = query[ix];
			if (ix.indexOf(',') !== -1) {
				delete query[ix];
				var indexes = ix.split(',');
				indexes.forEach(function(ix2) {
					query[ix2] = value;
				});
			}
		}

		var requestData = new Object();
		requestData.type = req.params.type;
		requestData.data = query;
		requestData.data.addr = req.headers[Constants.REMOTE_ADDR_HEADER] || null;
		requestData.data.referer = req.headers['referer'] || null;
		requestData.data.ts = (new Date()).toISOString();
		if (typeof server.onRequestCallback === 'function')
			server.onRequestCallback(requestData);
		deliverPayload(req, res);

	});

	var httpServer = m_http.createServer(function(req, res) {
		router(req, res, function(err) {
			if (err) m_sys.puts(err.stack);
			res.writeHead(404);
			res.end();
		});
	});

	httpServer.listen(port);

};

Server.prototype.onRequest = function(callback) {
	this.onRequestCallback = callback;
};

module.exports = Server;