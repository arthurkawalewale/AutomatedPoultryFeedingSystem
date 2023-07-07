import RPi.GPIO as GPIO
import time

FLOW_SENSOR_PIN = 11 # GPIO pin connected to the flow sensor
CALIBRATION_FACTOR = 4.5  # Calibration factor for flow sensor (adjust as per your sensor)

flow_frequency = 0
total_liters = 0

GPIO.setmode(GPIO.BOARD)
GPIO.setup(FLOW_SENSOR_PIN, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def calculate_water_volume(flow_frequency):
    global total_liters
    liters = (flow_frequency / CALIBRATION_FACTOR) * 0.01  # Convert frequency to liters
    total_liters += liters
    return total_liters

def pulse_callback(channel):
    global flow_frequency
    flow_frequency += 1

GPIO.add_event_detect(FLOW_SENSOR_PIN, GPIO.FALLING, callback=pulse_callback)

try:
    while True:
        time.sleep(1)
        water_volume = calculate_water_volume(flow_frequency)
        print(f"Water volume: {water_volume} liters")
        flow_frequency = 0

except KeyboardInterrupt:
    GPIO.cleanup()
