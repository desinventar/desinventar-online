#!/usr/bin/perl
# DesInventar
# Export Event/Cause List from core database to a region
# database.
# (c) 1999-2007 Corporacion OSSO
#
# 2007-03-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
use Getopt::Long;
use DBI;

my $data_source = 'DBI:mysql:database=dicore;host=localhost';
my $username    = 'nobody';
my $passwd      = '';

my $bHelp     = 0;
my $sLangCode = '';

# This script's output is in UTF-8
binmode(STDOUT, ':utf8');

if (!GetOptions('help|h'     => \$bHelp,
                'langcode|l=s' => \$sLangCode
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
$query = "SELECT * FROM Event WHERE EventLangCode='" . $sLangCode . "'";
$sth = $dbh->prepare($query);
$sth->execute();
print "DELETE FROM Event;\n";
while ($e = $sth->fetchrow_hashref) {
	$sEventId        = $e->{EventId};
	$sEventLocalName = $e->{EventLocalName};
	$sEventLocalDesc = $e->{EventLocalDesc};
	printf("INSERT INTO Event (EventId, EventLocalName, EventLocalDesc, EventActive, EventPreDefined) VALUES ('%s','%s','%s',1,1);\n",
		$sEventId, $sEventLocalName, $sEventLocalDesc);	
}
$sth->finish();

# Export Cause List
$query = "SELECT * FROM Cause WHERE CauseLangCode='" . $sLangCode . "'";
$sth = $dbh->prepare($query);
$sth->execute();
print "DELETE FROM Cause;\n";
while ($e = $sth->fetchrow_hashref) {
	$sCauseId        = $e->{CauseId};
	$sCauseLocalName = $e->{CauseLocalName};
	$sCauseLocalDesc = $e->{CauseLocalDesc};
	printf("INSERT INTO Cause (CauseId, CauseLocalName, CauseLocalDesc, CauseActive, CausePreDefined) VALUES ('%s','%s','%s',1,1);\n",
		$sCauseId, $sCauseLocalName, $sCauseLocalDesc);	
}
$sth->finish();


$dbh->disconnect();

