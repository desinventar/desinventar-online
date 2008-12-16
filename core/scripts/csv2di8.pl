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
use POSIX qw(strftime);
use Text::CSV;
use Data::Dumper;

my $sInFile = '';
my $bHelp   = '';
my $sType   = '';

if (!GetOptions('help|h'     => \$bHelp,
                'infile|i=s' => \$sInFile,
                'type|t=s'   => \$sType
)) {
	die "Error : Incorrect parameter list, please use --help\n";
}

# get today's date
$sNowDate  = strftime("%Y-%m-%d",localtime(time()));
# Set output mode to UTF8
#binmode(STDOUT, ':utf8');
binmode(STDOUT, ':encoding(iso-8859-1)');
my $csv = Text::CSV->new();
my %Event     = ();
my %Cause     = ();
my %Geography = ();
my %Disaster  = ();
if ($sInFile ne '') {
	open(F, '<:encoding(iso-8859-1)', $sInFile) || die "Can't open input file : $sInFile\n";
} else {
	open(F, "<&STDIN");
}
<F>;
while(<F>) {
	chop $_;
	if ($csv->parse($_) >= 0) {
		$_ =~ s/\"//g;
		@columns = $csv->fields();
		if ($sType eq 'EVENT') {
			$sEventId = &getFieldStr($columns[1]);
			if (length($sEventId) > 0) {
				$Event{$sEventId} = $sEventId;
			}
		}
		if ($sType eq 'CAUSE') {
			$sCauseId = &getFieldStr($columns[9]);
			if ($sCauseId ne '') {
				$Cause{$sCauseId} = $sCauseId;
			}
		}
		if ($sType eq 'GEOGRAPHY') {
			# First try Level2
			$sGeographyCode  = &getFieldStr($columns[6]);
			$sGeographyName  = &getFieldStr($columns[7]);
			$iGeographyLevel = 2;
			if ($sGeographyCode ne  '') {
				$sGeographyParentCode = &getFieldStr($columns[4]);
				$Geography{$sGeographyCode}{GeographyCode}       = $sGeographyCode;
				$Geography{$sGeographyCode}{GeographyLevel}      = $iGeographyLevel;
				$Geography{$sGeographyCode}{GeographyName}       = $sGeographyName;
				$Geography{$sGeographyCode}{GeographyParentCode} = $sGeographyParentCode;
				#printf("%-15s,%-30s,%4d,%-30s\n", $sGeographyCode, $sGeographyName, $iGeographyLevel, $sGeographyParentCode);
			}
			
			# Try Level1
			$sGeographyCode  = &getFieldStr($columns[4]);
			$sGeographyName  = &getFieldStr($columns[5]);
			$iGeographyLevel = 1;
			if ($sGeographyCode ne '') {
				$sGeographyParentCode = &getFieldStr($columns[2]);
				$Geography{$sGeographyCode}{GeographyCode}       = $sGeographyCode;
				$Geography{$sGeographyCode}{GeographyLevel}      = $iGeographyLevel;
				$Geography{$sGeographyCode}{GeographyName}       = $sGeographyName;
				$Geography{$sGeographyCode}{GeographyParentCode} = $sGeographyParentCode;
				#printf("%-15s,%-30s,%4d,%-30s\n", $sGeographyCode, $sGeographyName, $iGeographyLevel, $sGeographyParentCode);
			}
				
			# Try Level 0
			$sGeographyCode = &getFieldStr($columns[2]);
			$sGeographyName = &getFieldStr($columns[3]);
			$iGeographyLevel = 0;
			if ($sGeographyCode ne '') {
				$sGeographyParentCode = '';
				$Geography{$sGeographyCode}{GeographyCode}       = $sGeographyCode;
				$Geography{$sGeographyCode}{GeographyLevel}      = $iGeographyLevel;
				$Geography{$sGeographyCode}{GeographyName}       = $sGeographyName;
				$Geography{$sGeographyCode}{GeographyParentCode} = $sGeographyParentCode;
				#printf("%-15s,%-30s,%4d,%-30s\n", $sGeographyCode, $sGeographyName, $iGeographyLevel, $sGeographyParentCode);
			}
		}
		
		if ($sType eq 'DISASTER') {
			$sGeographyCode = &getFieldStr($columns[6]);
			if ($sGeographyCode eq '') { $sGeographyCode = &getFieldStr($columns[4]); }
			if ($sGeographyCode eq '') { $sGeographyCode = &getFieldStr($columns[2]); }
			$sDate = &getFieldStr($columns[8]); $sDate =~ s/\//-/g;
			printf("%s,%s,%s,%s,%s,%s,%s,%s,%d,%s,%s,%s,%d,%d,%d,%d,%d,%d,%d,%d,%d,%s,%s,%s,%s,%s,%d,%d,%d,%s,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d\n",
				&quoteStr($columns[0]),     # Serial
				&quoteStr($sDate),          # Date
				&quoteStr($sGeographyCode), # GeographyCode
				&quoteStr(''),              # SiteNotes (empty in DI7)
				&quoteStr($columns[11]),    # Disaster Source
				&quoteStr(''),              # RecordAuthor (empty in DI7)
				&quoteStr($sNowDate),       # RecordCreation (empty in DI7, use current time
				&quoteStr($columns[1]),     # EventId
				0,                          # EventDuration (empty in DI7)
				&quoteStr($columns[12]),    # Magnitude
				&quoteStr($columns[9]),     # Cause
				&quoteStr($columns[10]),    # CauseNotes
				
				&getFieldNum($columns[16]),               # EffectPeopleDead
				&getFieldNum($columns[18]),               # EffectPeopleMissing
				&getFieldNum($columns[17]),               # EffectPeopleInjured
				&getFieldNum($columns[21]),               # EffectPeopleHarmed (DI7: Victims)
				&getFieldNum($columns[22]),               # EffectPeopleAffected
				&getFieldNum($columns[24]),               # EffectPeopleEvacuated
				&getFieldNum($columns[23]),               # EffectPeopleRelocated
				&getFieldNum($columns[19]),               # EffectHousesDestroyed
				&getFieldNum($columns[20]),               # EffectHousesAffected
				
				sprintf("%.2f", &getFieldNum($columns[26])),          # EffectLossesValueLocal
				sprintf("%.2f", &getFieldNum($columns[25])),          # EffectLossesValueUSD
				sprintf("%.2f", &getFieldNum($columns[31])),          # EffectRoads
				sprintf("%.2f", &getFieldNum($columns[29])),          # EffectFarmingAndForest
				sprintf("%.0f", &getFieldNum($columns[30])),          # EffectLiveStock (Catle in DI7)
				
				&getFieldNum($columns[27]),               # EffectEducationCenters
				&getFieldNum($columns[28]),               # EffectMedicalCenters
				0,                          # EffectOtherLosses (emtpy in DI7)
				&quoteStr($columns[14]),    # EffectNotes
				
				&getFieldNum($columns[48]),               # SectorTransport
				&getFieldNum($columns[47]),               # SectorCommunications
				&getFieldNum($columns[50]),               # SectorRelief
				&getFieldNum($columns[43]),               # SectorAgricultural
				&getFieldNum($columns[44]),               # SectorWaterSupply
				&getFieldNum($columns[45]),               # SectorSewerage
				&getFieldNum($columns[41]),               # SectorEducation
				&getFieldNum($columns[49]),               # SectorPower
				&getFieldNum($columns[46]),               # SectorIndustry
				&getFieldNum($columns[42]),               # SectorHealth
				&getFieldNum($columns[51]),               # SectorOther
			);
		}
	} else {
		print "Status : " . $csv->status() . "\n";
		print "Error  : " . $csv->error_input() . "\n";
	}
}
if ($sInFile ne '') {
	close(F);
}

my $i = 0;
my ($key, $value);
if ($sType eq 'EVENT') {
	#print Dumper(%Event);
	while (($key,$value) = each(%Event) ) {
		printf("%s,%s,%s\n", &quoteStr($i),&quoteStr($key),&quoteStr($value));
		$i++;
	}
}

if ($sType eq 'CAUSE') {
	#print Dumper(%Cause);
	while (($key,$value) = each(%Cause) ) {
		printf("%s,%s\n", &quoteStr($i),&quoteStr($key));
		$i++;
	}
}

if ($sType eq 'GEOGRAPHY') {
	for $key (sort keys %Geography) {
		printf("%d,%s,%s,%s\n",
			$Geography{$key}{GeographyLevel},
			&quoteStr($Geography{$key}{GeographyCode}),
			&quoteStr($Geography{$key}{GeographyName}),
			&quoteStr($Geography{$key}{GeographyParentCode})
		);
	}
}
sub getFieldStr() {
	my $v = $_[0];
	if (defined $v) {
		return $v;
	} else {
		return "";
	}		
}
sub getFieldNum() {
	my $v = $_[0];
	if (defined $v) {
		if (length($v) > 0) {
			return $v;
		} else {
			return "0";
		}
	} else {
		return "0";
	}		
}
sub quoteStr() {
	my $v = $_[0];
	if (defined $v) {
		$v =~ s/"//g;
		return '"' . $v . '"';
	} else {
		return '""';
	}
}