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

my $bRun       = 0;
my $bDebug     = 0;
my $bCore      = 0;
my $bInfo      = 1;
my $sRegion    = '';
my $sTableName = '';

# Script's output is encoded in UTF-8
binmode(STDOUT, ':utf8');

if (!GetOptions('run|r'      => \$bRun,
                'debug|d'    => \$bDebug,
                'core|c'     => \$bCore,
                'info|i'     => \$bInfo,
                'region|r=s' => \$sRegion,
                'table|t=s'  => \$sTableName,
                
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
$dbin->{mysql_enable_utf8} = 1;

if ($bCore) {
	&cleanTable('Region');
	&convertTable($dbin, $sRegion, "Region", "Region");
	&cleanTable('RegionAuth');
	&convertTable($dbin, $sRegion, "RegionAuth", "RegionAuth");
	&cleanTable('User');
	&convertTable($dbin, $sRegion, "Users", "User");
} else {
	@RegionTables = ('Event',
	                 'Cause',
	                 'GeoLevel',
	                 ['GeoCarto','GeoLevel', {'GeographyId' => '',
	                                          'RegionId'    => $sRegion}],
	                 'Geography',
	                 'Disaster',
	                 'DatabaseLog',
	                 'EEField',
	                 'EEGroup',
	                 'EEData');
	#@RegionTables = ('GeoLevel',
	#                 ['GeoCarto','GeoLevel', {'GeographyId' => '',
	#                                          'RegionId'    => $sRegion}]
	#                );
	
	if ($sTableName ne '') {
		@RegionTables = split(',',$sTableName);
		$bInfo = 0;
		if ($sTableName eq 'INFO') {
			@RegionTables = ();
			$bInfo = 1;
		}
	}

	foreach $myTable (@RegionTables) {
		if (ref($myTable) eq 'ARRAY') {
			$a = ref($myTable) . "\n";
			$sDstTable = $myTable->[0];
		} else {
			$sDstTable = $myTable;
		}
		&cleanTable($sDstTable);
	}
	foreach $myTable (@RegionTables) {
		if (ref($myTable) eq 'ARRAY') {
			$sSrcTable = $sRegion . "_" . $myTable->[1];
			$sDstTable = $myTable->[0];
			$oDefValues = $myTable->[2];
		} else {
			$sSrcTable = $sRegion . "_" . $myTable;
			$sDstTable = $myTable;
			$oDefValues = {};
		}
		&convertTable($dbin, $sRegion, $sSrcTable, $sDstTable, $oDefValues);
	}
	if ($bInfo) {
		# Rebuild Info Table
		&rebuildInfoTable($dbin, $sRegion);
	}
}

$dbin->disconnect();
exit 0;


sub rebuildInfoTable() {
	my $dbin      = $_[0];
	my $sRegionId = $_[1];
	
	$sth = $dbin->prepare("SELECT * FROM Region WHERE RegionUUID='" . $sRegionId . "'");
	$sth->execute();
	while ($r = $sth->fetchrow_hashref()) {
		&saveInfo('RegionId'        , $r->{RegionUUID}, '');
		&saveInfo('RegionLabel'     , $r->{RegionLabel}, '');
		&saveInfo('CountryIso'      , $r->{CountryIsoCode}, '');
		&saveInfo('RegionLastUpdate', $r->{RegionStructLastUpdate}, '');
		&saveInfo('PeriodBeginDate' , $r->{PeriodBeginDate}, '');
		&saveInfo('PeriodEndDate'   , $r->{PeriodEndDate}, '');
		&saveInfo('PeriodOutOfRange', $r->{OptionOutOfPeriod},'');
		&saveInfo('GeoLimitMinX'    , $r->{GeoLimitMinX}, '');
		&saveInfo('GeoLimitMinY'    , $r->{GeoLimitMinY}, '');
		&saveInfo('GeoLimitMaxX'    , $r->{GeoLimitMaxX}, '');
		&saveInfo('GeoLimitMaxY'    , $r->{GeoLimitMaxY}, '');
		&saveInfo('CartoLayerFile'  , $r->{RegionLayerFile}, '');
		&saveInfo('CartoLayerName'  , $r->{RegionLayerName}, '');
		&saveInfo('CartoLayerCode'  , $r->{RegionLayerCode}, '');
		&saveInfo('InfoCredits'     , $r->{RegionCredits}, '');
	}
	$sth->finish();	

	$sth = $dbin->prepare("SELECT * FROM DatabaseInfo WHERE RegionUUID='" . $sRegionId . "'");
	$sth->execute();
	while ($r = $sth->fetchrow_hashref()) {
		$sLang = $r->{RegionLangCode};
		if ($sLang eq 'es') { $sLang = 'spa'; }
		if ($sLang eq 'en') { $sLang = 'eng'; }
		if ($slang eq 'fr') { $sLang = 'fre'; }
		if ($sLang eq 'pr') { $sLang = 'por'; }
		&saveInfo('I18NFirstLang', $sLang, '');
		#&saveInfo('InfoAdminURL', $r->{OptionAdminURL}, '');
	}
	$sth->finish();
}

sub saveInfo() {
	my $sInfoKey      = $_[0];
	my $sInfoValue    = $_[1];
	my $sInfoAuxValue = $_[2];
	print "UPDATE Info SET InfoValue    ='" . $sInfoValue    . "'," .
	                      "InfoAuxValue ='" , $sInfoAuxValue . "' " .
	                      "WHERE InfoKey='" . $sInfoKey . "';" . "\n";
}
sub cleanTable() {
	my $sTableDst = $_[0];
	my $sQuery    = "";
	$sQuery = "DELETE FROM " . $sTableDst . ";";
	print $sQuery . "\n";
}

sub buildEEDataTableDef() {
	my $dbin = $_[0];
	my $sRegion = $_[1];
	my %def = ();
	# Mandatory Fields
	$def{'DisasterId/STRING'} = undef;
	$def{'SyncRecord/DATETIME'} = 'DATETIME';
	$sth = $dbin->prepare("SELECT * FROM " . $sRegion . "_EEField");
	$sth->execute();
	while ($r = $sth->fetchrow_hashref()) {
		$sFieldDef = $r->{'EEFieldId'} . '/' . $r->{'EEFieldType'};
		$def{$sFieldDef} = undef;
	}
	$sth->finish();
	return %def;
}
sub buildEEDataStruct() {
	my %oTableDef = $_[0];
	foreach $sFieldDef (keys(%oTableDef)) {
		($sFieldName, $sFieldType) = split('/', $sFieldDef);
		print $sFieldName . "\n";
	}
}
sub convertTable() {
	my $dbin      = $_[0];
	my $sRegion   = $_[1];
	my $sTableSrc = $_[2];
	my $sTableDst = $_[3];
	my %oDefValues = $_[4];
	my %oTableDef = %{%DesInventarDB::TableDef->{$sTableDst}};
	if ($sTableDst eq 'EEData') {
		%oTableDef = &buildEEDataTableDef($dbin, $sRegion);
		# Fixed Fields (Ugly !!)
		print "DROP TABLE 'EEData';\n";
		print "CREATE TABLE 'EEData' (DisasterId VARCHAR(50) NOT NULL, SyncRecord DATETIME);\n";
		print "BEGIN TRANSACTION;\n";
		foreach $sFieldDef (keys(%oTableDef)) {
			($sFieldName, $sFieldType) = split('/', $sFieldDef);
			if (substr($sFieldName,0,3) eq 'EEF') {
				if ($sFieldType eq 'INTEGER') { $sDefaultValue = "DEFAULT 0"; }
				if ($sFieldType eq 'STRING' ) { $sDefaultValue = "DEFAULT ''"; }
				print 'ALTER TABLE EEData ADD COLUMN ' . $sFieldName . ' ' . $sFieldType . ' ' . $sDefaultValue . ';' . "\n";
			}
		}
		print "COMMIT;\n";
		#&buildEEDataStruct(%oTableDef);
	}
	my $sQuery    = "";
	my $sthin     = null;
	my $sthout    = null;
	$sQuery = "BEGIN TRANSACTION;";
	print $sQuery . "\n";

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
				if (defined $oDefValues->{$sField}) {
					$sValue = $oDefValues->{$sField};
				} else {
					$sValue = $sField;
				}
			}
			if ($sValue eq 'DATETIME') {
				#$sValue = time(); # TimeStamp
				$sValue = strftime("%Y-%m-%d %H:%M:%S", gmtime); # ISO8601
			} elsif (exists $o->{$sValue}) {
				$sValue = $o->{$sValue};
			} 
			# Remove Invalid Chars from Value (i.e. \n)
			$sValue =~ s/\n//g; # New Line Chars
			$sValue =~ s/\0//g; # Null Chars
			$sValue =~ s/\t//g; # Tab Char
			$sValue =~ s/"//g;  # Double Quotes
			
			if (($sFieldType eq 'INTEGER') ||
			    ($sFieldType eq 'DOUBLE' )) {
				if ($sValue eq '') { $sValue = 0; }
			}
			$sFieldList .= $sField . $sAppend;
			# Quote some fields
			if ( ($sFieldType eq 'STRING'  ) ||
			     ($sFieldType eq 'DATETIME') ||
			     ($sFieldType eq 'TEXT'    ) ) {
				$sValueList .= '"' . $sValue . '"' . $sAppend;
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
	$sthin->finish;
	$sQuery = "COMMIT;";
	print $sQuery . "\n";
	
	# Save Sync Field
	$sNow = strftime("%Y-%m-%d %H:%M:%S", gmtime); # ISO8601
	&saveInfo('Sync_' . $sTableDst, $sNow, '');
}
