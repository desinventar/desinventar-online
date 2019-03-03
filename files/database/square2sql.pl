#!/usr/bin/perl
# Process country_square.txt to initialize the are covered by each country
# 2009-07-06 (jhcaiced) Jhon H. Caicedo <jhcaiced@desinventar.org>

# ABW: Xmin=-70.05966   ;Xmax=-69.87486   ;Ymin=12.41111   ;Ymax=12.62778   ;

# This script's output is in UTF-8
binmode(STDOUT, ':utf8');

my $File = 'country_square.txt';
open(F, '<', $File) || die "Can't open Country List\n";
while(<F>) {
  chomp $_;
  s/\"//g;
  $line = $_;
  ($CountryIsoCode,$Xmin,$Xmax,$Ymin,$Ymax) = ($line =~ m/^([A-Z][A-Z][A-Z]):.*=(.*);.*=(.*);.*=(.*);.*=(.*);.*/);
  $Xmin =~ s/ //g;
  $Xmax =~ s/ //g;
  $Ymin =~ s/ //g;
  $Ymax =~ s/ //g;
  printf("UPDATE Country SET CountryMinX=%s,CountryMaxX=%s,CountryMinY=%s,CountryMaxY=%s WHERE CountryIso='%s';\n", $Xmin, $Xmax, $Ymin, $Ymax, $CountryIsoCode);
}
close(F);
