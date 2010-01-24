#!/usr/bin/perl
use encoding "utf8";
use Encode;

open(SPA  ,"<:encoding(utf8)",'../web/include/spa.conf');
open(TRANS,"<:encoding(utf8)",'../web/include/eng.conf');

my %trans = ();
while(<TRANS>) {
	chomp $_;
	$line = $_;
	($key, $value) = split('=', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value ne '') {
		$trans{$key} = $value;
		#$line = sprintf('"%s","%s","%s"', $key, $value, '');
		#print $line . "\n";
	}
}

while(<SPA>) {
	chomp $_;
	$line = $_;
	($key, $value) = split('=', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value ne '') {
		$line = sprintf('"%s","%s","%s"', $key, $value, $trans{$key});
		print $line . "\n";
	}
}

sub trim() {
	$string = shift;
	$string =~ s/\s+$//;
	$string =~ s/^\s+//;
	$string =~ s/\t//g;
	$string =~ s/\n//g;
	$string =~ s/\r//g;
	return $string;
}
