import RPi.GPIO as GPIO
import time
import paho.mqtt.client as mqtt
import smtplib
import mysql.connector
#from servo import control_servo

# Set up GPIO pins for servo motor
SERVO_PIN = 33
GPIO.setmode(GPIO.BOARD)
GPIO.setup(SERVO_PIN, GPIO.OUT)
servo = GPIO.PWM(SERVO_PIN, 50)  # PWM frequency: 50Hz
servo.start(0)  # Start servo motor
# Ultrasonic Sensor 1(trough water)
TRIG1 = 13
ECHO1 = 15

# Ultrasonic Sensor 2
TRIG2 = 16
ECHO2 = 18

# Set up GPIO pins as input or output
GPIO.setup(TRIG1,GPIO.OUT)
GPIO.setup(ECHO1,GPIO.IN)

GPIO.setup(TRIG2,GPIO.OUT)
GPIO.setup(ECHO2,GPIO.IN)

# MQTT settings
MQTT_BROKER = '169.254.209.140'
MQTT_PORT = 1883
TANK_ULTRASONIC_TOPIC = 'tank_level/tank'
TROUGH_ULTRASONIC_TOPIC = 'trough_level/trough'
WATERFLOW_TOPIC = 'waterflow/volume'

# Callback function for MQTT connection
def on_connect(client, userdata, flags, rc):
    print('Connected to MQTT broker')
    client.subscribe([(TANK_ULTRASONIC_TOPIC, 0),(TROUGH_ULTRASONIC_TOPIC, 0), (WATERFLOW_TOPIC, 0)])

# Dictionary to store the received values from different sensors
sensor_values = {}


# Callback function for received MQTT messages
def on_message(client, userdata, msg):
    #Extract data from message
    topic =msg.topic
    payload = float(msg.payload.decode("utf-8"))
   
        # Extract the sensor name from the topic
    sensor_name = topic.split("/")[-1]
    data = json.loads(payload)
    value=data["trough"]
    value1=data["tank"]
    # Store the value in the sensor_values dictionary
    #sensor_values[sensor_name] = float(payload)
    print("value",value)
    print("value1",value1)
    # Check if both values are available
    #print("length",len(sensor_values))
    if len(sensor_values) == 1:
        
        # Insert the values into the MySQL database
        insert_data_into_mysql(sensor_values["trough"], sensor_values["tank"])

        # Clear the sensor_values dictionary for the next set of values
        sensor_values.clear()
    
    #insert_data_to_database(round(payload,2))
    #*************************************
    if msg.topic == TROUGH_ULTRASONIC_TOPIC:
        sensor1_data = payload
        #print('Received Trough level:', round(level_in_trough,2))
        """if level_rounded > 1.00:
            print("calling servo")
            servo.ChangeDutyCycle(7) # rotate servo to open position
            time.sleep(0)
            
        elif level_rounded >= 1.00:
            print("level is cool servo stay idle")
            
        move_servo(level_in_tank)"""
            
    elif msg.topic == TANK_ULTRASONIC_TOPIC:
        sensor2_data = payload
      
    
        #print('Received Tank level:', round(level_in_tank,2))
        """if level_rounded1 >6.00:
            print("Calling send txt method")
            #send_email()
        elif level_rounded1 >=6.00:
            print("level in tank is cool")
            
    elif topic == WATERFLOW_TOPIC:
        volume = float(msg.payload)
        print('Received water flow volume:', volume)
        process_waterflow(volume)
        #send data to db"""
        
# Function to control servo based on distance
def move_servo(dc):
    #duty = angle / 18 + 2
    pwm = GPIO.PWM(SERVO_GPIO, 50)
    pwm.start(dc)
    sleep(1)
    pwm.stop()
    GPIO.output(SERVO_GPIO, False)

# Function to process water flow sensor data
def process_waterflow(volume):
    # Add your code to handle the water flow sensor data here
    print('Water flow volume:', volume)


#*********************************************
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
#*********************************************
def tank_low_level_warning(distance):
    tank_height=18 #set  tank height here
    levl=tank_height-distance
    return levl
def trough_low_level_warning(distance):
    tank_height=10 #set  tank height here
    level=tank_height-distance
    return level
#**********************************************
# Function to insert data into MySQL database
def insert_data_into_mysql(trough, tank):
    # Connect to MySQL database
    db = mysql.connector.connect(
        host="169.254.209.140",
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
        level_trough = read_ultrasonic_sensor(TRIG1,ECHO1)
        level_reservoir = read_ultrasonic_sensor(TRIG2, ECHO2)
        #calculating trough level
        trough_level=trough_low_level_warning(level_trough)
        #calculating tank level
        tank_level=tank_low_level_warning(level_reservoir)
        #print("level in trough",round(trough_level,2))
        #print("level in reservoir",round(tank_level,2))
        #time.sleep(2)
        client.publish(TROUGH_ULTRASONIC_TOPIC, trough_level)
        client.publish(TANK_ULTRASONIC_TOPIC, tank_level)
        #move_servo(distance)
        time.sleep(1)  # Adjust the delay as needed
except KeyboardInterrupt:
    pass

# Clean up GPIO
servo.stop()
GPIO.cleanup()
client.disconnect()
client.loop_stop()
