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
		
		public static function getLastDayOfMonth($Year, $Month) {
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
	} //class
</script>
