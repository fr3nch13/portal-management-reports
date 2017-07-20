#!/bin/bash

#
# checks to see if this script is running.
# if it is, then exit;
#

#proc_myname=`basename $(readlink -f $0)`
proc_myname=$0
proc_check=`ps aux | grep ${proc_myname} | grep bash | grep -v grep | wc -l`
proc_count=$(($proc_check - 2))


if [ "$proc_count" -gt "0" ]
then
echo "${proc_myname} as already running with count: ${proc_count}"
echo "proc_check: ${proc_check}"
echo "proc_count: ${proc_count}"
echo `ps aux | grep ${proc_myname} | grep bash | grep -v grep`
echo '################################'
exit
fi