import RPi.GPIO as GPIO
import time

# Set the GPIO pin number for the water sensor
water_sensor_pin = 13

def setup():
    GPIO.setmode(GPIO.BOARD)
    GPIO.setup(water_sensor_pin, GPIO.IN)

def read_water_level():
    return GPIO.input(water_sensor_pin)

def print_water_level(water_level):
    if water_level == GPIO.LOW:
        print("Water level: Low")
    else:
        print("Water level: High")

def cleanup():
    GPIO.cleanup()

# Main program
if __name__ == '__main__':
    try:
        setup()
       
        while True:
            water_level = read_water_level()
            print_water_level(water_level)
           
            time.sleep(1)
           
    except KeyboardInterrupt:
        cleanup()

