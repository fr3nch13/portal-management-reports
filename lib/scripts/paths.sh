#!/bin/bash

#
# Paths for useage in other scrips
#
# used this way because this is included with the source command
path_scripts="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
# use this if this is the main script
#path_scripts=`dirname $(readlink -f $0)`

path_base=`dirname ${path_scripts}`
path_base=`dirname ${path_base}`
path_base="${path_base}/"

# main app directories
path_app="${path_base}app/"
path_bin="${path_app}bin/"
path_console="${path_app}Console/"
path_config="${path_app}Config/"
path_tmp="${path_app}tmp/"
path_logs="${path_tmp}logs/"

# plugins directory
path_plugins="${path_base}plugins/"

# command shortcuts
path_cake_cmd="php -q ${path_console}cake.php -working ${path_app}"
cmd_schema="${path_cake_cmd} schema"
cmd_cron="${path_cake_cmd} cron"
cmd_update="${path_cake_cmd} update"
cmd_utility="${path_cake_cmd} utility"

# other variables
hr="##################################################"

function fappend {
    echo -e "$2">>$1;
}