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
use Data::Dumper;
use DesInventarDB;

my $data_source = 'DBI:mysql:database=di8db;host=localhost';
my $username    = 'di8db'; 
my $passwd      = 'di8db';

my $bRun    = 0;
my $bDebug  = 0;
my $bCore   = 0;
my $sRegion = '';

# Script's output is encoded in UTF-8
binmode(STDOUT, ':utf8');

if (!GetOptions('run|r'      => \$bRun,
                'debug|d'    => \$bDebug,
                'core|c'     => \$bCore,
                'region|r=s' => \$sRegion
   )) {
   	die "Error : Incorrect parameter list\n";
}
if ($bCore) {
	$sDBFile = 'core.db';
} else {
	if ($sRegion eq '') {
		die "Must specifiy which region to export\n";
	}
}
my $dbin  = DBI->connect($data_source, $username, $passwd) or die "Can't open MySQL database\n";
my $dbout = DBI->connect("DBI:SQLite:dbname=" . $sDBFile,"","");

if ($bCore) {
	#&convertTable($dbin, $dbout, "Region", "Region");
	#&convertTable($dbin, $dbout, "RegionAuth", "RegionAuth");
	#&convertTable($dbin, $dbout, "Users", "User");
} else {
	#&convertTable($dbin, $dbout, $sRegion . "_Event", "Event");
	#&convertTable($dbin, $dbout, $sRegion . "_Cause", "Cause");
	#&convertTable($dbin, $dbout, $sRegion . "_GeoLevel", "GeoLevel");
	#&convertTable($dbin, $dbout, $sRegion . "_Geography", "Geography");
	#&convertTable($dbin, $dbout, $sRegion . "_Disaster", "Disaster");
}


$dbin->disconnect();
$dbout->disconnect();
exit 0;

sub convertTable() {
	my $dbin      = $_[0];
	my $dbout     = $_[1];
	my $sTableSrc = $_[2];
	my $sTableDst = $_[3];
	#my %oTableDef = %{$_[4]}; # This HASH comes By Reference...
	my %oTableDef = %{%DesInventarDB::TableDef->{$sTableDst}};
	my $sQuery    = "";
	my $sthin     = null;
	my $sthout    = null;
	$sQuery = "DELETE FROM " . $sTableDst . ";";
	if ($bDebug) {
		print $sQuery . "\n";
	}
	if ($bRun) {
		$sthout = $dbout->prepare($sQuery);
		$sthout->execute();
		$icount = $sthout->rows();
		$sthout->finish;
	}

	$sQuery = "SELECT * FROM " . $sTableSrc;
	$sthin = $dbin->prepare($sQuery);
	$sthin->execute();
	while ($o = $sthin->fetchrow_hashref()) {
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
			if (defined $oTableDef{$sFieldDef}) {
				$sValue = $oTableDef{$sFieldDef};
			} else {
				$sValue = $sField;
			}
			if ($sValue eq 'DATETIME') {
				#$sValue = time(); # TimeStamp
				$sValue = strftime("%Y-%m-%d %H:%M:%S", gmtime); # ISO8601
			} elsif (exists $o->{$sValue}) {
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
		if ($bDebug) {
			print $sQuery . "\n";
		}
		if ($bRun) {
			$sthout = $dbout->prepare($sQuery);
			$sthout->execute();
			$icount = $sthout->rows();
			$sthout->finish;
		}
	}
	$sthin->finish;
}
