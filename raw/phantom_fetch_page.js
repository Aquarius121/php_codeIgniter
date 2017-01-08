var fs = require('fs');
var page = require('webpage').create(),
  system = require('system'),
  t, address;
page.settings.userAgent = 'WebKit/534.46 Mobile/9A405 Safari/7534.48.3';  

url = system.args[1];

page.onResourceRequested = function(requestData, request) {
    if ((/https:\/\/.+?\.css$/gi).test(requestData['url']) || 
        (/https:\/\/.+?\.png$/gi).test(requestData['url']) ||
        (/https:\/\/.+?\.rss$/gi).test(requestData['url']) || 
        (/https:\/\/.+?\.jpg$/gi).test(requestData['url']) ||
        (/https:\/\/.+?\.ico$/gi).test(requestData['url'])) 
      
    {
        console.log('Skipping', requestData['url']);
        request.abort();
    } 
 
};


page.open(url, function(status) { 
  
    var title = page.evaluate(function() {
      return document.body.innerHTML;

      //return document.title;
    })
    console.log('TexT:' + title);
    alert('Page title is ' + title);
    phantom.exit();
});
