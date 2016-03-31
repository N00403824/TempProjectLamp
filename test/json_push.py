#!/usr/bin/python
import sqlite3
import time
import json
import urllib
import requests



conn = sqlite3.connect('climate_info.db')

# This enables column access by name: row['column_name']
conn.row_factory = sqlite3.Row
curs = conn.cursor()

# Retrieves the building information and creates the main output
curs.execute("SELECT building, room, coord_x, coord_y, coord_z FROM device LIMIT 1")
building = curs.fetchone()

climate_info = dict(building)


#Obtain Infomation from Server for lastData
#last_data_request = requests.get("http://cs.newpaltz.edu/~loweb/pi/api/input.php?lastData=true&building="+ climate_info["building"] +"&room="+ climate_info["room"])
last_statement = "http://cs.newpaltz.edu/~loweb/pi/api/input.php?lastData=true&building=%s&room=%s" % (climate_info["building"], climate_info["room"])   
last_data_request = requests.get(last_statement)
last_data = last_data_request.json()
lastData = last_data["info"]["lastData"]


# Retrieves the climate information
if lastData is not None:
   statement = "SELECT timestamp, humidity, temperature FROM climate WHERE timestamp >'%s'" % last_data["info"]["lastData"]
else:	
   statement = "SELECT timestamp, humidity, temperature FROM climate"

print statement

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

post_location = "http://cs.newpaltz.edu/~loweb/pi/api/input.php"
post_request = requests.post(post_location, data=test_json)
print "URL Status: " + str(post_request.status_code)
print "URL Response: " + post_request.text
