<script language="php">
	class DIDate {
		// Return 1 if its a Leap Year, 0 if it's not and -1 if an error occurs
		public static function isLeapYear($prmYear) {
			$bReturn = -1; // Error
	        if (is_int($prmYear) && $prmYear > 0) {
	        	# In the Gregorian calendar there is a leap year every year divisible by four
	        	# except for years which are both divisible by 100 and not divisible by 400.
	        	if ($prmYear % 4 != 0) {
	        		$iReturn = 0; // Not Leap Year
				} else {
					$iReturn = 1; // Leap Year
					if ($prmYear % 100 == 0) {
						$iReturn = 1; // Leap Year
					}
					if ($prmYear % 400 == 0) {
						$iReturn = 0; // Not Leap Year
					}
				}
			}
			return $iReturn;
		} //function
		
		public static function getDaysOfMonth($Year, $Month) {
			$MDays = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
			$Day = $MDays[$Month];
			// On February, check if Year is leap and add one more day
			if ($Month == 2) {
				if (DIDate::isLeapYear($Year)) {
					$Day++;
				}
			} //if
			return $Day;
		} //function
		
		public static function getYear($prmDate) {
			$Year = trim(substr($prmDate,0,4));
			return $Year;
		}
		public static function getMonth($prmDate) {
			$Month = trim(substr($prmDate, 5, 2));
			return $Month;
		}
		public static function getDay($prmDate) {
			$Day = trim(substr($prmDate, 8, 2));
			return $Day;
		}

		public static function getWeekOfYear($prmDate) {
			$iWeek = date("W", mktime(5, 0, 0, DIDate::getMonth($prmDate),
			                                   DIDate::getDay($prmDate),
			                                   DIDate::getyear($prmDate)
			    ));
			return $iWeek;
		} //function
		
		public static function padNumber($prmValue, $prmLength) {
			$value = $prmValue;
			while(strlen($value) < $prmLength) {
				$value = '0' . $value;
			}
			return $value;
		}
	} //class
</script>
