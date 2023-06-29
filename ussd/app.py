from flask import Flask, request
import africastalking
import mysql.connector

app = Flask(__name__)

# Initialize Africa's Talking
username = "sandbox"
api_key = "f1387a173ccb88aa75ccd332c153fe2097066b26c1cc3bec62470fe6156e79c6"
africastalking.initialize(username, api_key)
ussd = africastalking.USSD
# Connect to MySQL database
def get_water_level():
    try:
        db_connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="poultry_feeding_system"
        )

        db_cursor = db_connection.cursor()
        db_cursor.execute("SELECT reservoir_reading FROM water_readings ORDER BY created_at DESC LIMIT 1")
        row = db_cursor.fetchone()
        db_cursor.close()
        db_connection.close()

        if row:
            return row[0]
        else:
            return "No water level found."
    except mysql.connector.Error as error:
        print(f"Failed to connect to the database: {error}")
        return None

def get_feed_level():
    try:
        db_connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="poultry_feeding_system"
        )

        db_cursor = db_connection.cursor()
        db_cursor.execute("SELECT reservoir_reading FROM feed_readings ORDER BY created_at DESC LIMIT 1")
        row = db_cursor.fetchone()
        db_cursor.close()
        db_connection.close()

        if row:
            return row[0]
        else:
            return "No feed level found."
    except mysql.connector.Error as error:
        print(f"Failed to connect to the database: {error}")
        return None

@app.route('/ussd', methods=['POST'])
def ussd_callback():
    session_id = request.form.get('sessionId')
    phone_number = request.form.get('phoneNumber')
    service_code = request.form.get('serviceCode')
    user_input = request.form.get('text')



    if user_input == '':
        # User just entered the USSD menu
        response="CON Welcome to the Iot water and feed system Menu.\n1. Check water remaining\n2. Check amount of feed remaining  \n"

    elif user_input == '1':
        # Handle Option 1 logic
        water_level = get_water_level()
        response = f"CON Water level is : {water_level} L\n"
        response += "00. Back to menu."
    elif user_input == '1*00' or user_input == '1*00' :
        response="CON Welcome to the Iot water and feed system Menu.\n1. Check water remaining\n2. Check amount of feed remaining  \n"
        
    elif user_input == '2':

        # Handle Option 2 logic
        feed_level = get_feed_level()
       
        response = f"CON Remaining feed is : {feed_level} kg \n"
        response += "00. Back to menu."

    elif user_input == '2*00':
        response="CON Welcome to the Iot water and feed system Menu.\n1. Check water remaining\n2. Check amount of feed remaining  \n"
    

    elif user_input == '2*1*00':
        response="CON Welcome to the Iot water and feed system Menu.\n1. Check water remaining\n2. Check amount of feed remaining  \n"
    
    else:
        # Handle invalid input
        response = "END Invalid input. Please try again."

    return response, 200

if __name__ == '__main__':
    app.run(debug=True)
