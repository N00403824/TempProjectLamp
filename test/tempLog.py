#!/usr/bin/python

# tempLog.py
# Heidi Fritz
# Log Current Time, Temperature in Celsius
# Puts date, time, temperature in a sqlite3 database(climate_info.db)

import sqlite3  # must do 'sudo apt-get install sqlite'
import os
from crontab import CronTab  # must do 'sudo pip install python-crontab'
import datetime
import glob



# Returns temperature in Celcius
def getTemp(devicefile):
        try:
            fileobj = open(devicefile,'r')
            lines = fileobj.readlines()
            fileobj.close()
        except:
            return None

        # get the status from the end of line 1 
        status = lines[0][-4:-1]

        # if the status is ok, get the temperature from line 2
        if status=="YES":
            #tempstr= lines[1][-6:-1]
	        tempstr = lines[1].split("=",1)[1]
            #tempstr = tempstr.split("=",1)[1]
            tempvalue=float(tempstr)/1000
            print tempvalue
            return tempvalue
        else:
            print "There was an error."
            return None
        
# Creates a database and table to log temperature and time
def logTemp(temp):
        tableName = "climate"
        databaseName = '/home/pi/climate/climate_info.db'
        
	# connect to database
        conn = sqlite3.connect(databaseName)
        print "Opened database successfully."
        print "Currently working in " + databaseName

        # check if the table 'Climate' already exists
        cursor = conn.cursor()
        
        query = cursor.execute("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = '%s';"
                               %tableName)
        exists = cursor.fetchone()[0]  # fetches result of query
        if not(exists):
           # there are no tables named 'Climate'
           conn.execute("CREATE TABLE %s(timestamp NUMERIC NOT NULL DEFAULT( datetime('now','localtime')), temperature REAL);"
                        %tableName)
           print "Table created."
           conn.execute("INSERT INTO %s (temperature)VALUES((?));"
                        %tableName, (temp,))
           conn.commit()
           print "Temp recorded in " + tableName + "."
        else:
           # there is a table named 'Climate'
           conn.execute("INSERT INTO %s (temperature)VALUES((?));"
                        %tableName, (temp,))
           conn.commit()
           print "Temp recorded in" + tableName + "."
              
        conn.close()

        return 0

# Creates a cron job to run this file
def createJob():
        cron = CronTab(user='root')
        job = cron.new(command='sudo python /home/pi/climate/tempLog.py')
        job.minute.every(10)
        job.enable()
        cron.write()
        if cron.render():
            print cron.render()
        print "CronJob created."
        return 0

# Checks for a cron job. Calls createJob() if job exists otherwise calls logTemp()
def run():
        # enable kernel modules
        os.system('sudo modprobe w1-gpio')
        os.system('sudo modprobe w1-therm')

        # search for a device file that starts with 28
        devicelist = glob.glob('/sys/bus/w1/devices/28*')
        if devicelist=='':
            return None
        else:
            # append /w1slave to the device file
            w1devicefile = devicelist[0] + '/w1_slave'

        # get the temperature from the device file
        temperature = getTemp(w1devicefile)
        
        #cron = CronTab(user='root')
        # check if cronjob exists
        #if not cron.render():    
        #    createJob()
        #else:
        logTemp(temperature)
        return 0

run()
