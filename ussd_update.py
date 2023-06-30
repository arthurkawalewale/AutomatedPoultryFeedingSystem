from flask import Flask, request
import mysql.connector

app = Flask(__name__)
db_connection = mysql.connector.connect(
    host="localhost",
    user="sydney",
    password="poultryiot1",
    database="poultryiot"
)
db_cursor = db_connection.cursor()

# Global variable to store the current menu level
menu_level = "main"

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
    global menu_level  # Use the global variable

    session_id = request.values.get("sessionId")
    text = request.values.get("text", "")

    if text == "":
        response = display_main_menu()
        menu_level = "main"

    elif text == "0":
        response = "END Thank you, bye!."
        menu_level = "main"

    elif text == "1":
        response = get_latest_feed_reading()
        menu_level = "feed"

    elif text == "2":
        response = get_latest_water_reading()
        menu_level = "water"

    elif text == "3":
        response = "CON Enter the date (YY-MM-DD) for the readings:"
        menu_level = "average"

    elif text.startswith("3*"):
        parts = text.split("*")

        if len(parts) == 2:
            date = parts[1]
            response = get_average_readings_for_day(date)
            menu_level = "average"
        else:
            response = "CON Invalid input format. Please enter the date in the correct format."
            menu_level = "average"

    elif text == "#":
        if menu_level == "feed" or menu_level == "water" or menu_level == "average":
            response = display_main_menu()
            menu_level = "main"
        else:
            response = "CON Invalid input. Please try again."
    elif text == "1*#":
        response = display_main_menu()
    elif text == "2*#":
        response = display_main_menu()
    elif text == "3*#":
        response = display_main_menu()

    else:
        response = "CON Invalid input. Please try again."

    return response


if __name__ == "__main__":
    app.run(host="localhost", port=5000, debug=True)
