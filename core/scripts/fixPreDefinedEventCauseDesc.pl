#!/usr/bin/perl
# DesInventar
# Fix Predefined Event/Cause Description
# Export Event/Cause List from core database to a region
# database.
# (c) 1999-2007 Corporacion OSSO
#
# 2007-03-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
use Getopt::Long;
use DBI;

my $data_source = 'DBI:mysql:database=di8db;host=localhost';
my $username    = 'di8db';
my $passwd      = 'di8db';

my $bHelp     = 0;
my $sLangCode = 'es';
my $sRegion   = 'empty';
# This script's output is in UTF-8
binmode(STDOUT, ':utf8');

if (!GetOptions('help|h'       => \$bHelp,
                'langcode|l=s' => \$sLangCode,
                'region|r=s'   => \$sRegion
   )) {
   die "Error : Incorrect parameter list, please use --help\n";
}

# Validate Parameters
if ($sLangCode eq '') {
	die "Error : Must specify language code to copy\n";
}

# Set Encoding of Data, just in case MySQL is using another
print "SET NAMES 'utf8';\n";

# Export Event List
my $dbh = DBI->connect($data_source, $username, $passwd) or die "Can't open dicore database\n";
$query = "SELECT * FROM DIEvent WHERE EventLangCode='" . $sLangCode . "'";
$sth = $dbh->prepare($query);
$sth->execute();
while ($e = $sth->fetchrow_hashref) {
	$sEventId        = $e->{EventId};
	$sEventLocalName = $e->{EventLocalName};
	$sEventLocalDesc = $e->{EventLocalDesc};
	printf("UPDATE %s_Event SET EventLocalDesc='%s' WHERE EventId='%s';\n",
		$sRegion, $sEventLocalDesc, $sEventId);	
}
$sth->finish();
# Export Cause List
$query = "SELECT * FROM DICause WHERE CauseLangCode='" . $sLangCode . "'";
$sth = $dbh->prepare($query);
$sth->execute();
while ($e = $sth->fetchrow_hashref) {
	$sCauseId        = $e->{CauseId};
	$sCauseLocalName = $e->{CauseLocalName};
	$sCauseLocalDesc = $e->{CauseLocalDesc};
	printf("UPDATE %s_Cause SET CauseLocalDesc='%s' WHERE CauseId='%s';\n",
		$sRegion, $sCauseLocalDesc, $sCauseId);	
}
$sth->finish();


$dbh->disconnect();

