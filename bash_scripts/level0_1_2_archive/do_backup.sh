# Rsync the set of folders specified in $src_folders under $rsync_dir folder.
# Remove the folders specified in $skip_folders under $rsync_dir
#
# The variable "src_folders" is defined in backup.conf in $scriptdir or ~/bin
# These folders are archived in tar.gz files daily using the do_archive.sh script.
# ----------------------------------------------------------------------------
scriptdir=`dirname $0`
scriptdir=`(cd $scriptdir; pwd)`
scritpname=`basename $0`

rsync_dir=/usr/backup/sync/
tar_dir=/usr/backup/tar/
snar_dir=/usr/backup/snar/
logs_dir=/usr/backup/logs

conf_file="backup.conf"
if [ -f $scriptdir/$conf_file ]; then
	conf_filepath=$scriptdir/$conf_file
elif [ -f ~/bin/$conf_file ]; then
	conf_filepath=~/bin/$conf_file
else
	echo "Conf file not found in $scriptdir or ~/bin"
	exit 1
fi

# setup the $src_folders variable, and optionally overwrite the dirs above
. ${conf_filepath}

if [ ! -d $logs_dir ]; then
	mkdir -p $logs_dir
fi
logf=${logs_dir}/do_backup.log

echo "$0 started on `date`" >> ${logf}

mkdir -p $rsync_dir $tar_dir $snar_dir > /dev/null 2>&1

for src in $src_folders
do
	src2=`echo $src | sed 's?^/??g'`
	srcp=`dirname $src2`
        rsd="$rsync_dir/$srcp"
        mkdir -p $rsd > /dev/null 2>&1
	(cd / && rsync --delete -a ${src2} $rsd)
done
for skip in $skip_folders
do
	/bin/rm -fr ${rsync_dir}/${skip}
done
echo "$0 ended on `date`" >> ${logf}
