import json
import mysql.connector
import paho.mqtt.client as mqtt
import RPi.GPIO as GPIO
import time
from Servo_test import control_servo
from sms import send_whatsapp,send_sms

# Set up GPIO pins for servo motor
#SERVO_PIN = 33
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)

#GPIO.setup(SERVO_PIN, GPIO.OUT)
#servo = GPIO.PWM(SERVO_PIN, 50)  # PWM frequency: 50Hz
#servo.start(0)  # Start servo motor

# Ultrasonic Sensor 1 (through water)
TRIG1 = 13
ECHO1 = 15

# Ultrasonic Sensor 2
TRIG2 = 16
ECHO2 = 18

# Set up GPIO pins as input or output
GPIO.setup(TRIG1, GPIO.OUT)
GPIO.setup(ECHO1, GPIO.IN)

GPIO.setup(TRIG2, GPIO.OUT)
GPIO.setup(ECHO2, GPIO.IN)

# Set up GPIO pin for water flow sensor
WATER_FLOW_PIN = 11
GPIO.setup(WATER_FLOW_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)

# MQTT settings
MQTT_BROKER = 'localhost'
MQTT_PORT = 1883
TANK_ULTRASONIC_TOPIC = 'tank_level/tank'
TROUGH_ULTRASONIC_TOPIC = 'trough_level/trough'
WATERFLOW_TOPIC = 'waterflow/volume'

# Initialize MQTT client
client = mqtt.Client()
client.connect(MQTT_BROKER, MQTT_PORT, 60)

# Variables to store water flow data
flow_frequency = 0
flow_rate = 0
total_liters = 0
last_time = time.time()

# Callback function for MQTT connection
def on_connect(client, userdata, flags, rc):
    print('Connected to MQTT broker')
    client.subscribe([(TANK_ULTRASONIC_TOPIC, 0), (TROUGH_ULTRASONIC_TOPIC, 0), (WATERFLOW_TOPIC, 0)])


# Dictionary to store the received values from different sensors
sensor_values = {}

# Callback function for received MQTT messages
def on_message(client, userdata, msg):
    topic = msg.topic
    payload = float(msg.payload.decode("utf-8"))
   
    
    # Extract the sensor name from the topic
    sensor_name = topic.split("/")[-1]
    
    # Store the value in the sensor_values dictionary
    sensor_values[sensor_name] = round(payload,2)
  
    if  "tank" in sensor_values and sensor_values["tank"] < 6:
        print("sending email")
        send_whatsapp("Main tank is running out of water please refill")
        send_sms("Main tank is running out of water please refill")
    elif (("tank" in sensor_values and sensor_values["tank"] )> 6) and (("trough" in sensor_values and sensor_values["trough"])<1):
        control_servo(90)
    elif "trough" in sensor_values and sensor_values["trough"] >0.1:
        control_servo(0)
    print("Trough:","trough" in sensor_values and sensor_values["trough"])
    print("Tank:","tank" in sensor_values and sensor_values["tank"])
    print("Sensor Values",sensor_values)
    # Check if both values are available  
    if len(sensor_values) == 3:
        # Insert the values into the MySQL database
        insert_data_into_mysql(sensor_values["trough"], sensor_values["tank"],sensor_values["volume"])

        # Clear the sensor_values dictionary for the next set of values
        sensor_values.clear()

# Function to read ultrasonic sensor distance
def read_ultrasonic_sensor(TRIG, ECHO):
    distance_add = 0
    for x in range(20):
        try:
            
        
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
            distance_add = distance_add + distance

        except Exception as e:
            pass
    
    average = distance_add/20
    average1 = round(average,2)

    return average
# Function to calculate tank low level warning
def tank_low_level_warning(distance):
    tank_height = 18  # Set tank height here
    level = tank_height - distance
    return level

# Function to calculate trough low level warning
def trough_low_level_warning(distance):
    tank_height = 10  # Set tank height here
    level = tank_height - distance
    return level

# Function to insert data into MySQL database
def insert_data_into_mysql(trough, tank, flow_volume):
    # Connect to MySQL database
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="root",
        database="water_module"
    )
    """db = mysql.connector.connect(
        host="169.254.66.242",
        user="root",
        password="root",
        database="poultry_feeding_system"
    )"""

    # Create a cursor object to execute SQL queries
    cursor = db.cursor()

    # Prepare the SQL query
    query = "INSERT INTO water_level (level_in_trough, level_in_tank, flow_volume) VALUES (%s, %s, %s)"
    values = (round(trough,2), round(tank,2), round(flow_volume,2))
    # Prepare the SQL query
    #query = "INSERT INTO water_readings (reading, water_tank_id) VALUES (%s, %s)"
    #values = (round(tank,2),1)

    try:
        # Execute the SQL query
        cursor.execute(query, values)
        db.commit()
        print("Data inserted successfully!")
    except mysql.connector.Error as error:
        print("Error inserting data into MySQL table:", error)
        db.rollback()

    # Close the cursor and database connection
    cursor.close()
    db.close()

# Callback function for MQTT publish
def on_publish(client, userdata, mid):
    print('Published data to MQTT broker')

client.on_connect = on_connect
client.on_message = on_message
client.on_publish = on_publish

# Add event detection for falling edge on water flow pin
def calculate_flow_rate(channel):
    global flow_frequency, flow_rate, total_liters, last_time

    pulse_time = time.time() - last_time
    flow_frequency = 1.0 / pulse_time
    flow_rate = flow_frequency / 7.5
    total_liters += (flow_rate / 60.0) * pulse_time

    last_time = time.time()

GPIO.add_event_detect(WATER_FLOW_PIN, GPIO.FALLING, callback=calculate_flow_rate)

# Start the MQTT loop
client.loop_start()

# Main loop
try:
    while True:
        # Read water levels from ultrasonic sensors
        level_trough = read_ultrasonic_sensor(TRIG1, ECHO1)
        level_tank = read_ultrasonic_sensor(TRIG2, ECHO2)
        
        # Calculate trough and tank levels
        trough_level = trough_low_level_warning(level_trough)
        tank_level = tank_low_level_warning(level_tank)
        
        # Publish trough and tank levels as MQTT messages
        client.publish(TROUGH_ULTRASONIC_TOPIC, str(round(trough_level,2)))
        client.publish(TANK_ULTRASONIC_TOPIC, str(round(tank_level,2)))
        

        # Publish water flow volume to MQTT broker
        client.publish(WATERFLOW_TOPIC, str(round(total_liters,2)))
        print('Water flow volume:', total_liters)

        # Insert the values into the MySQL database
        insert_data_into_mysql(trough_level, tank_level, total_liters)

        # Clear the total_liters variable for the next interval
        total_liters = 0

        time.sleep(1)  # Adjust the delay as needed

except KeyboardInterrupt:
    pass

# Clean up GPIO
servo.stop()
GPIO.cleanup()
client.disconnect()
client.loop_stop()

