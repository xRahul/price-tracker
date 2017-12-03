<?php

namespace PriceTracker\Trackers;

use PriceTracker\Trackers\TimeTrackerInterface;
use Exception;

class TimeTracker implements TimeTrackerInterface {

	private $beginTimestamp;
	private $endTimestamp;

	public function startTracking()
	{
		$this->beginTimestamp = microtime(true);
	}

	public function stopTracking()
	{
		$this->endTimestamp = microtime(true);
	}

	public function getTrackingData()
	{
		$this->checkTrackingData();
		return intval(($this->endTimestamp - $this->beginTimestamp) * 1000);
	}

	private function checkTrackingData() {
		if (empty($this->endTimestamp)) {
			throw new Exception("You need to stop tracking first.");
		}
		if (empty($this->beginTimestamp)) {
			throw new Exception("You need to start tracking first.");
		}
	}
}