#!/usr/bin/perl
#
# DesInventar - http://www.desinventar.org
# (c) 1999-2009 Corporacion OSSO
#
# 2009-07-20 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 
# Fix Info Table, for each database in the system
# sets Info('RegionId') to the name of the Directory
# Must usually be run as root in order to have write
# access to the databases.
#
use DBI;
use DBD::mysql;
use DBD::SQLite;
use POSIX qw(strftime);
use Data::Dumper;

# Script's output is encoded in UTF-8
binmode(STDOUT, ':utf8');

my $DataDir = '/var/lib/desinventar';
my @Dirs = ();
open(DATA, "find $DataDir -type d | sort |") or die "Can't open file list\n";
while(<DATA>) {
	chomp $_;
	push(@Dirs, $_);
}
close(DATA);

foreach(@Dirs) {
	chomp $_;
	$DataFile = $_ . '/desinventar.db';
	$RegionId = substr(substr($_,length($DataDir)),1);
	if ((-e $DataFile) && ($RegionId ne '')) {
		my $dbh = DBI->connect("DBI:SQLite:dbname=" . $DataFile,"","");
		$dbh->{unicode} = 1;
		$Query = "UPDATE Info SET InfoValue='" . $RegionId . "' WHERE InfoKey='RegionId';";
		print $RegionId . "\n";
		$dbh->do($Query);
		$dbh= undefined;
	}
}
