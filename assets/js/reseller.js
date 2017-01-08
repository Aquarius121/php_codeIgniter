// add IE methods to mozilla XML parser functions
if (document.implementation.hasFeature("XPath", "3.0")) {
	XMLDocument.prototype.selectNodes = function(cXPathString, xNode) {
		if (!xNode) { xNode = this; }
		var oNSResolver = this.createNSResolver(this.documentElement)
		var aItems = this.evaluate(cXPathString, xNode, oNSResolver, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null)
		var aResult = [];
		for (var i = 0; i < aItems.snapshotLength; i++) {
			aResult[i] =  aItems.snapshotItem(i);
		}
		return aResult;
	}
	XMLDocument.prototype.selectSingleNode = function(cXPathString, xNode) {
		if (!xNode) { xNode = this; }

		var xItems = this.selectNodes(cXPathString, xNode);
		if (xItems.length > 0) {
			return xItems[0];
		} else {
			return null;
		}
	}

	Element.prototype.selectNodes = function(cXPathString) {
		if (this.ownerDocument.selectNodes) {
			return this.ownerDocument.selectNodes(cXPathString, this);
		} else {
			throw "For XML Elements Only";
		}
	}

	Element.prototype.selectSingleNode = function(cXPathString) {
		if (this.ownerDocument.selectSingleNode) {
			return this.ownerDocument.selectSingleNode(cXPathString, this);
		} else {
			throw "For XML Elements Only";
		}
	}
}

// ------------------------------------------------------------------------------------------------------------------------
// FUNCTIONS HTTP ---------------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------


var httpObj = httpObject();

function httpObject(){
	// code for Mozilla, etc.
	if (window.XMLHttpRequest){
		var httpObj=new XMLHttpRequest();
	}
	// code for IE
	else if (window.ActiveXObject){
		var httpObj=new ActiveXObject("Microsoft.XMLHTTP");
    }
    else {
  		alert('ERROR: Unable to create http object');
  		return false;
  	}
  	return httpObj;
}
// "httpObj", "handler", "returnAsXml" are optional
function httpQuery(url, httpObj, handler, returnAsXml){
	if (httpObj==undefined) httpObj=httpObject();
	var async=(handler)?true:false;
	httpObj.open("POST", url, async);
	if (async) httpObj.onreadystatechange=handler;
	if (window.XMLHttpRequest) httpObj.send(null);
	else if (window.ActiveXObject)	httpObj.send();
	if (!async) {
		if (httpObj.readyState == 4 && httpObj.status == 200) {
			if (returnAsXml) return httpObj.responseXML; else return httpObj.responseText;
		} else
			alert('ERROR: Unable get http response, code: '+httpObj.statusText);
	}
	return false;
}

// "httpObj", "handler" are optional
function httpQueryXml(url, httpObj, handler){
	return httpQuery(url, httpObj, handler, true);
}

// "returnAsXml" is optional
function httpResponse(httpObj, returnAsXml){
	if (httpObj.readyState == 4 && httpObj.status == 200) {
			if (returnAsXml) return httpObj.responseXML; else return httpObj.responseText;
	} else return false;
}

function httpResponseXml(httpObj){
	return httpResponse(httpObj, true);
}

// ------------------------------------------------------------------------------------------------------------------------
// FUNCTIONS XML ----------------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------

function xmlObjectFromString(xmlString){
	// code for IE
	if (window.ActiveXObject) {
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async=false;
		xmlDoc.loadXML(xmlString);
	}
	// code for Mozilla, etc.
	else if (document.implementation && document.implementation.createDocument) {
    	var objDOMParser = new DOMParser();
    	var xmlDoc = objDOMParser.parseFromString(xmlString, "text/xml");
	}
	else {
		alert('ERROR: Unable to create XML document');
		return false;
	}

	return xmlDoc;
}

// ------------------------------------------------------------------------------------------------------------------------
// FUNCTIONS XSLT ---------------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------

function xslObjectFromUrl(url){
	try {
		// IE
		xslDoc = new ActiveXObject("MSXML2.FreeThreadedDOMDocument");
		xslDoc.async = false;
		xslDoc.load(url);
	} catch (e) {
		// Mozilla
		try {
			docRequest = new XMLHttpRequest();
			docRequest.open("GET", url, false);
			docRequest.send(null);
			xslDoc = docRequest.responseXML;
		} catch (e) {
			alert('ERROR: Unable to create XSL document');
			return false;
		}
	}

	return xslDoc;
}

function xslObjectFromXmlObject(){
	if (document.all) {
		var xsl = new ActiveXObject("MSXML2.FreeThreadedDOMDocument");
		xsl.async = false;
		xsl.loadXML(xml.xml);
		return xsl;
	} else {
		return xml;
	}
}

// "parameters" is optional
function xslProcess(xslSource, xmlSource, container, parameters) {
	try  {
		// IE
		var docCache = new ActiveXObject("MSXML2.XSLTemplate");
		docCache.stylesheet = xslSource;
		var docProcessor = docCache.createProcessor();
		docProcessor.input = xmlSource;
		if (parameters) for (var col in parameters) docProcessor.addParameter(col, parameters[col], '');
		docProcessor.transform();
		container.innerHTML = docProcessor.output;
	} catch (e) {
		// Mozilla
		try {
			var xslt_processor = new XSLTProcessor();
			xslt_processor.importStylesheet(xslSource);
			if (parameters) for (var col in parameters) xslt_processor.setParameter(null, col, parameters[col]);
			var result = xslt_processor.transformToFragment(xmlSource, document);
			container.innerHTML="";
			container.appendChild(result);
		} catch (e) {
			alert("ERROR: unable to perform XSLT processing");
		}
	}
}

// ----------------------------------------
// ---------- Other functions -------------
// ----------------------------------------

$(function() {
	
	var prevent_default = function(ev) {
		ev.preventDefault();
		return false;
	};
	
	// tooltips
	$(".tl").tooltip().each(function() {
		_this = $(this);
		if (_this.attr("href") === "#")
			_this.on("click", prevent_default);
	});
		
});
