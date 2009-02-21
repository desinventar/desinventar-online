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
use DBD::mysql;
use DBD::SQLite;
use POSIX qw(strftime);
my $data_source = 'DBI:mysql:database=di8db;host=localhost';
my $username    = 'di8db'; 
my $passwd      = 'di8db';

use DesInventarDB;
use Symbol;
# Script's output is encoded in UTF-8
binmode(STDOUT, ':utf8');

my $dbin  = DBI->connect($data_source, $username, $passwd) or die "Can't open MySQL database\n";
my $dbout = DBI->connect("DBI:SQLite:dbname=" . "core.db","","");

&convertTable($dbin, $dbout, "Region", "Region", \%DesInventarDB::Region);

$dbin->disconnect();
$dbout->disconnect();
exit 0;

sub convertTable() {
	my $dbin      = $_[0];
	my $dbout     = $_[1];
	my $sTableSrc = $_[2];
	my $sTableDst = $_[3];
	my %oTableDef = %{$_[4]}; # This HASH comes By Reference...
	
	my $sQuery = "SELECT * FROM " . $sTableSrc;
	$sth = $dbin->prepare($sQuery);
	$sth->execute();
	while ($o = $sth->fetchrow_hashref()) {
		$sFieldList = "(";
		$sValueList = "(";
		$iCount = scalar keys(%oTableDef);
		$i = 1;
		foreach $sFieldDef (keys(%oTableDef)) {
			if ($i<$iCount) {
				$sAppend = ",";
			} else {
				$sAppend = "";
			}
			($sField,$sFieldType) = split('/',$sFieldDef);
			$sValue = $oTableDef{$sFieldDef};
			if (defined $o->{$sValue}) {
				$sValue = $o->{$sValue};
			}
			$sFieldList .= $sField . $sAppend;
			if ( ($sFieldType eq 'STRING') ||
			     ($sFieldType eq 'DATETIME') ) {
				$sValueList .= "'" . $sValue . "'" . $sAppend;
			} else {
				$sValueList .= $sValue . $sAppend;
			}
			$i++;
		}
		$sFieldList .= ")";
		$sValueList .= ")";
		$sQuery = "INSERT INTO " . $sTableDst . " " . $sFieldList . " VALUES " . $sValueList . ";";
		print $sQuery . "\n";
	}
	$sth->finish;
}
