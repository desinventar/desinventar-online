#!/usr/bin/perl
#
# Control Script for DICORE Server
# (c) 2006-2008 Corporacion OSSO 
# 2008-08-20 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Getopt::Long;
use Data::Dumper;

my $sURL   = "http://localhost:8081";
my $client = Frontier::Client->new(url => $sURL, debug => 0);

$sSessionUUID = $client->call('RpcUserOperations.openUserSession', ('root','97ossonp'));
print "Session : " . $sSessionUUID . "\n";
$sRegionUUID = 'BOLIVIA';
$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));
$sInfo = $client->call('RpcRegionOperations.acquireDatacardLock', ($sSessionUUID, '0073ce05-152f-4a1f-ba5f-9694f1be2063'));
print Dumper($sInfo);
sleep(1);
$sInfo = $client->call('RpcRegionOperations.isDatacardLocked', ($sSessionUUID, '0073ce05-152f-4a1f-ba5f-9694f1be2063'));
print Dumper($sInfo);
sleep(2);
$sInfo = $client->call('RpcRegionOperations.acquireDatacardLock', ($sSessionUUID . "X", '0073ce05-152f-4a1f-ba5f-9694f1be2063'));
print Dumper($sInfo);
sleep(2);
$sInfo = $client->call('RpcRegionOperations.releaseDatacardLock', ($sSessionUUID, '0073ce05-152f-4a1f-ba5f-9694f1be2063'));
print Dumper($sInfo);
$client->call('RpcRegionOperations.closeRegion', ($sSessionUUID, $sRegionUUID));
sleep(2);
$client->call('RpcUserOperations.closeUserSession', ($sSessionUUID));
#