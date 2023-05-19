import RPi.GPIO as GPIO
import time
import serial
import mysql.connector

# Set up GPIO pins
GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)

# Ultrasonic Sensor 1(trough water)
TRIG1 = 13
ECHO1 = 15

# Ultrasonic Sensor 2
TRIG2 = 16
ECHO2 = 18

# Servo Motor
SERVO = 33

# GSM Module
ser = serial.Serial('/dev/ttyAMA0', 9600, timeout=1)
ser.flush()

# Database
mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="root",
  database="water_module"
)

mycursor = mydb.cursor()

# Set up GPIO pins as input or output
GPIO.setup(TRIG1,GPIO.OUT)
GPIO.setup(ECHO1,GPIO.IN)

GPIO.setup(TRIG2,GPIO.OUT)
GPIO.setup(ECHO2,GPIO.IN)

GPIO.setup(SERVO,GPIO.OUT)
pwm=GPIO.PWM(SERVO,50)
pwm.start(0)

def read_ultrasonic_sensor(TRIG, ECHO):
    
    #pulse_start = 0.0
    #pulse_end = 0.0
    
    GPIO.output(TRIG, GPIO.LOW)
    time.sleep(0.1)
   
   
    GPIO.output(TRIG, GPIO.HIGH)
    time.sleep(0.00001)
    
    GPIO.output(TRIG, GPIO.LOW)

    while GPIO.input(ECHO)==0:
        pulse_start = time.time()
        

    while GPIO.input(ECHO)==1:
        pulse_end = time.time()
         

    pulse_duration = pulse_end - pulse_start
    
    distance = pulse_duration * 17150
    distance = round(distance, 2)
    

    return distance

def send_sms():
    ser.write(b'AT\r')
    time.sleep(1)

    ser.write(b'AT+CMGF=1\r')
    time.sleep(1)

    ser.write(b'AT+CMGS="+265998001217"\r')
    time.sleep(1)

    ser.write(b'Warning: Water level in main reservoir is too low!\r')
    time.sleep(1)

    ser.write(bytes([26]))
    time.sleep(1)
 

def tank_low_level_warning(distance):
    tank_height=18 #set  tank height here
    levl=tank_height-distance
    return levl
def trough_low_level_warning(distance):
    tank_height=10 #set  tank height here
    level=tank_height-distance
    return level

while True:
    # Read water levels from ultrasonic sensors
    level_trough = read_ultrasonic_sensor(TRIG1,ECHO1)
    level_reservoir = read_ultrasonic_sensor(TRIG2, ECHO2)
    
    #calculating trough level
    trough_level=trough_low_level_warning(level_trough)
    #calculating tank level
    tank_level=tank_low_level_warning(level_reservoir)

    print("level in trough",round(trough_level,2))
    print("level in reservoir",round(tank_level,2))
    time.sleep(2)

    # Send data to database
    sql = "INSERT INTO water_level (level_trough, level_reservoir) VALUES (%s, %s)"
    val = (trough_level, tank_level)
    mycursor.execute(sql, val)
    mydb.commit()

    # Check if water level in reservoir is too low
    if tank_level < 6:
       
        print("Calls gsm (send_sms()) method to send text to phone")
        #send_sms()

    # If water level in trough is low, open main water reservoir
    if trough_level < 1 and tank_level > 6:
        pwm.ChangeDutyCycle(7) # rotate servo to open position
        time.sleep(0)
        #pwm
    if trough_level > 1 or tank_level < 6:
        pwm.stop()
    
        
