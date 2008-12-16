#!/usr/bin/perl
#
# Control Script for DICORE Server
# (c) 2007 Corporacion OSSO 
# 2007-03-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Getopt::Long;

my $sURL   = "http://localhost:8081";

my $client = Frontier::Client->new(url => $sURL, debug => 0);

$sSessionUUID = $client->call('RpcUserOperations.openUserSession', ('root','root'));
$sRegionUUID = 'BOLIVIA2';
print "Session : " . $sSessionUUID . "\n";
$r = $client->call('RpcRegionOperations.dropRegion', ($sSessionUUID,$sRegionUUID));
#$r = $client->call('RpcRegionOperations.createRegion', ($sSessionUUID,
#	 {'RegionUUID'     => $sRegionUUID,
#	  'RegionLabel'    => 'Demo Region',
#	  'CountryIsoCode' => 'BOL',
#	 }
#	) );
print "Result : $r\n";
#$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));
#$sInfo = $client->call('RpcRegionOperations.getRegionInformation', ($sSessionUUID));
#print "getRegionOperation : " . length($sInfo) . " bytes\n";
#$client->call('RpcRegionOperations.closeRegion', ($sSessionUUID, $sRegionUUID));
$client->call('RpcUserOperations.closeUserSession', ($sSessionUUID));
