# Create a level 0 archive tar.gz on the 1st of every month and a level 1 archive for every other date
# Also, at any time, if the level 0 was not created on the 1st of that month, also create the
# level 0 archive at that time.
# Level 0 and 1 archives created using the tar --level=0 (or no --level arg for 1) option, and using the
#     .snar files
# The level 1 archives are always incremental from the level 0 archive. 
# Thus the restore is - restore level 0 + latest level 1
#
# The archive is created from the $rsync_dir folder. The files are copied in this folder in
#     another script.
# The tar.gz files are maintained in the $tar_dir folder.
# The $snar_dir folder has the details that allow the level 0/level 1 operation
# -------------------------------------------------------------------------------------------
rsync_dir=/usr/backup/sync/
tar_dir=/usr/backup/tar/
snar_dir=/usr/backup/snar/
logs_dir=/usr/backup/logs

level0_flag=0

if [ ! -d $logs_dir ]; then
	mkdir -p $logs_dir
fi
logf=${logs_dir}/do_archive.log

echo "$0 started on `date`" >> ${logf}

echo "rsync_dir=$rsync_dir    tar_dir=$tar_dir    snar_dir=$snar_dir logs_dir=$logs_dir" >> $logf

dd=`date +%02d`
mmm=`date +%b`
echo "dd = $dd , $mmm"
if [ "x$dd" = "x01" ]; then
	# Means, it is the 1st of a month. Time for level 0 archive
	level0_flag=1
        echo "level 0 as date is 01"
fi

if [ ! -f $tar_dir/archive.01${mmm}.tgz ]; then
	# Means the level 0 archive on the 1st of this month is missing. Time for level 0 archive.
	level0_flag=1
        echo "level 0 as archive.01${mmm}.tgz is missing"
fi

cd $rsync_dir
if [ $level0_flag -eq 1 ]; then
	# Means we are creating a level 0 archive
	echo "`date`: Start level 0 ...."
        mkdir ${tar_dir}/tmp
	# Move previous level 0 archive, to be restored only in case this level 0 archive fails
        mv -f ${tar_dir}/archive.*.tgz ${tar_dir}/tmp
	# Perform the level 0 archive
	tar --create --gzip --file=$tar_dir/archive.01${mmm}.tgz --level=0 --listed-incremental=$snar_dir/archive.snar .
        if [ $? -ne 0 ]; then
		# If it failed for any reason, we will restore the last level 0 archive.
		echo "level 0 failed"
		mv -f ${tar_dir}/tmp/* ${tar_dir}
	fi
	# Delete the tmp folders with the old archives (if present)
	/bin/rm -fr ${tar_dir}/tmp
	echo "`date`: End level 0 ...."
else
	# Means we are creating a level 1 archive
	echo "`date`: Start level 1 ...."
	# Copy the level 0 snar file, so all level 1 archives are incremental from the last level 0 archive
	cp -p $snar_dir/archive.snar $snar_dir/archive.snar.tmp
	# Perform the level 1 archive
	tar --create --gzip --file=$tar_dir/archive.${dd}${mmm}.tgz --listed-incremental=$snar_dir/archive.snar.tmp .
	/bin/rm $snar_dir/archive.snar.tmp
	echo "`date`: End level 1 ...."
fi
echo "$0 ended on `date`" >> ${logf}
