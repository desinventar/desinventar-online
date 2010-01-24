#!/usr/bin/perl
#  DesInventar - http://www.desinventar.org
#  (c) 1998-2010 Corporacion OSSO
#  
#  2010-01-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
#  Create the spa.conf,eng.conf,por.conf,fre.conf files 
#  with the interface strings using the csv file
#
use encoding "utf8";
use Encode;
use Data::Dumper;
use Getopt::Long;

my $lang = '';
my $index = 2;

if (!GetOptions('help|h'    => \$bHelp,
                'lang|l=s'  => \$lang
   )) {
	die "Error : Incorrect parameter list, please use --help\n";
}                

my %trans = ();
my $prevgroup = '';

open(CSV,'<:encoding(utf8)','di8_interface_strings.csv');
$header = <CSV>;
while(<CSV>) {
	chomp $_;
	$line = $_;
	($group, $key, $value_spa, $value_eng, $value_por, $value_fre) = split(',', $line);
	$group = trim($group);
	if ($group ne $prevgroup) {
		if ($prevgroup ne '') {
			print "\n";
		}
		print '[' . $group . ']' . "\n";
		$prevgroup = $group;
	}
	$key = trim($key);
	$value_spa = trim($value_spa);
	$value_eng = trim($value_eng);
	$value_por = trim($value_por);
	$value_fre = trim($value_fre);
	$value = $value_spa;
	if ($lang eq 'spa') {
		$value = $value_spa;
	} elsif ($lang eq 'eng') {
		$value = $value_eng;
	} elsif ($lang eq 'por') {
		$value = $value_por;
	} elsif ($lang eq 'fre') {
		$value = $value_fre;
	}
	$line = sprintf('%s = %s', $key, $value);
	print $line . "\n";
}
close(CSV);
exit(0);

sub trim() {
	$string = shift;
	$string =~ s/\s+$//;
	$string =~ s/^\s+//;
	$string =~ s/\t//g;
	$string =~ s/\n//g;
	$string =~ s/\r//g;
	$string =~ s/\"//g;
	return $string;
}
