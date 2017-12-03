<?php

namespace PriceTracker\Trackers;

interface TimeTrackerInterface {
	public function startTracking();
	public function stopTracking();
	public function getTrackingData();
}