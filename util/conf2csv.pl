#!/usr/bin/perl
#  DesInventar - http://www.desinventar.org
#  (c) 1998-2010 Corporacion OSSO
#  
#  2010-01-24 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
#  Move data from *.conf files into a single CSV file for 
#  easier maintaining the interface strings and translations
#
use encoding "utf8";
use Encode;

open(SPA,'<:encoding(utf8)','../portal/include/spa.conf');
open(ENG,'<:encoding(utf8)','../portal/include/eng.conf');
open(POR,'<:encoding(utf8)','../portal/include/por.conf');

my %trans_eng = ();
while(<ENG>) {
	chomp $_;
	$line = $_;
	($key, $value) = split('=', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value ne '') {
		$trans_eng{$key} = $value;
	}
}

my %trans_por = ();
while(<POR>) {
	chomp $_;
	$line = $_;
	($key, $value) = split('=', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value ne '') {
		$trans_por{$key} = $value;
	}
}

while(<SPA>) {
	chomp $_;
	$line = $_;
	($key, $value) = split('=', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value eq '') {
		$group = $key;
		$group =~ s/\[//g;
		$group =~ s/\]//g;
	}
	
	if ($value ne '') {
		$line = sprintf('"%s","%s","%s","%s","%s"', $group, $key, $value, $trans_eng{$key},$trans_por{$key});
		print $line . "\n";
	}
}
close(ENG);
close(POR);
exit(0);

sub trim() {
	$string = shift;
	$string =~ s/\s+$//;
	$string =~ s/^\s+//;
	$string =~ s/\t//g;
	$string =~ s/\n//g;
	$string =~ s/\r//g;
	return $string;
}
