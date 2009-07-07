#!/usr/bin/perl
use POSIX qw(strftime);
use DBI;
use DBD::SQLite;
use Data::Dumper;

my $data_dir = '/var/lib/desinventar';
my $sDBFile = $data_dir . '/core.db';
my %DBList = ();

my $data_source = 'DBI:mysql:database=di8db;host=localhost';
my $username    = 'di8db'; 
my $passwd      = 'di8db';
my $dbh  = DBI->connect($data_source, $username, $passwd) or die "Can't open MySQL database\n";
$dbh->{mysql_enable_utf8} = 1;
#my $dbh = DBI->connect("DBI:SQLite:dbname=" . $sDBFile,"","");
#$dbh->{unicode} = 1;
$sth = $dbh->prepare("SELECT * FROM Region ORDER BY RegionUUID");
$sth->execute();
while ($r = $sth->fetchrow_hashref()) {
	$DBName1 = $r->{RegionUUID};
	$DBName2 = $r->{CountryIsoCode};
	$DBList{$DBName1} = $DBName2;
}
#%DBList = ('COLOMBIA' => 'COLOMBIA');

$cmd = "/bin/cp ../files/database/core.db $data_dir";
system2($cmd);
$cmd = "/bin/cp ../files/database/base.db $data_dir";
system2($cmd);
$cmd = "./mysql2sqlite.pl --core | sqlite3 $data_dir/core.db";
system2($cmd);

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
	$cmd = "cat colores.sql | sqlite3 $data_dir/$DBName2/desinventar.db";
	system2($cmd);
	$cmd = "cp $data_dir/carto/$DBName1/* $data_dir/$DBName2";
	system2($cmd);
	$q = "UPDATE Region SET RegionId=\"$DBName2\" WHERE RegionId=\"$DBName1\";";
	$cmd = "echo '$q' | sqlite3 $data_dir/core.db";
	system2($cmd);
	$q = "UPDATE RegionAuth SET RegionId=\"$DBName2\" WHERE RegionId=\"$DBName1\";";
	$cmd = "echo '$q' | sqlite3 $data_dir/core.db";
	system2($cmd);
}

sub system2() {
	$cmd = $_[0];
	#print "$cmd\n";
	system($cmd);
}
#$sth->finish();
#$dbh= undefined;

exit 0;
