#!/bin/bash

# $1 = contact id 
# $2 = filename

# username: anthony.santiago@gmail.com
# password: mymediainfo

cd $(dirname $0)

curl "https://www.mymediainfo.com/mymediainfo/contact/drilldown.jsp?h=w*&c=${1}&fromResult=true&updatedcontact=false" \
	 -H "Host: www.mymediainfo.com" \
	 -H "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0" \
	 -H "Accept: */*" \
	 -H "Accept-Language: en-US,en;q=0.5" \
	 -H "DNT: 1" \
	 -H "X-Requested-With: XMLHttpRequest" \
	 -H "Referer: https://www.mymediainfo.com/mymediainfo/search/newsearch.jsp" \
	 -H "Cookie: csrf=QHMCPsC3fLQYBnt56GXiHV8BgxzNU2YBTBiNpvClFVrwvElkoSPzegM46cK97c1"%"2BnmqEqFYZcs4"%"3D; visid_incap_241535=whmoXUV+SRiX0Uv5cDy9NW795lYAAAAAQUIPAAAAAABAv5JGTlRq0oS0ugZwaky7; JSESSIONID=A314B50C471341EE49B07B0C4D906881; nlbi_241535=F0LOP1fXryqyML1i/KwiIAAAAAACvqNeBvrKN7w7kZ6XfXGG; incap_ses_198_241535=lewZXNxl0Ui0Z5XgCnG/Am9FCFcAAAAAbw5HzH/eswHIKDZdgt+Drw==" \
	 -H "Connection: keep-alive" \
	 -H "Pragma: no-cache" \
	 -H "Cache-Control: no-cache" \
	 -H "Content-Length: 0" \
	 > $2
