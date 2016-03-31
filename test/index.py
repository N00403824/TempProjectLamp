#!/usr/bin/python
import sqlite3
import time
from flask import Flask, jsonify, request
import json
import urllib


app = Flask(__name__)
@app.route("/")
def getInfo():

    conn = sqlite3.connect('climate_info.db')

    # This enables column access by name: row['column_name']
    conn.row_factory = sqlite3.Row

    curs = conn.cursor()


    # Confirms that there's a timestamp in the URL Get and then decodes it into a string for usage in the sql query
    if request.args.get('lastData') != None:
    	lastData = urllib.unquote(request.args.get('lastData')).decode('utf8')

    # Retrieves the climate information
    try:	
    	statement = "SELECT timestamp, humidity, temperature FROM climate WHERE timestamp >='%s'" % lastData
    except:
	statement = "SELECT timestamp, humidity, temperature FROM climate"

    curs.execute(statement)

    temperature = curs.fetchall()


    # Retrieves the building information and creates the main output
    curs.execute("SELECT building, room, coord_x, coord_y, coord_z FROM device LIMIT 1")
    building = curs.fetchone()

    climate_info = dict(building)

    # Adds the climate information to the main output
    climate_info["info"] = [ dict(temp) for temp in temperature ]
    climate_info["error"] = None
    climate_info["insert"] = True    

    if request.args.get('username') != None:
    	climate_info["username"] = request.args.get('username')

    #Close the SQL Connection
    conn.commit()
    conn.close()

    return json.dumps(climate_info)

	


@app.route('/request')
def get_test():
    username = request.args.get('username')
    password = request.args.get('password')
    return jsonify(username=request.args.get('username'),
                   password=request.args.get('password'),
                   id=42)

@app.route('/location')
def get_current_user():
    return jsonify(username="admin",
                   email="admin@localhost",
                   id=42)


if __name__ == "__main__":
    app.run(host='0.0.0.0', port=80, debug=True)

class MyFlask(Flask):
    
    def handle_exception(self, e):
        # add all necessary log info here
        log.info("dumping session: %s", session)
        log.info("dumping request: %s", request)
        log.info("dumping request args: %s", request.args)
        return super(MyFlask, self).handle_exception(e)
