#!/usr/bin/perl

while(<STDIN>) {
	chomp $_;
	$line = $_;
	($key, $value) = split('=', $line);
	$key = trim($key);
	$value = trim($value);
	if ($value ne '') {
		$line = sprintf('"%s","%s","%s"', $key, $value, '');
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
