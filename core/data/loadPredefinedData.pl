#!/usr/bin/perl -w
#
# DesInventar8
# http://www.desinventar.org
# (c) 1999-2008 Corporacion OSSO
#
# 2008-05-15 Jhon H. Caicedo <jhcaiced@desinventar.org>
#
# Convert a CSV file to SQL, this data should be imported into di8db.DIEvent
#
use Getopt::Long;
use Time::Local;
use Text::CSV;
use POSIX qw(strftime);

my $sInFile = '';
my $bHelp   = '';
my $sType   = '';

if (!GetOptions('help|h'     => \$bHelp,
                'infile|i=s' => \$sInFile,
                'type|t=s'   => \$sType
)) {
	die "Error : Incorrect parameter list, please use --help\n";
}

if ($sInFile eq '') {
	die "Error : Please specify input file name\n";
}
if (($sType ne 'EVENT') && 
    ($sType ne 'CAUSE')) {
	die "Error : Type of data must be EVENT or CAUSE\n";
}
my $csv = Text::CSV->new();

# get today's date
$sNowDate  = strftime("%Y-%m-%d",localtime(time()));
# Set output mode to UTF8
binmode(STDOUT, ':utf8');
open(F, '<:utf8', $sInFile) || die "Can't open input file : $sInFile\n";
<F>;
while (<F>) {
	chop $_;
	if ($csv->parse($_) >= 0) {
		if ($sType eq 'EVENT') {
			($sEventId, $sEventLocalName, $sEventLocalDesc, $sEventDI6Name, $sEventLangCode) = $csv->fields();
			printf("INSERT INTO DIEvent (EventId,EventLocalName,EventLocalDesc,EventDI6Name,EventLangCode,EventCreationDate,EventLastUpdate) VALUES (%s,%s,%s,%s,%s,%s,%s);\n",
				&quoteStr($sEventId),
				&quoteStr($sEventLocalName),
				&quoteStr($sEventLocalDesc),
				&quoteStr($sEventDI6Name),
				&quoteStr($sEventLangCode),
				&quoteStr($sNowDate),
				&quoteStr($sNowDate)
			);
		} else {
			($sCauseId, $sCauseLocalName, $sCauseLocalDesc, $sCauseDI6Name, $sCauseLangCode) = $csv->fields();
			printf("INSERT INTO DICause (CauseId,CauseLocalName,CauseLocalDesc,CauseDI6Name,CauseLangCode, CauseCreationDate,CauseLastUpdate) VALUES (%s,%s,%s,%s,%s,%s,%s);\n",
				   &quoteStr($sCauseId),
				   &quoteStr($sCauseLocalName),
				   &quoteStr($sCauseLocalDesc),
				   &quoteStr($sCauseDI6Name),
				   &quoteStr($sCauseLangCode),
				   &quoteStr($sNowDate),
				   &quoteStr($sNowDate)
			);
		}
	}
}
close(F);

sub quoteStr() {
	my $v = $_[0];
	return "'" . $v . "'";
}