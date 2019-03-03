#!/usr/bin/perl
# Process iso3166.txt file to load Country List into base.db
# iso3166.txt can be downloaded from:
# http://www.davros.org/misc/iso3166.txt
# 2009-07-06 (jhcaiced) Jhon H. Caicedo <jhcaiced@desinventar.org>
use POSIX qw(strftime);

# This script's output is in UTF-8
binmode(STDOUT, ':utf8');

my $File = 'iso3166.txt';
my $bProcess = 1;

print "DELETE FROM Country;\n";
open(F, '<', $File) || die "Can't open ISO Country List\n";
while(<F>) {
  chomp $_;
  $line = $_;
  if ($line =~ m/withdrawn/) {
    $bProcess = 0;
  }
  if ($bProcess) {
    if ($line =~ m/^[A-Z][A-Z] /) {
      ($CountryIsoCode,$rest) = ($line =~ m/^[A-Z][A-Z] ([A-Z][A-Z][A-Z]) [0-9][0-9][0-9] (.*)/);
      ($CountryName,$rest) = split(',',$rest);
      $CountryIsoName = $rest . ' ' . $CountryName;
      $CountryIsoName =~ s/,//g;
      $CountryIsoName =~ s/^ +//g;
      $sNow = strftime("%Y-%m-%d %H:%M:%S", gmtime); # ISO8601
      printf("INSERT INTO Country (CountryIso,CountryIsoName,CountryName,RecordCreation,RecordSync,RecordUpdate) VALUES ('%s',\"%s\",\"%s\",'%s','%s','%s');\n",
        $CountryIsoCode, $CountryIsoName, $CountryName, $sNow,$sNow,$sNow);
    }
  }
}
close(F);
