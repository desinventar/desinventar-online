#!/usr/bin/perl
#
# Test Permissions Mechanism for DICORE
# (c) 2007 Corporacion OSSO 
# 2007-05-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Getopt::Long;
use Data::Dumper;

my $CmdNew    = 1;
my $CmdUpdate = 2;
my $CmdDelete = 3;

my $ObjEvent     = 1;
my $ObjCause     = 2;
my $ObjGeoLevel  = 3;
my $ObjGeography = 4;
my $ObjDisaster  = 5;

my $sURL   = "http://localhost:8080";
my $client = Frontier::Client->new(url => $sURL, debug => 0);
$sSessionUUID = $client->call('RpcUserOperations.openUserSession', ('demo','demo'));
print "Session : " . $sSessionUUID . "\n";
$sRegionUUID = 'COLOMBIA';
$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));
$ht = $client->call('RpcDIServer.readDIObject', ($sSessionUUID, $ObjEvent, 'EARTHQUAKE'));
$ht = $client->call('RpcDIServer.saveDIObject', ($sSessionUUID, $ObjEvent, $CmdDelete, $ht));
print Dumper($ht);

#$ht = $client->call('RpcDIServer.readDIObject', ($sSessionUUID, 1, 'SISMO2'));
#print Dumper($ht);
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
