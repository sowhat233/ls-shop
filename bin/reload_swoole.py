import os
import time
import datetime
import pyinotify

class WatchHandler2(pyinotify.ProcessEvent):

    def __init__(self):

        # 执行命令
        self.cmd = "./laravels reload"

        # 需要监听的文件路径
        self.watch_file_path = "/var/www/ls-shop/api/"

        self.time = self.getTime()


    def process_IN_MODIFY(self, event):

        if( self.time<self.getTime() ):

            self.time =self.getTime()

            os.system(self.cmd)


    def getTime(self):

        return int(round(time.time() * 2))


def main():

    watch_class = WatchHandler2()

    wm = pyinotify.WatchManager()

    wm.add_watch(watch_class.watch_file_path, pyinotify.ALL_EVENTS, rec=True)

    notifier = pyinotify.Notifier(wm, watch_class)
    notifier.loop()
 
if __name__ == '__main__':
    main()