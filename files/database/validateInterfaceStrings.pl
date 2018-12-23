#!/usr/bin/perl
#
#  Process *.tpl and .conf files in order to test if all labels are defined.
#
use encoding "utf8";
use Encode;
use Data::Dumper;
use Getopt::Long;

my $lang = 'eng';
my $bHelp = 0;
if (!GetOptions('help|h'    => \$bHelp,
                'lang|l=s'  => \$lang,
   )) {
	die "Error : Incorrect parameter list, please use --help\n";
}
$conffile = '../../web/conf/' . $lang . '.conf';
$i = 0;
my %confstr = ();
open(CONF,$conffile);
while(<CONF>)
{
	$line = $_;
	chomp $_;
	if ( ($key,$value) = ($line =~ m/(.*)=(.*)/) )
	{
		$confstr{$key} = $value;
		$i++;
	}
}
close(CONF);
print "$i strings\n";
$i = 0;
my $keys = ();
open(TPL,'cat ../../web/templates/*.tpl ../../portal/templates/*.tpl |') || die "Could'n execute command\n";
while(<TPL>)
{
	$line = $_;
	chomp $_;
	while ($line =~ m/{-#(.+?)#-}/g)
	{
		$key = $1;
		if (! exists $confstr{$key})
		{
			print $key . "\n";
			$i++;
		}
	}
}
close(TPL);
print "$i lines\n";
exit(0);
