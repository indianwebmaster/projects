# ping_check.sh
# Author: Manoj Thakur     08-Jun-2019
#
# The purpose of this script is the monitor the network status by continuosly pinging a domain. 
# It will write the ping success/failure information in a $status_log file, along with the duration of the last failure
# -------------------------------------
URL="www.google.com"
check_frequency_secs=5
write_log_frequency_secs=60
status_log="$HOME/log/ping_check.status"

last_success_time="-"
last_failure_time="-"

check_time_secs=$write_log_frequency_secs
fail_start_secs=0
fail_duration=0
i=0
dt_suffix=$(date +%Y%m%d_%H%M%S)
while [ 1 ]; do
	i=$(expr $i + 1)
	ping -c 1 -t 1 $URL > /dev/null 2>&1
	retval=$?
	if [ $retval == 0 ]; then
		last_success_time=$(date +%a_%d%b%Y_%H_%M_%S)
		if [ $fail_start_secs -ne 0 ]; then
			curr_time_secs=$(date +%s)
			fail_duration=$(expr $curr_time_secs - $fail_start_secs)
			fail_start_secs=0
		fi
	else
		last_failure_time=$(date +%a_%d%b%Y_%H_%M_%S)
		if [ $fail_start_secs -eq 0 ]; then
			fail_start_secs=$(date +%s)
		fi
	fi
	printf "$i Last Success: %s.   Last Failure: %s (%s secs)\r" "$last_success_time" "$last_failure_time" "$fail_duration"

	if [ $i -eq 1 ]; then
		if [ -f $status_log ]; then
			mv $status_log ${status_log}.${dt_suffix}
		fi
		printf "%4s %30s %30s %9s\n" "##" "LastSuccessTime" "LastFailTime" "Duration" >> $status_log
	fi
	if [ $check_time_secs -ge $write_log_frequency_secs ]; then
		printf "%4d %30s %30s %9d\n" $i "$last_success_time" "$last_failure_time" "$fail_duration" >> $status_log
		check_time_secs=0
	fi
	check_time_secs=$(expr $check_time_secs + $check_frequency_secs)
	sleep $check_frequency_secs
done

echo ""
