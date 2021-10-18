import os
import time
import datetime
import pyinotify

class WatchHandler2(pyinotify.ProcessEvent):

    def __init__(self):

        # 执行命令
        self.cmd = "php /var/www/ls-shop/api/bin/laravels reload"

        # 需要监听的文件路径
        self.watch_file_path = "/var/www/ls-shop/api/"

        self.time = self.getTime()


    def process_IN_MODIFY(self, event):

        path_name = event.pathname

        #如果是.log或者.xml 则不处理
        if(path_name.find(".log") != -1 or path_name.find(".xml") != -1):

            pass

        else:

            if( self.time<self.getTime() ):

                self.time = self.getTime()

                os.system(self.cmd)


    def getTime(self):

        return int(time.time())


def main():

    watch_class = WatchHandler2()

    wm = pyinotify.WatchManager()

    wm.add_watch(watch_class.watch_file_path, pyinotify.ALL_EVENTS, rec=True)

    notifier = pyinotify.Notifier(wm, watch_class)
    notifier.loop()
 
if __name__ == '__main__':
    main()