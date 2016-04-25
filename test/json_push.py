#!/usr/bin/python
import sqlite3
import time
import json
import urllib
import requests
from crontab import CronTab  # must do 'sudo pip install python-crontab'


def json_push():

	conn = sqlite3.connect('/home/pi/climate/climate_info.db')

	# This enables column access by name: row['column_name']
	conn.row_factory = sqlite3.Row
	curs = conn.cursor()

	# Retrieves the building information and creates the main output  
	curs.execute("SELECT building, room, coord_x, coord_y, coord_z, lamp, postal_code, latitude, longitude FROM device LIMIT 1")
	building = curs.fetchone()

	climate_info = dict(building)


	#Obtain Infomation from Server for lastData
	last_statement = "%s?lastData=true&building=%s&room=%s" % (climate_info["lamp"], climate_info["building"], climate_info["room"])   
	last_data_request = requests.get(last_statement)
	last_data = last_data_request.json()
	lastData = last_data["info"]["lastData"]


	# Retrieves the climate information
	if lastData is not None:
   	   statement = "SELECT timestamp, humidity, temperature FROM climate WHERE timestamp >'%s'" % last_data["info"]["lastData"]
	else:	
	   statement = "SELECT timestamp, humidity, temperature FROM climate"



	curs.execute(statement)

	temperature = curs.fetchall()

	# Adds the climate information to the main output
	climate_info["info"] = [ dict(temp) for temp in temperature ]
	climate_info["error"] = None
	climate_info["insert"] = True

	#Close the SQL Connection
	conn.commit()
	conn.close()

	test_json = json.dumps(climate_info)

	print test_json

	# Create the JSON Post to Push to the LAMP server
	#post_location = "http://cs.newpaltz.edu/~loweb/pi/"
	post_location = "%s" % climate_info["lamp"]
	post_request = requests.post(post_location, data=test_json)
	print "URL Status: " + str(post_request.status_code)
	print "URL Response: " + post_request.text






# Creates a cron job to run this file
def createJob():
	cron = CronTab(user='pi')
	job = cron.new(command='python /home/pi/climate/json_push.py')
	job.minute.every(10)
	job.enable()
	cron.write()
	if cron.render():
	   print cron.render()
	print "CronJob created."
	return 0


# Main Part of the program
def run():

	cron = CronTab(user='pi')
	# check if cronjob exists
	if not cron.render():
	   createJob()
	else:
	   json_push()
	return 0

run()
