from flask import Flask, request
import mysql.connector
import africastalking

# Initialize Africa's Talking
username = "sandbox"
api_key = "0c5670b1ecc4862781f0946da4f5851413f2faef187bafd9bb86c78851cd33f8"
africastalking.initialize(username, api_key)
ussd = africastalking.USSD

app = Flask(__name__)
db_connection = mysql.connector.connect(
    host="localhost",
    user="sydney",
    password="poultryiot1",
    database="poultryiot"
)
db_cursor = db_connection.cursor()

# Menu Levels
MAIN_MENU = "main"
SUBMENU_FEED = "feed"
SUBMENU_WATER = "water"
SUBMENU_AVERAGE = "average"

current_menu = MAIN_MENU


def display_main_menu():
    response = "CON IoT-Based CFS-Main Menu.\n"
    response += "1. Feed remaining\n"
    response += "2. Water remaining\n"
    response += "3. Average readings for a specific day\n"
    response += "0. Exit"

    return response


def get_latest_feed_reading():
    query = "SELECT trough_reading, reservoir_reading, DATE(created_at) AS date_only FROM feed_readings ORDER BY created_at DESC LIMIT 1"
    db_cursor.execute(query)
    result = db_cursor.fetchone()
    if result:
        response = f"CON Feed remaining:\n"
        response += f"1. Feeder: {result[0]} gram(s)\n"
        response += f"2. Main storage: {result[1]} kg(s)\n"
        response += f"#. Back to main menu\n"
    else:
        response = "CON No feed readings found."

    return response


def get_latest_water_reading():
    query = "SELECT trough_reading, reservoir_reading, DATE(created_at) AS date_only FROM water_readings ORDER BY created_at DESC LIMIT 1"
    db_cursor.execute(query)
    result = db_cursor.fetchone()
    if result:
        response = f"CON Water remaining:\n"
        response += f"1. Drinker: {result[0]} ml(s)\n"
        response += f"2. Tank: {result[1]} Ltr(s)\n"
        response += f"#. Back to main menu\n"
    else:
        response = "CON No water readings found."

    return response


def get_average_readings_for_day(date):
    query = "SELECT AVG(trough_reading) FROM feed_readings WHERE DATE(created_at) = %s"
    try:
        db_cursor.execute(query, (date,))
        avg_trough_reading_feed = db_cursor.fetchone()[0]
        if avg_trough_reading_feed is None:
            avg_trough_reading_feed = 0
    except Exception as e:
        avg_trough_reading_feed = 0

    query = "SELECT AVG(trough_reading) FROM water_readings WHERE DATE(created_at) = %s"
    try:
        db_cursor.execute(query, (date,))
        avg_trough_reading_water = db_cursor.fetchone()[0]
        if avg_trough_reading_water is None:
            avg_trough_reading_water = 0
    except Exception as e:
        avg_trough_reading_water = 0

    response = "CON Statistics for {}:\n".format(date)
    response += "1. Water: {:.2f} L(s)\n".format(avg_trough_reading_water)
    response += "2. Feed: {:.2f} KGs\n".format(avg_trough_reading_feed)
    response += "#. Back to main menu\n"

    return response


@app.route("/ussd", methods=["POST", "GET"])
def ussd_callback():
    global current_menu

    session_id = request.values.get("sessionId")
    text = request.values.get("text", "")

    if text == "":
        response = display_main_menu()

    elif text == "0":
        response = "END Thank you, bye!."

    elif text == "1":
        response = get_latest_feed_reading()
        current_menu = SUBMENU_FEED

    elif text == "2":
        response = get_latest_water_reading()
        current_menu = SUBMENU_WATER

    elif text == "3":
        response = "CON Enter the date (YY-MM-DD) for the readings:"
        current_menu = SUBMENU_AVERAGE

    elif current_menu == SUBMENU_AVERAGE and text.startswith("#"):
        if current_menu == MAIN_MENU:
            response = display_main_menu()
            current_menu = MAIN_MENU
        else:
            response = "CON Invalid input. Please try again."  

    elif current_menu == SUBMENU_FEED or current_menu == SUBMENU_WATER or current_menu == SUBMENU_AVERAGE:
        if current_menu == SUBMENU_FEED:
            response = get_latest_feed_reading()
        elif current_menu == SUBMENU_WATER:
            response = get_latest_water_reading()
        elif current_menu == SUBMENU_AVERAGE:
            response = "CON Enter the date (YY-MM-DD) for the readings:"
        
        current_menu = MAIN_MENU

    else:
        response = "CON Invalid input. Please try again."

    return response


if __name__ == "__main__":
    app.run(host="localhost", port=5000, debug=True)
