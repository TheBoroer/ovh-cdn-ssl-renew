<?php
  /**
   * OVH api credentials
   */
$ovhConfig = array(
		   'applicationKey' => 'YourApplicationKey', //  $applicationKey
		   'applicationSecret' => 'YourApplicationSecret',  //$applicationSecret,
		   'endpoint' => 'YourEndpoint', //$endpoint,
		   'consumer_key' => 'YourConsumerKey' //$consumer_key
		   );


// if we call this script, this is because we have to update the cert, so update the cert
$services['cdn'] = array(
			 array( // CDN1
			       'name' => 'cdn-AAA.BBB.CCC.DDD-EEE', // your CDN1's name
			       'certDomain' => 'your.domain.tld' // the main letsencrypt domain for the cert(s)
			       ),
/* you can put more than one service
       array( // CDN2
			       'name' => 'cdn-FFF.GGG.HHH.JJJ-KKK', // your CDN2's name
			       'certDomain' => 'your.second-domain.tld' // the main letsencrypt domain for the cert(s)
			       ),
             ...
*/
			 );
// if you letsencrypt directory is not the default, please write the path to the 'live' directory
//$leLive = '/where/is/letsencrypt/live/';
