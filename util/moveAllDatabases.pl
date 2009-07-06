#!/usr/bin/perl
use POSIX qw(strftime);
use DBI;
use DBD::SQLite;
use Data::Dumper;

my $data_dir = '/var/lib/desinventar';
my $sDBFile = $data_dir . '/core.db';

my %DBList = ();
my $dbh = DBI->connect("DBI:SQLite:dbname=" . $sDBFile,"","");
$dbh->{unicode} = 1;
$sth = $dbh->prepare("SELECT * FROM Region ORDER BY RegionId");
$sth->execute();
while ($r = $sth->fetchrow_hashref()) {
	$DBName1 = $r->{RegionId};
	$DBName2 = $r->{CountryIso};
	$DBList{$DBName1} = $DBName2;
}
#%DBList = ('ARGENTINA' => 'ARG_2009-07-06');

while (my ($DBName1, $DBName2) = each(%DBList) ) {
	print "--------------------------------------------------\n";
	$DBName2 .= "_" . strftime('%Y-%m-%d_%H%M%S', gmtime);
	printf("%15s %20s\n", $DBName1, $DBName2);
	$cmd = "mkdir $data_dir/$DBName2";
	system2($cmd);
	$cmd = "cp ../files/database/desinventar.db $data_dir/$DBName2";
	system2($cmd);
	$cmd = "./mysql2sqlite.pl -r $DBName1 | sqlite3 $data_dir/$DBName2/desinventar.db";
	system2($cmd);
	$cmd = "cp $data_dir/carto/$DBName1/* $data_dir/$DBName2";
	system2($cmd);
	$q = "UPDATE Region SET RegionId='$DBName2' WHERE RegionId='$DBName1';";
	$cmd = "sqlite3 $data_dir/core.db < echo '$q'";
	$dbh->do($q);
	#print "$q\n";
	$q = "UPDATE RegionAuth SET RegionId='$DBName2' WHERE RegionId='$DBName1';";
	$cmd = "sqlite3 $data_dir/core.db < '$q'";
	$dbh->do($q);
	#print "$q\n";
}

sub system2() {
	$cmd = $_[0];
	#print "$cmd\n";
	system($cmd);
}
#$sth->finish();
#$dbh= undefined;

exit 0;
