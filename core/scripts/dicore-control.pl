#!/usr/bin/perl
#
# Control Script for DICORE Server
# (c) 2007 Corporacion OSSO 
# 2007-03-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

use strict;
use warnings;
use Frontier::Client;
use Getopt::Long;

my $sURL   = "http://localhost:8081";
my $bHelp  = 0;
my $bStart = 0;
my $bStop  = 0;
my $bSave  = 0;
my $bAwake = 0;
my $bClear = 0;
if (!GetOptions('help|h'   => \$bHelp,
                'start'  => \$bStart,
                'stop'   => \$bStop,
                'save'   => \$bSave,
                'awake'  => \$bAwake,
                'clear'  => \$bClear
   )) {
   die "Error : Incorrect parameter lit, please use --help\n"; 
}
if ($bHelp) {
	exit 0;
}

my $client = Frontier::Client->new(url => $sURL, debug => 0);
if ($bStart) {
	print "Starting DICORE Server...\n";
	#my $sJava = "/usr/java/jdk1.5.0_10/bin/java";
	my $sJava = "java";
	my $sJavaOpts = "-Dfile.encoding=utf-8 -Djava.ext.dirs=/usr/share/java-ext";
	my $sJarFile = "/usr/share/desinventar/dicore.jar";
	my $sCmd = $sJava . " " . $sJavaOpts . " -jar " . $sJarFile  . " 2>>/tmp/dicore.log 1>>/tmp/dicore.log &";
	print "$sCmd\n";
	system($sCmd);
}
if ($bStop) {
	print "Stop DICORE Server now !!!\n";
	$client->call('RpcDIServer.doCmdServer',(0));
}
if ($bSave) {
	print "Saving Session List...\n";
	$client->call('RpcDIServer.doCmdServer',(1));
}
if ($bClear) {
	print "Removing temporal server files ...\n";
	system("/bin/rm /tmp/di8*.db3");
}
if ($bAwake) {
	#print "Awake DB Connections...\n";
	$client->call('RpcDIServer.doCmdServer',(3));
}
