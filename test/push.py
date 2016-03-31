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

# Confirms that there's a timestamp in the URL Get and then decodes it into a string for usage in the sql query
#if request.args.get('lastData') != None:
#   lastData = urllib.unquote(request.args.get('lastData')).decode('utf8')


# Retrieves the building information and creates the main output
curs.execute("SELECT * FROM device LIMIT 1")
building = curs.fetchone()

climate_info = dict(building)


#Obtain Infomation from Server for lastData
#last_data_request = requests.get("http://cs.newpaltz.edu/~loweb/pi/api/input.php?lastData=true&building="+ climate_info["building"] +"&room="+ climate_info["room"])
last_statement = "http://cs.newpaltz.edu/~loweb/pi/api/input.php?lastData=true&building="+ climate_info["building"] +"&room=%s" % climate_info["room"]   
last_data_request = requests.get(last_statement)
last_data = last_data_request.json()
lastData = last_data["info"]["lastData"]


# Retrieves the climate information
try:	
   statement = "SELECT timestamp, humidity, temperature FROM climate WHERE timestamp >='%s'" % lastData
except:
   statement = "SELECT timestamp, humidity, temperature FROM climate"

curs.execute(statement)

temperature = curs.fetchall()

# Adds the climate information to the main output
climate_info["info"] = [ dict(temp) for temp in temperature ]
climate_info["error"] = None


#Close the SQL Connection
conn.commit()
conn.close()

print json.dumps(climate_info)
