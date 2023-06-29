import RPi.GPIO as GPIO
import time
servo_pin = 33


def control_servo(duty_cycle):
    # Set up GPIO
    GPIO.setmode(GPIO.BOARD)
    GPIO.setup(servo_pin, GPIO.OUT)
    # Create PWM object
    pwm = GPIO.PWM(servo_pin, 50)  # GPIO 18, PWM frequency = 50Hz

    try:
        # Start PWM with given duty cycle
        pwm.start(duty_cycle)

        # Wait for a few seconds
        time.sleep(5)

        # Stop PWM
        pwm.stop()

    finally:
        # Clean up GPIO
        GPIO.cleanup()

# Example usage
#duty_cycle = 2.5  # Set the desired duty cycle (between 2.5 and 12.5)
control_servo(12.5)