#!/bin/bash

sleep 2
echo "Starting Vigilant daemon..."

nextRun=$(bc <<< "$(date '+%s') - 300")

while true
do
	now=$(date '+%s')
	dif=$(bc <<< "$now - $nextRun")

	if [ "$dif" -ge 300 ]; then
		if ! php vigilant.php; then
			exit 1
		fi

		nextRun=$(date '+%s')
	fi

	sleep 10
done
