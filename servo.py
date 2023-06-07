import time
import RPi.GPIO as GPIO
servo_pin = 33
GPIO.setmode(GPIO.BOARD)
GPIO.setup(servo_pin,GPIO.OUT)
servo = GPIO.PWM(servo_pin,50)
servo.start(0)

def turn_on_servo():
	servo.ChangeDutyCycle(7.5)
	
def turn_off_servo():
	servo.ChangeDutyCycle(0)
	
try:
	while True:
		time.sleep(1)
		
except KeyboardInterrupt:
	servo.stop()
	GPIO.cleanup()

turn_on_servo()
