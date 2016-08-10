"""
A library to interface Arduino through serial connection with customized Home Security features
"""
import serial, re

class Arduino():
    """
    Models an Arduino connection
    """

    def __init__(self, serial_port='/dev/ttyACM0', baud_rate=57600,
            read_timeout=0.1):
        """
        Initializes the serial connection to the Arduino board
        """
        print 'Connecting to serial port'
        self.conn = serial.Serial(serial_port, baud_rate)
        #self.conn.timeout = read_timeout # Timeout for readline()

    def set_enable(self, enabled):
        """
        Performs a mode operation on HomeSecurity
        Internally sends 'E{enabled}' where mode could be:
        - T for enabled
        - F for disabled
        """
        command = (''.join(('E',str(enabled)))).encode()
        #print 'set_enable =',command,(''.join(('E',str(enabled))))
        self.conn.write(command)
    
    def set_activate_pin(self, pin, val):
        """
        Activate a digitalPin on HomeSecurity
        Internally sends 'P{pin}:{val}' where val could be:
        - T for TRUE
        - F for FALSE
        """
        command = (''.join(('P' + str(pin) + ':', str(val)))).encode()
        #print 'set_activate_pin =',command,(''.join(('P' + str(pin),str(val))))
        self.conn.write(command)

    def set_mode(self, mode):
        """
        Performs a mode operation on HomeSecurity
        Internally sends 'M{mode}' where mode could be:
        - 0 for STAY
        - 1 for AWAY
        - 2 for NIGHT
        - 3 for DISARM
        """
        command = (''.join(('M',str(mode)))).encode()
        #print 'set_mode =',command,(''.join(('M',str(mode))))
        self.conn.write(command)

    def set_alarm(self, alarm):
        """
        Performs an alarm operation on HomeSecurity
        Internally sends 's{alarm}' where mode could be:
        - 1 for WARNING
        - 2 for ACTIVE
        - 0 for DISARM
        """
        command = (''.join(('s',str(alarm)))).encode()
        #print 'set_alarm =',command,(''.join(('s',str(alarm))))
        self.conn.write(command)

    def set_pin_stay(self, pin, val):
        """
        Performs an alarm operation on HomeSecurity
        Internally sends 'S{pin}:{val}' where val could be:
        - 1 for TRUE
        - 0 for FALSE
        """
        command = (''.join(('S' + str(pin) + ':',str(val)))).encode()
        #print 'set_pin_stay =',command,(''.join(('S' + str(pin),str(val))))
        self.conn.write(command)

    def set_pin_away(self, pin, val):
        """
        Performs an alarm operation on HomeSecurity
        Internally sends 'A{pin}:{val}' where val could be:
        - 1 for TRUE
        - 0 for FALSE
        """
        command = (''.join(('A' + str(pin) + ':',str(val)))).encode()
        #print 'set_pin_away =',command,(''.join(('A' + str(pin),str(val))))
        self.conn.write(command)

    def set_pin_night(self, pin, val):
        """
        Performs an alarm operation on HomeSecurity
        Internally sends 'N{pin}:{val}' where val could be:
        - 1 for TRUE
        - 0 for FALSE
        """
        command = (''.join(('N' + str(pin) + ':',str(val)))).encode()
        #print 'set_pin_night =',command,(''.join(('N' + str(pin),str(val))))
        self.conn.write(command)

    def get_line(self):
        """
        Grabs the string data from the Arduino and return that value
        """
        line_received = self.conn.readline().decode().strip()
        header, value = line_received.split(':')
        match = re.match(r"([a-z]+)([0-9]+)", header, re.I)
        #print '- = Received = -'
        if match:
            items = match.groups()
            #print 'Type: ' + items[0]
            #print 'Val:  ' + value
            #print 'Pin:  ' + items[1]
            return items[0], value, items[1]
        else:
            #print 'Type: ' + header
            #print 'Val:  ' + value
            return header, value
    
    def close(self):
        """
        To ensure we are properly closing our connection to the
        Arduino device. 
        """
        self.conn.close()
        print 'Connection to Arduino closed'
