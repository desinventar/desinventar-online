#!/usr/bin/perl
#
# Test Read/Save DIObjects
# (c) 2007-2008 Corporacion OSSO 
# 2008-09-05 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Getopt::Long;
use Data::Dumper;

use constant { DI_EVENT     =>  1,
               DI_CAUSE     =>  2,
               DI_GEOLEVEL  =>  3,
               DI_GEOGRAPHY =>  4,
               DI_DISASTER  =>  5,
               DI_DBINFO    =>  6,
               DI_DBLOG     =>  7,
               DI_USER      =>  8,
               DI_REGION    =>  9,
               DI_EEFIELD   => 10,               
               DI_EEDATA    => 11
             };
use constant { CMD_NEW    => 1,
               CMD_UPDATE => 2,
               CMD_DELETE => 3
             };

my $sURL   = "http://localhost:8081";
my $client = Frontier::Client->new(url => $sURL, debug => 0);
$sSessionUUID = $client->call('RpcUserOperations.openUserSession', ('root','97ossonp'));
print "Session : " . $sSessionUUID . "\n";
$sRegionUUID = 'COLOMBIA';
$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));

# 2008-09-10 Test Disaster + EEData
# Test New/Save Object
# Commands : NEW=1, UPDATE=2, DELETE=3
# Objects  : EVENT=1 DISASTER=5

# Save DI_DISASTER Test
#my $data = { 'DisasterId' => 'DEMO', 'EventId' => 'EARTHQUAKE'};
#$ht = $client->call('RpcDIServer.saveDIObject', ($sSessionUUID, DI_DISASTER, CMD_NEW, $data));
#print Dumper($ht);

# Save DI_EEDATA Test
#$data = { 'DisasterId' => '6418c42a-6915-48e8-a0c6-b34c0ac2a74a',
#          'EEF000'     => 'Valor Demo',
#          'EEF001'     => 15
#        };
#$ht = $client->call('RpcDIServer.saveDIObject', ($sSessionUUID, 11, CMD_UPDATE, $data));
#print Dumper($ht);

#$data = { 'DisasterId' => '6418c42a-6915-48e8-a0c6-b34c0ac2a74a' };
#$ht = $client->call('RpcDIServer.saveDIObject', ($sSessionUUID, 5, CMD_DELETE, $data));
#print Dumper($ht);

# Test Read
#$ht = $client->call('RpcDIServer.readDIObject', ($sSessionUUID, 1, 'EARTHQUAKE'));
#print Dumper($ht);


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
