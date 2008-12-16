#!/usr/bin/perl -w
# Import initial data for Countries into the dicore.Country 
# table.
# (c) INTICOL LTDA 2007 - Jhon H. Caicedo <jhcaiced@gmail.com>
#
# A list of country codes in TXT format can be obtained
# from : http://www.davros.org/misc/iso3166.html
#        http://www.davros.org/misc/iso3166.txt

$bFound   = 0;
$bRunning = 1;
print "DELETE FROM Country;\n";
while (<STDIN>) {
	chomp $_;
	if (/^[A-Z][A-Z] /) {
		$bFound = 1;
		if ($bRunning) {
			#$ISOCode2   = substr($_, 0, 2);
			$ISOCode3   = substr($_, 3, 3);
			#$ISOCodeNum = substr($_, 7, 3);
			$ISOName    = substr($_, 11);
			$ISOName =~ s/\'/\\'/g;
			printf("INSERT INTO Country (CountryISOCode, CountryISOName) VALUES ('%s','%s');\n", $ISOCode3, $ISOName);
			#$ISOCode2 $ISOCode3 $ISOCodeNum $ISOName\n";
		}
	} else {
		if ($bFound) {
			$bRunning = 0;
		}
	}
}
