import json
import mysql.connector
import paho.mqtt.client as mqtt
import RPi.GPIO as GPIO
import time
from servo import control_servo

# Set up GPIO pins for servo motor
GPIO.setmode(GPIO.BOARD)

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
    payload = float(msg.payload)
    
    # Extract the sensor name from the topic
    sensor_name = topic.split("/")[-1]
    
    # Store the value in the sensor_values dictionary
    sensor_values[sensor_name] = round(payload, 2)
  
    if "tank" in sensor_values and sensor_values["tank"] <= 6:
        print("sending email")
    elif "tank" in sensor_values and 6 < sensor_values["tank"] < 1:
        control_servo(7.5)
    elif "trough" in sensor_values and sensor_values["trough"] >= 0.40:
        control_servo(2.5)
    
    # Check if both values are available
    if len(sensor_values) == 3:
        # Insert the values into the MySQL database
        insert_data_into_mysql(sensor_values["trough"], sensor_values["tank"], sensor_values["volume"])

        # Clear the sensor_values dictionary for the next set of values
        sensor_values.clear()

        

# Function to control servo based on distance
def move_servo(dc):
    pwm = GPIO.PWM(SERVO_PIN, 50)
    pwm.start(dc)
    time.sleep(1)
    pwm.stop()

# Function to read ultrasonic sensor distance
def read_ultrasonic_sensor(TRIG, ECHO):
    distance_sum = 0
    for _ in range(20):
        GPIO.output(TRIG, False)
        time.sleep(0.1)
           
        GPIO.output(TRIG, True)
        time.sleep(0.00001)
        GPIO.output(TRIG, False)

        while GPIO.input(ECHO) == 0:
            pulse_start = time.time()

        while GPIO.input(ECHO) == 1:
            pulse_end = time.time()

        pulse_duration = pulse_end - pulse_start
        distance = pulse_duration * 17150
        distance_sum += distance

    average_distance = distance_sum / 20
    return round(average_distance, 2)

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

    # Create a cursor object to execute SQL queries
    cursor = db.cursor()

    # Prepare the SQL query
    query = "INSERT INTO water_level (level_in_trough, level_in_tank, flow_volume) VALUES (%s, %s, %s)"
    values = (round(trough, 2), round(tank, 2), round(flow_volume, 2))

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
        client.publish(TROUGH_ULTRASONIC_TOPIC, str(round(trough_level, 2)))
        client.publish(TANK_ULTRASONIC_TOPIC, str(round(tank_level, 2)))
        

        # Publish water flow volume to MQTT broker
        client.publish(WATERFLOW_TOPIC, str(round(total_liters, 2)))
        print('Water flow volume:', total_liters)

        # Insert the values into the MySQL database
        insert_data_into_mysql(trough_level, tank_level, total_liters)

        # Clear the total_liters variable for the next interval
        total_liters = 0

        time.sleep(1)  # Adjust the delay as needed

except KeyboardInterrupt:
    pass

# Clean up GPIO
GPIO.cleanup()
client.disconnect()
client.loop_stop()
