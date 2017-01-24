#!/usr/bin/env php
<?php
/**
 * Informations can be fond on github
 * https://github.com/jaysee/ovh-cdn-ssl-renew
 */

require 'conf.php';
// OVH php API: https://github.com/ovh/php-ovh
require __DIR__ . '/vendor/autoload.php';
use \Ovh\Api;

$ovh = new Api( $ovhConfig['applicationKey'], $ovhConfig['applicationSecret'], $ovhConfig['endpoint'], $ovhConfig['consumer_key'] );

foreach ( $services['cdn'] as $cdn ) {
  $certBaseDir = ( isset( $leLive ) ? $leLive : '/etc/letsencrypt/live' ) . $cdn['certDomain'];
  if ( !is_dir( $certBaseDir ) ) {
    echo "[CDN-CERT] Error, certDomain directory not found! Domain=" . $cdn['certDomain'] . " certBaseDir=$certBaseDir\n";
    exit( 1 );
  }

  $cert = file_get_contents( $certBaseDir . '/cert.pem' );
  $chain = file_get_contents( $certBaseDir . '/fullchain.pem' );
  $key = file_get_contents( $certBaseDir . '/privkey.pem' );

  $datas = array(
		 'certificate' => $cert,
		 'chain' => $chain,
		 'key' => $key
		 );
  try {
    $service = $ovh->get( sprintf( '/cdn/dedicated/%s/ssl/', $cdn['name'] ) );

    // read our current cert info
    $my_cert = openssl_x509_parse( $cert );
    //echo "myCert ValideFrom=". date( 'r', $my_cert['validFrom_time_t'] ) . "\n";
    //echo "myCert ValideTo=". date( 'r', $my_cert['validTo_time_t'] ) . "\n";

    // compare to check if we need to update datas
    if ( strtotime( $service['certificateValidFrom'] ) != $my_cert['validFrom_time_t'] ) {
      echo "[CDN-CERT] Will update CDN's certs as ValideFrom dates differs\n";
    } else if ( strtotime( $service['certificateValidFrom'] ) != $my_cert['validFrom_time_t'] ) {
      echo "[CDN-CERT] Will update CDN's certs as ValideTo dates differs\n";
    } else {
      //echo "No need to renew\n";
      exit( 0 );
    }

    if ( $service['status'] != "on" ) {
      echo "[CDN-CERT] The current cert status is '".$service['status']."', need to wait for 'on' status before uploading, please retry later...\n";
      // here, we could sleep some time, and call again the script to update the cert
      // but as we call this script daily, this will update tomorrow :)
      exit( 2 );
    }

    $endpoint = '/cdn/dedicated/%s/ssl/update';

  } catch ( GuzzleHttp\Exception\ClientException $e ) {
    if ( $e->getResponse()->getStatusCode() != 404 ) {
      echo "Failed to update CDN certs, err=".$e;
      exit(1);
    }
    // 404 means no cert, means create
    $endpoint = '/cdn/dedicated/%s/ssl';
    $datas[ 'name' ] = 'LetsencryptCert';
  }

  echo "Will post datas on $endpoint\n";
  try {
    $req = $ovh->post( sprintf( $endpoint, $cdn['name'] ), (object) $datas );
  } catch( GuzzleHttp\Exception\ClientException $e ) {
    echo "Got an response error doing $endpoint\n" . $e . "\n";
    exit( 1 );
  }

  echo "Done, operation status is: " . $req['status']. "\n";
}
