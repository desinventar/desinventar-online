#!/usr/bin/perl
# DesInventar
# (c) 1999-2008 Corporacion OSSO 
# Test Script for EEField Operations
# 2008-02-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

use Frontier::Client;
use Frontier::RPC2;
use Getopt::Long;
use Data::Dumper;

use constant DI_EEFIELD => 10;
use constant { CMD_NEW    => 1,
               CMD_UPDATE => 2,
               CMD_DELETE => 3
             };

my $sURL   = "http://localhost:8081";

my $client = Frontier::Client->new(url => $sURL, debug => 0);
$sSessionUUID = $client->call('RpcUserOperations.openUserSession', ('root','97ossonp'));
$sRegionUUID = 'COLOMBIA';
$client->call('RpcRegionOperations.openRegion', ($sSessionUUID, $sRegionUUID));
print "Session : " . $sSessionUUID . "\n";
$r = $client->call('RpcDIServer.saveDIObject', 
       ($sSessionUUID, DI_EEFIELD, CMD_NEW,
	     {'EEFieldId'      => 'DEMOFLOAT',
	      'EEGroupId'      => '',
	      'EEFieldLabel'   => 'Demo Field',
	      'EEFieldDesc'    => 'Use this field for demo purposes',
	      'EEFieldType'    => 'FLOAT',
	      'EEFieldSize'    => 20,
	      'EEFieldOrder'   => 1,
	      'EEFieldActive'  => $client->boolean(1),
	      'EEFieldPublic'  => $client->boolean(1)
	     }
	) );
print "Result : " . Dumper($r) . "\n";
$client->call('RpcRegionOperations.closeRegion', ($sSessionUUID, $sRegionUUID));
$client->call('RpcUserOperations.closeUserSession', ($sSessionUUID));
