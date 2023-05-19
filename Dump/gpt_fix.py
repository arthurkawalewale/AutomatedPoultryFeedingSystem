import json
import mysql.connector
import paho.mqtt.client as mqtt
import RPi.GPIO as GPIO
import time

# Set up GPIO pins for servo motor
SERVO_PIN = 33
GPIO.setmode(GPIO.BOARD)
GPIO.setup(SERVO_PIN, GPIO.OUT)
servo = GPIO.PWM(SERVO_PIN, 50)  # PWM frequency: 50Hz
servo.start(0)  # Start servo motor

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

# MQTT settings
MQTT_BROKER = '169.254.209.140'
MQTT_PORT = 1883
TANK_ULTRASONIC_TOPIC = 'tank_level/tank'
TROUGH_ULTRASONIC_TOPIC = 'trough_level/trough'
WATERFLOW_TOPIC = 'waterflow/volume'

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
    sensor_values[sensor_name] = payload
    
    # Check if both values are available
    if len(sensor_values) == 2:
        # Insert the values into the MySQL database
        insert_data_into_mysql(sensor_values["trough"], sensor_values["tank"])

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
        GPIO.output(TRIG, GPIO.LOW)
        time.sleep(0.1)
           
        GPIO.output(TRIG, GPIO.HIGH)
        time.sleep(0.00001)
        GPIO.output(TRIG, GPIO.LOW)

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
def insert_data_into_mysql(trough, tank):
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
    query = "INSERT INTO water_level (level_in_trough, level_in_tank) VALUES (%s, %s)"
    values = (trough, tank)

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

# Set up MQTT client
client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message
client.connect(MQTT_BROKER, MQTT_PORT, 60)

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

        time.sleep(1)  # Adjust the delay as needed

except KeyboardInterrupt:
    pass

# Clean up GPIO
servo.stop()
GPIO.cleanup()
client.disconnect()
client.loop_stop()
