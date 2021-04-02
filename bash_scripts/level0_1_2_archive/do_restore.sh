# Restore from the level 0 and level 1 archives created using the combination of the do_backup.sh
#    and the do_archive.sh scripts.
# The specific file path to be restored, is specified as the argument to this script ($1)
# At this time, we just identify which .tar.gz file contains the filepath - for a manual restore.
# ----------------------------------------------------------------------------------------------
rsync_dir=/usr/backup/sync/
tar_dir=/usr/backup/tar/
snar_dir=/usr/backup/snar/

filepath=$1

dd=31
found=0
exitloop=0
while [ $exitloop -eq 0 ]; do
	af=`/bin/ls $tar_dir/archive.${dd}[A-Z]*.tgz $tar_dir/archive.0${dd}[A-Z]*.tgz 2> /dev/null`
	echo "$dd: $af"
	if [ -f $af ]; then
		tar --list --incremental --verbose --verbose --file=$af 2> /dev/null | grep $filepath | grep "^Y"
		if [ $? -eq 0 ]; then
			echo "Found in $af"
			found=1
			exitloop=1
		fi
	fi
	dd=`expr $dd - 1`
	if [ $dd -lt 1 ]; then
		exitloop=1
	fi
done
