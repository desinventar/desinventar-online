#!/usr/bin/perl
#
# Test Import of Data
# DesInventar
# (c) 1999-2008 Corporacion OSSO 
# 2008-08-26 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Data::Dumper;
use Getopt::Long;

my $sURL   = "http://localhost:8081";
my $client = Frontier::Client->new(url => $sURL, debug => 0);

$sSessionUUID = $client->call('RpcUserOperations.openUserSession', ('root','97ossonp'));
$sRegionUUID = 'IRAN';
$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));
# Event=1,Cause=2,GeoLevel=3,Geography=4,Disaster=5
$r = $client->call('RpcRegionOperations.importFromCSV', ($sSessionUUID, "/tmp/ir_event.csv", 1));
#$r = $client->call('RpcRegionOperations.importFromCSV', ($sSessionUUID, "/tmp/ir_disaster.csv", 5));
print Dumper($r);
$client->call('RpcRegionOperations.closeRegion', ($sSessionUUID, $sRegionUUID));
$client->call('RpcUserOperations.closeUserSession', ($sSessionUUID));
