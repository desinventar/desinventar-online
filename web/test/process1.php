<?php
	require_once('../include/fb.php');
	
	class LongProcess {
		var $pid = 0;
		var $MinValue = 0;
		var $MaxValue = 100;
		var $Value    = 0;
		public function __construct($prmSessionId) {
			$this->pid = 10;
		}
		public function getPid() {
			return $this->pid;
		}
		public function reset() {
			$this->Value = $this->MinValue;
		}
		public function set($prmValue) {
			if ($prmValue > $this->MaxValue) {
				$prmValue = $this->MaxValue;
			}
			if ($prmValue < $this->MinValue) {
				$prmValue = $this->MinValue;
			}
			$this->Value = $prmValue;
		}
		public function setMax($prmValue) {
			$this->MaxValue = $prmValue;
		}
		public function setMin($prmValue) {
			$this->MinValue = $prmValue;
		}
		public function get() {
			return $this->Value;
		}
		public function getMin() {
			return $this->MinValue;
		}
		public function getMax() {
			return $this->MaxValue;
		}
		public function getPercent() {
			$Range = $this->MaxValue - $this->MinValue;
			$Percent = 0;
			if ($Range > 0) {
				$Percent = ($this->Value - $this->MinValue)/$Range;
			}
			$Percent = round($Percent * 100);
			return $Percent;
		}
	} //class

$p = new LongProcess('A');
print $p->getPid() . "<br />\n";
$p->setMax(300);
$p->set(297);
