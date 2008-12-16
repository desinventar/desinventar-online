#!/usr/bin/perl
#
# Remove Unused Sessions
# (c) 2007 Corporacion OSSO 
# 2007-05-07 Jhon H. Caicedo <jhcaiced@desinventar.org>
#

#use strict;
#use warnings;
use Frontier::Client;
use Getopt::Long;
use Data::Dumper;

my $sURL   = "http://localhost:8080";
my $client = Frontier::Client->new(url => $sURL, debug => 0);
my $ht = $client->call('RpcDIServer.removeUnusedSessions', (600));

exit 0;
#