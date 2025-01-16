## Borgbackup Viewer
This container allows to view the local borg repository in a web session. It also allows you to restore files and folders from the backup by using desktop programs in a web browser.

### Notes
- After adding and starting the container, you need to visit `https://ip.address.of.this.server:5800` in order to log in with the user `nextcloud` and the password that you can retrieve when running `sudo docker inspect nextcloud-aio-borgbackup-viewer | grep WEB_AUTHENTICATION_PASSWORD`.
- Then, you should see a terminal. There type in `borg mount /mnt/borgbackup/borg /tmp/borg` to mount the backup archive. You need to type in the password for borg next that is shown in the AIO interface. Afterwards type in `nautilus /tmp/borg` which will show a file explorer and allows you to see all the files. You can then copy files and folders back to ...
- After you are done with the operation, you should remove the container for better security again from the stack: https://github.com/nextcloud/all-in-one/tree/main/community-containers#how-to-remove-containers-from-aios-stack
- See https://github.com/nextcloud/all-in-one/tree/main/community-containers#community-containers how to add it to the AIO stack

### Repository
https://github.com/szaimen/aio-borgbackup-viewer

### Maintainer
https://github.com/szaimen

