#!/bin/bash

# $1 = filename
# $2 = query 
# $3 = argument string

# username: anthony.santiago@gmail.com
# password: mymediainfo

cd $(dirname $0)

rm "${1}" &> /dev/null
curl "https://www.mymediainfo.com/mymediainfo/search/searchresult.jsp?sessionstoredid=true&advautosave=" -s \
	 -o "${1}" \
	 -H "Accept: */*" \
	 -H "Accept-Language: en-GB,en;q=0.5" \
	 -H "Cache-Control: no-cache" \
	 -H "Connection: keep-alive" \
	 -H "Content-Type: application/x-www-form-urlencoded; charset=UTF-8" \
	 -H "Cookie: csrf=8to2T9Fje5EF4h2n9HtTrnM70ul9tcfkIXgpIQv3H"%"2FDm9i0ckZhwOj0boqQPEUMJTPTpzA"%"2BSdGE"%"3D; visid_incap_241535=whmoXUV+SRiX0Uv5cDy9NW795lYAAAAAQUIPAAAAAABAv5JGTlRq0oS0ugZwaky7; nlbi_241535=H4QlMz9SGSqTD9PMptZ+kwAAAACfxYQ1DjObZMaKOeKDdxtV; incap_ses_260_241535=jhTCBiJ01BeDsyXnKLabA2795lYAAAAANCk3aAwCyFCcwtvi0E1mRw==; JSESSIONID=09CF3200C8698013BC9612F49BA0EB95" \
	 -H "DNT: 1" \
	 -H "Host: www.mymediainfo.com" \
	 -H "Pragma: no-cache" \
	 -H "https://www.mymediainfo.com/mymediainfo/search/newsearch.jsp?qs=${2}&curtab=0&fromoverview=1" \
	 -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0" \
	 -H "X-Requested-With: XMLHttpRequest" \
	 --data "qs=${2}&resultsPerPage=5000&nav=&page_id=1&storeSession=true&isKeyword=true&${3}"
