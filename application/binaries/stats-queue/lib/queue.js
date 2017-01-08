var Queue = function(maxSize) {
	if (maxSize === undefined)
		maxSize = 1024;
	this.maxSize = maxSize;
	this.dataQueue = [];
};

Queue.prototype.add = function(data) {
	this.dataQueue.push(data);
	if (this.dataQueue.length === this.maxSize) 
		this.processQueue();
};

Queue.prototype.processQueue = function() {
	if (!this.dataQueue.length) return;
	var dataQueueCopy = this.dataQueue;
	this.dataQueue = [];
	if (typeof this.onProcessCallback === 'function')
		this.onProcessCallback(dataQueueCopy);
};

Queue.prototype.onProcess = function(callback) {
	this.onProcessCallback = callback;
};

module.exports = Queue;