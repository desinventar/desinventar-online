#!/usr/bin/perl
# DesInventar
# Complete data in EEData table
# (c) 1999-2008 Corporacion OSSO
#
# 2008-09-05 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
use Getopt::Long;
use DBI;

my $data_source = 'DBI:mysql:database=di8db;host=localhost';
my $username    = 'nobody';
my $passwd      = '';

my $bHelp     = 0;
my $sLangCode = '';

# This script's output is in UTF-8
binmode(STDOUT, ':utf8');

if (!GetOptions('help|h'     => \$bHelp,
                'region|r=s' => \$sRegionId
   )) {
   die "Error : Incorrect parameter list, please use --help\n";
}

# Validate Parameters
if ($sRegionId eq '') {
	die "Error : Must specify region to process\n";
}

# Set output mode to UTF8
binmode(STDOUT, ':utf8');

# Export Event List
my $dbh = DBI->connect($data_source, $username, $passwd) or die "Can't open dicore database\n";
$query = "SELECT A.DisasterId AS RecId,B.DisasterId AS ExtId FROM " . $sRegionId . "_Disaster A LEFT JOIN " . $sRegionId . "_EEData B ON (A.DisasterId=B.DisasterId)";
$sth = $dbh->prepare($query);
$sth->execute();
while ($e = $sth->fetchrow_hashref) {
	$sDisasterId     = $e->{"RecId"};
	$sExtId          = $e->{"ExtId"};
	if ($sExtId ne '') {
		#print("$sDisasterId  $sExtId\n");
	} else {
		print "INSERT INTO " . $sRegionId . "_EEData (DisasterId) VALUES ('" . $sDisasterId . "');\n";
	}
}
$sth->finish();

$dbh->disconnect();

