#!/usr/bin/perl
#
# DesInventar - http://www.desinventar.org
# (c) 1999-2009 Corporacion OSSO
#
# Utility to export databases from MySQL to SQLite
#
# 2009-02-20 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
use Getopt::Long;
use DBI;
use POSIX qw(strftime);
my $data_source = 'DBI:mysql:database=di8db;host=localhost';
my $username    = 'di8db'; 
my $passwd      = 'di8db';


my $dbh = DBI->connect($data_source, $username, $passwd) or die "Can't open MySQL database\n";

$dbh->disconnect();

