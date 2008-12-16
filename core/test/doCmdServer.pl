#!/usr/bin/perl
#
# Test Authentication doCmdServer Method
# (c) 2007 Corporacion OSSO 
# 2007-04-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Getopt::Long;
use Data::Dumper;

my $sURL   = "http://localhost:8080";
#if (!GetOptions('help|h'   => \$bHelp,
#                'start'  => \$bStart,
#                'stop'   => \$bStop,
#                'save'   => \$bSave
#   )) {
#   die "Error : Incorrect parameter lit, please use --help\n"; 
#}

my $client = Frontier::Client->new(url => $sURL, debug => 0);
my $r = $client->call('RpcDIServer.doCmdServer', (3));
#