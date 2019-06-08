# bkup_src2dest_template.sh
# Manoj Thakur    03-Sep-2017 
#
# Description:
#   This script is a template to allow rsync of files and folders. The meat of the code is in doRsync.shinc file.
#
echo "Backup script $0 started: `date`"
scriptDir=`dirname $0`
scriptDir=`cd $scriptDir;pwd`   # 2 line trick to get the absolute path of the script

# In case destination is a remote ssh server, define ssh-key so no password is required
gRemoteServer=""
gRemoteUser=""		# Same as previous
# Remote base directory under which the backup will be copied into
gBackupBaseFolder="/mnt/external_harddrive/backup_base"

# This will get list of all exported file systems (if any)
#gExportFileList=`/usr/sbin/showmount --no-headers -e | awk '{print $1}'`
gExportFileList="\
"
# Specify folders to be excluded from the gExportFileList
# These folders will be backed up at lower frequency (or not at all - e.g. data_nb)
gExportExcludeList="\
"

# Specific local folders (not exported) to be backed up
gStaticFolderBase="/home/user1"
gStaticRelativeFolders="\
	documents \
"
# Other folders with absolute paths
gStaticFolders="\
	/var/log \
"
gExcludeStaticFolders="\
	.documents.cache \
"

# Folder with any config files to backup, under gStaticFolderBase folder
gConfigFolderPrefix="config_files"  # parent folder in for configFiles

gConfigFiles="
"

. ${scriptDir}/doRsync.shinc

echo "Backup script $0 finished: `date`"
