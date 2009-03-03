#!/usr/bin/perl
# Process a SQL Query running queries line by line,
# use this to determine which line has errors.
use DBI;
use DBD::SQLite;
use Data::Dumper;
my %attr = (
	PrintError => 1,
	RaiseError => 1,
);
$sDBFile = 'desinventar.db';
my $dbh=DBI->connect("DBI:SQLite:dbname=" . $sDBFile,"","", \%attr);
my $sth;
while(<STDIN>) {
	chomp $_;
	#s/\0//g;
	print $_;
	#print Dumper($_);
	$sth = $dbh->prepare($_);
	$sth->execute;
	$sth->finish;
}
undef $sth;
$dbh->disconnect();
#