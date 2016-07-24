from flask import Flask, render_template,request, redirect, url_for
from HomeSecurity import *
from tinydb import TinyDB, Query
from threading import Thread
import time

db = TinyDB('db.json').table('HomeSecurity')
HomeDB = Query()
app = Flask(__name__)

# variables for template page (templates/index.html)
home_name = "House Name"
address = "Street Address, City, State, Postal Code"

# variables for the system
windowSensors = range(22, 28+1)
doorSensors = range(30, 33+1)
motionSensors = range(34, 40+1)

## RF433 Transmitter = 10
## RF433 Receiver = 11
## Security Alarm = 13
## Window/Door Sensor = 22 to 33
## Motion PIR Sensor = 34 to 45

def initHome():
    # initialize connection to Arduino
    # if your arduino was running on a serial port other than '/dev/ttyACM0/'
    # declare: a = Arduino(serial_port='/dev/ttyXXXX')
    a = Arduino()
    time.sleep(1)
    
    db.purge()
    a.set_mode(3)
    db.insert({'type': 'Mode', 'val': 3})
    a.set_alarm('d')
    db.insert({'type': 'Alarm', 'val': 3})

    ## Active Sensors
    # Window Sensors
    for i in windowSensors:
        a.set_activate_pin(i,1)
        db.insert({'type': 'ActivePin', 'pin': i, 'val': 1})
    # Door Sensors
    for i in doorSensors:
        a.set_activate_pin(i,1)
        db.insert({'type': 'ActivePin', 'pin': i, 'val': 1})
    # Motion Sensors
    for i in motionSensors:
        a.set_activate_pin(i,1)
        db.insert({'type': 'ActivePin', 'pin': i, 'val': 1})

    for i in windowSensors:
        db.insert({'type': 'WindowDoor', 'pin': i, 'val': 0})
    for i in doorSensors:
        db.insert({'type': 'WindowDoor', 'pin': i, 'val': 0})
    for i in motionSensors:
        db.insert({'type': 'Motion', 'pin': i, 'val': 0})

    # Stay
    for i in range(22,28+1):
        a.set_pin_stay(i,1)
        time.sleep(0.1)
        
    # Away
    for i in range(22,28+1):
        a.set_pin_away(i,1)
        time.sleep(0.1)

    for i in range(30,33+1):
        a.set_pin_away(i,1)
        time.sleep(0.1)

    for i in range(34,40+1):
        a.set_pin_away(i,1)
        time.sleep(0.1)

    # Night
    for i in range(22,28+1):
        a.set_pin_night(i,1)
        time.sleep(0.1)

    for i in range(30,33+1):
        a.set_pin_night(i,1)
        time.sleep(0.1)

    a.set_enable(1)
    db.insert({'type': 'HomeSecurity', 'val': 1})

    # initialize complete
    print 'Home Security initialized'
    return a;

# we are able to make 2 different requests on our webpage
# GET = we just type in the url
# POST = some sort of form submission like a button
@app.route('/', methods = ['POST','GET'])
def init_home():

    """
    # if we make a post request on the webpage aka press button then do stuff
    if request.method == 'POST':
        
        # if we press the turn on button
        if request.form['submit'] == 'Warning':
            print 'Security Alarm: WARNING'
    
            # turn the warning on
            a.set_alarm('w')
    
        # if we press the turn off button
        elif request.form['submit'] == 'Active':
            print 'Security Alarm: DISARM'

            # turn the warning off
            a.set_alarm('a')

        # if we press the turn off button
        elif request.form['submit'] == 'Disarm':
            print 'Security Alarm: DISARM'

            # turn the warning off
            a.set_alarm('d')

        else:
            pass
    """
    
    # the default page to display will be our template with our template variables
    return render_template('index.html', homeName=home_name, address=address)

# unsecure API urls
@app.route('/activate/<pin>/<val>')
def activate(pin, val):
    a.set_activate_pin(pin, val)
    query = db.get((HomeDB.type == 'ActivePin') & (HomeDB.pin == pin))
    return render_template('json.html', query=query)

@app.route('/mode/<val>')
def mode(val):
    a.set_mode(val)
    query = db.get((HomeDB.type == 'Mode'))
    return render_template('json.html', query=query)

@app.route('/alarm/<val>')
def alarm(val):
    a.set_alarm(val)
    query = db.get((HomeDB.type == 'Alarm'))
    return render_template('json.html', query=query)

@app.route('/get/mode')
def getmode():
    query = db.get((HomeDB.type == 'Mode'))
    return render_template('json.html', query=query)

@app.route('/get/alarm')
def getalarm():
    query = db.get((HomeDB.type == 'Alarm'))
    return render_template('json.html', query=query)

@app.route('/get/<pin>')
def get(pin):
    query = db.get((HomeDB.pin == int(pin)) & ((HomeDB.type == 'WindowDoor') | (HomeDB.type == 'Motion')))
    return render_template('json.html', query=query)



# lets launch our webpage!
# do 0.0.0.0 so that we can log into this webpage
# using another computer on the same network later
def runFlask():
    app.run(host='0.0.0.0')

if __name__ == "__main__":

    # initialize home
    a = initHome()
    time.sleep(0.1)
    
    # runs the flask app
    t1 = Thread(target = runFlask)
    t1.setDaemon(True)
    t1.start()

    while True:
        try:
            val = a.get_line()
            print val[0]   # Type
            print val[1]   # Value
            print val[2]   # Pin
            if val[2] == 0:
                db.update({'val': int(val[1])}, HomeDB.type == val[0])
            else:
                db.update({'val': int(val[1])}, (HomeDB.type == val[0]) & (HomeDB.pin == int(val[2])))
        except KeyboardInterrupt:
            print ''
            break # kill for loop

    print 'CLOSING...'
    a.close()