#!/usr/bin/perl
#
# DesInventar - http://www.desinventar.org
# (c) 1999-2009 Corporacion OSSO
#
# 2009-07-20 Jhon H. Caicedo <jhcaiced@desinventar.org>
# 
# Fix Disaster Table, recalculate Q Fields used in
# Consolidated data.
#
use DBI;
use DBD::mysql;
use DBD::SQLite;
use POSIX qw(strftime);
use Data::Dumper;
use DesInventarDB;

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
#@Dirs = ('/var/lib/desinventar/MEX_2009-07-07_023439');

foreach(@Dirs) {
	$DataFile = $_ . '/desinventar.db';
	$RegionId = substr(substr($_,length($DataDir)),1);
	if ((-e $DataFile) && ($RegionId ne '')) {
		my $dbh = DBI->connect("DBI:SQLite:dbname=" . $DataFile,"","");
		$dbh->{unicode} = 1;
		$Query = "UPDATE Disaster SET InfoValue='" . $RegionId . "' WHERE InfoKey='RegionId';";
		print $RegionId . "\n";
		my %oTableDef = %{%DesInventarDB::TableDef->{'Disaster'}};
		foreach $sFieldDef (keys(%oTableDef)) {
			if (index($sFieldDef, 'Q/') > 0) {
				($sFieldQName, $sFieldType) = split('/', $sFieldDef);
				$sFieldName = substr($sFieldQName, 0, -1);
				$Query = "UPDATE Disaster SET $sFieldQName=$sFieldName WHERE $sFieldName>0";
				$dbh->do($Query);
				$Query = "UPDATE Disaster SET $sFieldQName=0 WHERE $sFieldName<=0";
				$dbh->do($Query);
			}
		}
		#$dbh->do($Query);
		$dbh= undefined;
	}
}
