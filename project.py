import mysql.connector
import RPi.GPIO as GPIO
import time
from Servo_test import control_servo,servo_stop
from feed_Servo_test import feed_control_servo
from sms import send_whatsapp,send_sms
import waterflow
import threading
# Set up GPIO pins for servo motor

water_sensor_pin = 13
# GPIO pin connected to the flow sensor
FLOW_SENSOR_PIN = 11
# Calibration factor for flow sensor 
CALIBRATION_FACTOR = 4.5  
flow_frequency = 0
total_liters = 0

GPIO.setmode(GPIO.BOARD)
GPIO.setup(FLOW_SENSOR_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)
GPIO.setup(water_sensor_pin, GPIO.IN)
#GPIO.setup(SERVO_PIN, GPIO.OUT)

# Ultrasonicwateer Sensor 1 (through water)
TRIG1 = 13
ECHO1 = 15

# Ultrasonic water Sensor 2
TRIG2 = 16
ECHO2 = 18

# Ultrasonic feed Sensor 1 (through water)
TRIG3 = 22
ECHO3 = 24

# Ultrasonic feed Sensor 2
TRIG4 = 31
ECHO4 = 33

# Set up GPIO pins as input or output
GPIO.setup(TRIG1, GPIO.OUT)
GPIO.setup(ECHO1, GPIO.IN)

GPIO.setup(TRIG2, GPIO.OUT)
GPIO.setup(ECHO2, GPIO.IN)

GPIO.setup(TRIG3, GPIO.OUT)
GPIO.setup(ECHO3, GPIO.IN)

GPIO.setup(TRIG4, GPIO.OUT)
GPIO.setup(ECHO4, GPIO.IN)

# Set up GPIO pin for water flow sensor
WATER_FLOW_PIN = 11
GPIO.setup(WATER_FLOW_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)

# Variables to store water flow data
flow_frequency = 0
flow_rate = 0
total_liters = 0
last_time = time.time()
# Function to read ultrasonic sensor distance
# def read_ultrasonic_sensor(TRIG, ECHO):
#     distance_add = 0
#     for x in range(20):
#     try:
#         GPIO.output(TRIG, GPIO.LOW)
#         time.sleep(0.1)
#            
#            
#         GPIO.output(TRIG, GPIO.HIGH)
#         time.sleep(0.00001)
#             
#         GPIO.output(TRIG, GPIO.LOW)
#         while GPIO.input(ECHO)==0:
#             pulse_start = time.time()
#         while GPIO.input(ECHO)==1:
#             pulse_end = time.time()
#             
#         pulse_duration = pulse_end - pulse_start
#         distance = pulse_duration * 17150
# #         distance_add = distance_add + distance
#     except Exception as e:
#             pass   
#     #average = distance_add/20
#     #average1 = round(average,2)
#     return distance

print ("Waiting For Sensor To Settle")
time.sleep(1) #settling time 

def read_ultrasonic_sensor(TRIG, ECHO):
	dist_add = 0
	for x in range(20):
		try:
			GPIO.output(TRIG, True)
			time.sleep(0.00001)
			GPIO.output(TRIG, False)

			while GPIO.input(ECHO)==0:
				pulse_start = time.time()

			while GPIO.input(ECHO)==1:
				pulse_end = time.time()

			pulse_duration = pulse_end - pulse_start
			
			distance = pulse_duration * 17150

			distance = round(distance, 3)
		
			dist_add = dist_add + distance
			#print "dist_add: ", dist_add
			time.sleep(.1) # 100ms interval between readings
		
		except Exception as e: 
		
			pass
	
	avg_dist=dist_add/20
	dist=round(avg_dist,3)
	#print ("dist: ", dist)
	return dist

# Function to calculate tank low level warning
def tank_low_level_warning(distance):
    if distance >= 0 and distance <=14.00:
        tank_height = 14  # Set tank height here
        level = tank_height - distance
        if(level <  4):
            print("Tank level low sending sms to user : ", level)
            send_sms("Main water tank is running out of water")
            send_whatsapp("Main water tank is running out of water")
        else:
            print("Tank level ok")
            send_sms("Tank Level ok")
            send_whatsapp("Tank Level ok")
        return level

# Function to calculate trough low level warning
def trough_low_level_warning(distance):
    if distance >=0 and distance <=10.00:
        tank_height = 10  # Set tank height here
        level = tank_height - distance
        if(level <  1):
            print("Drinker level low  opening valve: ", level)
            control_servo(80)
        elif level >=2:
            print("Drinker level high  closing valve: ", level)
            control_servo(0)
            
        else:
            print("level ok")
            send_sms("Level ok")
            send_whatsapp("Level ok")
        return level

# Function to calculate tank low level warning
def reservior_low_level_warning(distance):
    if distance >=0 and distance <=32.00:
        tank_height = 32.00  # Set tank height here
        level = tank_height - distance
    return level

# Function to calculate trough low level warning
def feeder_low_level_warning(distance):
    if distance >=0 and distance <= 27.00:
        tank_height = 27.00  # Set tank height here
        level = tank_height - distance
    return level

# Function to insert data into MySQL database
def insert_data_into_mysql(trough, tank, feeder ,reservior):
    # Connect to MySQL database
    """db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="root",
        database="water_module"
    )"""
    db = mysql.connector.connect(
        host="169.254.66.242",
        user="root",
        password="root",
        database="poultry_feeding_system"
    )

    # Create a cursor object to execute SQL queries
    cursor = db.cursor()

    # Prepare the SQL query
    query1 = "INSERT INTO water_readings (trough_reading, reservoir_reading, water_model_id) VALUES (%s, %s, %s)"
    values1 = (round(trough,2), round(tank,2),1)
    # Prepare the SQL query
    
    query2 = "INSERT INTO feed_readings (trough_reading,reservoir_reading,feed_model_id) VALUES (%s, %s,%s)"
    values2 = (round(feeder,2),round(reservior,2),1)

    try:
        # Execute the SQL query
        cursor.execute(query1, values1)
        cursor.execute(query2, values2)
        db.commit()
        print("Data inserted successfully!")
    except mysql.connector.Error as error:
        print("Error inserting data into MySQL table:", error)
        db.rollback()

    # Close the cursor and database connection
    cursor.close()
    db.close()

# Add event detection for falling edge on water flow pin
def calculate_water_volume(flow_frequency):
    global total_liters
    liters = (flow_frequency / CALIBRATION_FACTOR) * 0.01  # Convert frequency to liters
    total_liters += liters
    return total_liters

def pulse_callback(channel):
    global flow_frequency
    flow_frequency += 1

GPIO.add_event_detect(FLOW_SENSOR_PIN, GPIO.FALLING, callback=pulse_callback)
def read_water_level():
    value=GPIO.input(water_sensor_pin)
    return value

"""
water_volume = waterflow.calculate_water_volume(flow_frequency)
watervolume=round(water_volume,1)
print(f"Water volume: {watervolume} liters")
flow_frequency = 0
"""
def main():
    water_trough = read_ultrasonic_sensor(TRIG1, ECHO1)
    water_tank = read_ultrasonic_sensor(TRIG2, ECHO2)
    drinker_level = trough_low_level_warning(water_trough)
    tank_level = tank_low_level_warning(water_tank)
    # Read water levels from ultrasonic sensors
    feed_reservior = read_ultrasonic_sensor(TRIG3, ECHO3)
    feed_trough = read_ultrasonic_sensor(TRIG4, ECHO4)

    feed_storage_level = reservior_low_level_warning(feed_reservior)
    feeder_level = feeder_low_level_warning(feed_trough)
    
    print ("Water Tank level : ", tank_level)
    print ("Drinker level : ", drinker_level)
    print ("Feed storage level : ", feed_storage_level)
    print ("Feeder level : ", feeder_level)
    insert_data_into_mysql(drinker_level, tank_level, feed_storage_level,feeder_level)
if __name__ == '__main__':
    main()

    