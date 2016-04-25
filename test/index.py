#!/usr/bin/python
import sqlite3
import time
from flask import Flask, jsonify, request, render_template, redirect, url_for
import json
import urllib


app = Flask(__name__)
@app.route("/")
def getInfo():

    conn = sqlite3.connect('/home/pi/climate/climate_info.db')

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

@app.route('/admin', methods=['GET', 'POST'])
def login():
    error = None
    if request.method == 'POST':
       user_info = { "username": request.form['username'],
                     "password": request.form['password']  }

       # Check the username and password.
       if request.form['username'] != 'admin' or request.form['password'] != 'admin':
          error = 'Invalid Credentials. Please try again.'
       else:
          return redirect(url_for('piInfo'))
    
    return render_template('login.html', error=error)	

@app.route('/info', methods=['GET', 'POST'])
def piInfo():

    conn = sqlite3.connect('/home/pi/climate/climate_info.db')

    # This enables column access by name: row['column_name']
    conn.row_factory = sqlite3.Row

    curs = conn.cursor()   

    if request.method == 'POST':

         error = None

         statement = None

         #Update location data
         if request.form.has_key('inputLocation'):

             statement = "Update device SET building ='%s', room = '%s'  WHERE id='1'"  % (request.form['building'], request.form['room'])
        
	     #else:
         #Update NWS data elif
         elif request.form.has_key('inputGPS'):

             statement = "Update device SET postal_code ='%s', latitude ='%s', longitude ='%s'  WHERE id='1'"  % (request.form['zip'], request.form['latitude'], request.form['longitude'])


         #Update LAMP data 
         elif request.form.has_key('inputLamp'):

              statement = "Update device SET lamp ='%s' WHERE id='1'"  % request.form['lamp']

         #Clear all climate data from the Pi Database.
         elif request.form.has_key('inputClearClimate'):
              if request.form.has_key('deleteClimate'):
                 if request.form.has_key('deleteClimate'):
                    if request.form['deleteClimate'] == 'true':
                       statement  = "DELETE FROM climate"
         else:
             statement = None

         #Update database
         output = {"input": request.form, "statement": statement, "error": error}

         # If statement is not null the local database will be updated with the new information
         if statement != None:
            curs.execute("%s" % statement)
            output["results"] = "Statement Happened"


            #Close the SQL Connection
            conn.commit()
            conn.close()

         #Update password data




         return json.dumps(output)
	
    else:
        # Retrieves the building information and creates the main output
        curs.execute("SELECT building, room, coord_x, coord_y, coord_z, lamp, postal_code, latitude, longitude FROM device LIMIT 1")
        pi_info = curs.fetchone()

        information = dict(pi_info)

    #Close the SQL Connection
    conn.commit()
    conn.close()

    return render_template('info.html', **information)




if __name__ == "__main__":
    app.run(host='0.0.0.0', port=80, debug=True)

class MyFlask(Flask):
    
    def handle_exception(self, e):
        # add all necessary log info here
        log.info("dumping session: %s", session)
        log.info("dumping request: %s", request)
        log.info("dumping request args: %s", request.args)
        return super(MyFlask, self).handle_exception(e)
