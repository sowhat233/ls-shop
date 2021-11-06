#!/usr/bin/python

import os
import time
import datetime
import pyinotify


# 启动  nohup python3 -u /var/www/ls-shop/bin/reload_swoole.py > /var/nohup.out 2>&1 &
# 查看任务 jobs
# 杀掉进程 kill %job_num


class WatchHandler(pyinotify.ProcessEvent):

    def __init__(self):

        self.base_path = "/var/www/ls-shop/api"

        self.time = self.getTime()

        # 需要执行的命令
        self.cmd = "php " + self.base_path + "/bin/laravels reload"

        # 需要监听的文件路径
        self.watch_file_path = self.base_path

    def process_IN_MODIFY(self, event):

        path_name = event.pathname

        # 如果是.log 或者.xml 则不处理
        if (path_name.find(".log") != -1 or path_name.find(".xml") != -1):

            pass

        else:

            now_time = self.getTime()

            if (self.time < now_time):
                self.time = now_time
                print('laraves reload...')
                os.system(self.cmd)

    def getTime(self):

        return int(time.time())


def main():

    watch_class = WatchHandler()

    # 停止laravels
    print('停止laravels...')
    os.system("php " + watch_class.base_path + "/bin/laravels stop")

    # 启动laravels
    print('启动laravels...')
    os.system("php " + watch_class.base_path + "/bin/laravels start -d")

    wm = pyinotify.WatchManager()

    wm.add_watch(watch_class.watch_file_path, pyinotify.ALL_EVENTS, rec=True)

    print('开始监听...')

    notifier = pyinotify.Notifier(wm, watch_class)
    notifier.loop()


if __name__ == '__main__':
    main()
