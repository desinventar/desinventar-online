#!/usr/bin/perl
#
#  Convert a csv file to xml
#
use encoding "utf8";
use Encode;
use Data::Dumper;
use Getopt::Long;

my $index = 2;
my $bHelp = 0;
my $bPortal = 0;
my $file  = 'interface_strings.csv';

if (!GetOptions('help|h'    => \$bHelp,
                'portal|p'  => \$bPortal
   )) {
  die "Error : Incorrect parameter list, please use --help\n";
}

if ($bPortal) {
  $file = 'portal_strings.csv';
}

my %trans = ();
my $prevgroup = '';
print '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
print '<StringList>' . "\n";
open(CSV,'<:encoding(utf8)',$file);
$header = <CSV>;
while(<CSV>)
{
  chomp $_;
  $line = $_;
  ($group, $key, $value_spa, $value_eng, $value_por, $value_fre) = split(',', $line);
  $group = trim($group);
  if ($group ne $prevgroup)
  {
    if ($group ne '')
    {
      if ($prevgroup ne '')
      {
        print "\t" . '</Group>' . "\n";
      }
      print "\t" . '<Group name="' . $group . '">' . "\n";
    }
    $prevgroup = $group;
  }
  $key = trim($key);
  $value_spa = trim($value_spa);
  $value_eng = trim($value_eng);
  $value_por = trim($value_por);
  $value_fre = trim($value_fre);
  print "\t\t" . '<Message id="' . $key . '">' . "\n";
  print "\t\t\t" . '<Text LangIsoCode="eng">' . $value_eng . '</Text>' . "\n";
  print "\t\t\t" . '<Text LangIsoCode="spa">' . $value_spa . '</Text>' . "\n";
  print "\t\t\t" . '<Text LangIsoCode="por">' . $value_por . '</Text>' . "\n";
  print "\t\t\t" . '<Text LangIsoCode="fre">' . $value_fre . '</Text>' . "\n";
  print "\t\t" . '</Message>' . "\n";
}
print "\t" . '</Group>' . "\n";
print '</StringList>' . "\n";
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
  $string =~ s/\&/\&amp\;/g;
  $string =~ s/</\&lt\;/g;
  $string =~ s/>/\&gt\;/g;
  $string =~ s/'/\&quot\;/g;
  return $string;
}
