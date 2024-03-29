import RPi.GPIO as GPIO
import time

# Set up GPIO pins for servo motor
servo_pin = 12  # Change this to the appropriate GPIO pin number

stop_flag=False
# Function to control servo based on angle
def control_servo(angle):
    global stop_flag
    
    GPIO.setmode(GPIO.BOARD)
    GPIO.setup(servo_pin, GPIO.OUT)
    while not stop_flag:
        
        # Map the angle to the servo pulse width range
        angle = max(0, min(180, angle))  # Ensure the angle is within 0-180 degrees
        pulse_width = (2.5 + angle / 18.0)  # Map angle to pulse width range of 0.5ms-2.5ms
        
        # Generate the PWM signal with the calculated pulse width
        pwm = GPIO.PWM(servo_pin, 50)  # GPIO pin, PWM frequency = 50Hz
        pwm.start(pulse_width)
        time.sleep(0.1)  # Adjust the delay as needed
        pwm.stop()
def servo_stop():
    global stop_flag
    stop_flag=True

# Example usage
#angle = 90  # Specify the desired angle (0-180 degrees)
# control_servo(45)
