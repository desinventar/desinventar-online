#!/usr/bin/perl
#
# Test Authentication Mechanism for DICORE
# (c) 2007 Corporacion OSSO 
# 2007-03-29 Jhon H. Caicedo <jhcaiced@desinventar.org>
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
$sRegionUUID = 'COLOMBIA';
$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));
$ht = $client->call('RpcDIServer.readDIObject', ($sSessionUUID, 1, 'EARTHQUAKE'));
print Dumper($ht);
#$ht2 = $client->call('RpcDIServer.saveDIObject', ($sSessionUUID, 1, 2, $ht));
#print Dumper($ht2);

#$ht = $client->call('RpcUserOperations.getAllPermsByUser', ($sSessionUUID));
#$ht = $client->call('RpcUserOperations.getAllPermsByRegion', ($sSessionUUID));
#$ht = $client->call('RpcUserOperations.getUserRole', ('demo', 'COLOMBIA'));
#$ht = $client->call('RpcUserOperations.getUserRoleByRegion', ($sSessionUUID, 'PERU'));
#$ht = $client->call('RpcUserOperations.setUserRole', ($sSessionUUID, 'jhcaiced', 'COLOMBIA','ADMINREGION'));
#print Dumper($ht);
#$ht = $client->call('RpcUserOperations.getPerm', ($sSessionUUID, 'DISASTER_INSERT'));
#print Dumper($ht);
$client->call('RpcRegionOperations.closeRegion', ($sSessionUUID, $sRegionUUID));
$client->call('RpcUserOperations.closeUserSession', ($sSessionUUID));
