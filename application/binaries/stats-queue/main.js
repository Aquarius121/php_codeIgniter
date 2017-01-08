var constantFile = 'constants.js';
if (process.argv[2] !== undefined && process.argv[2].length)
	constantFile = process.argv[2];

var Constants   = require('./' + constantFile);
var Dispatcher  = require('./lib/dispatcher.js');
var Queue       = require('./lib/queue.js');
var Server      = require('./lib/server.js');
var debugInfo   = require('debug')('main:info');
var debugTrace  = require('debug')('main:trace');
var m_readline  = require('readline');

var activationQueue = new Queue(Constants.QUEUE_SIZE_ACTIVATE);
var hitQueue        = new Queue(Constants.QUEUE_SIZE_HIT);
var server          = new Server(Constants.LISTEN_PORT);
var dispatcher      = new Dispatcher();

server.onRequest(function(request) {
	debugTrace('server.onRequest: %s', request.data.addr);
	if (request.type === Constants.TYPE_ACTIVATE)
		return activationQueue.add(request.data);
	if (request.type === Constants.TYPE_HIT)
		return hitQueue.add(request.data);
});

activationQueue.onProcess(function(queueData) {
	debugInfo('activationQueue.onProcess: %d', queueData.length);
	dispatcher.dispatch(Constants.TYPE_ACTIVATE, queueData);
});

hitQueue.onProcess(function(queueData) {
	debugInfo('hitQueue.onProcess: %d', queueData.length);
	dispatcher.dispatch(Constants.TYPE_HIT, queueData);
});

var processQueues = function() {
	activationQueue.processQueue();
	hitQueue.processQueue();
};

// process queues (at least) 
// every 15 minutes
setInterval(processQueues, 
	Constants.PROCESS_INTERVAL);

var cio = m_readline.createInterface({
  input: process.stdin,
  output: process.stdout,
});

cio.on('line', function () {
	m_readline.moveCursor(process.stdout, 0, -1);
	setTimeout(processQueues, 0);
	debugInfo('processQueues');
});

debugInfo('started');
