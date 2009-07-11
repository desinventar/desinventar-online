#!/usr/bin/perl
use Getopt::Long;
use POSIX qw(strftime);
use Text::CSV;
use Data::Dumper;
use DBI;

# This script's output is in UTF-8
binmode(STDOUT, ':utf8');

my $sLangIsoCode = 'spa';
my $sTableName   = 'Event';
my $sFileName    = '';

if (!GetOptions('lang|l=s'   => \$sLangIsoCode,
                'table|t=s'  => \$sTableName,
                'file|f=s'   => \$sFileName
   )) {
   	die "Error : Incorrect parameter list\n";
}
if ($sLangCode eq '') {
	$sLangCode = 'spa';
}
if ($sFileName eq '') {
	die "Must specify csv file to read and tablename\n";
}

my $dbh = DBI->connect("DBI:CSV:f_dir=.;csv_eol=\n;");
$dbh->{'csv_tables'}->{$sTableName} = {'file' => $sFileName};
my $sth = $dbh->prepare("SELECT * FROM " . $sTableName);
$sth->execute();
while (my $row = $sth->fetchrow_hashref) {
	$sNow = strftime("%Y-%m-%d %H:%M:%S", gmtime); # ISO8601
	$Query = sprintf("INSERT INTO %s VALUES " . 
		"('%s','%s','%s','%s',\"%s\",%d,%d,'%s','%s','%s','%s');",
		$sTableName,
		$row->{$sTableName . 'Id'},
		$sLangIsoCode,
		$sNow,
		$row->{$sTableName . 'Name(' . $sLangIsoCode . ')'},
		$row->{$sTableName . 'Desc(' . $sLangIsoCode . ')'},
		1, # Active
		1, # Predefined
		$row->{$sTableName . 'RGBColor'},
		$row->{$sTableName . 'KeyWords'},
		$row->{$sTableName . 'CreationDate'},
		$row->{$sTableName . 'LastUpdate'}
	);
	print $Query . "\n";
}
$sth->finish();
$dbh->disconnect();
exit 0;

$csv = new Text::CSV->new();
open(F,"<eve.csv") || die "Can't open file\n";
while (<F>) {
	chomp;
	$line = $_;
	$status = $csv->parse($line);
	@c = $csv->fields();
	print $status . "\n";
	$q = sprintf("INSERT INTO DI_Event VALUES (%s);", $c[0]);
	print $q . "\n";
	print $status . ' ' . Dumper(@c);
}
close(F);

print "\n";

