#!/usr/bin/perl
#  DesInventar - http://www.desinventar.org
#  (c) 1998-2010 Corporacion OSSO
#  
#  2010-01-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
#  Update the interface strings file using data from another file.
#
use encoding "utf8";
use Encode;

my %trans = ();

open(CSV,'<:encoding(utf8)','di8_interface_strings.csv');
<STDIN>;
while(<STDIN>) {
	chomp $_;
	$line = $_;
	($key,$value_spa,$value) = split(',', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value ne '') {
		#print $key . ' ' . $value . "\n";
		$trans{$key} = $value;
	}
}

# Process CSV File
$header = <CSV>;
print $header;
while(<CSV>) {
	chomp $_;
	$line = $_;
	($group, $key, $value_spa, $value_eng, $value_por, $value_fre) = split(',', $line);
	$group = trim($group);
	$key = trim($key);
	$value_spa = trim($value_spa);
	$value_eng = trim($value_eng);
	$value_por = trim($value_por);
	$value_fre = trim($value_fre);
	if ($trans{$key} ne '') {
		$value_fre = $trans{$key};
	}
	$line = sprintf('"%s","%s","%s","%s","%s","%s"', $group, $key, $value_spa, $value_eng, $value_por, $value_fre);
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
