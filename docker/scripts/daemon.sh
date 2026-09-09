#!/bin/bash

sleep 2
echo "Starting Vigilant daemon..."

lastRun=0

while true
do
	now=$(date '+%s')
	dif=$(bc <<< "$now - $lastRun")

	if [ "$dif" -ge 60 ]; then
		if ! php vigilant.php; then
			exit 1
		fi

		lastRun=$(date '+%s')
	fi

	sleep 10
done
