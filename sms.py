from twilio.rest import Client

# Twilio account credentials
account_sid = 'AC5eff4949f15baeb5692c838c0c4dba91'
auth_token = '311ad2c7e36457ea200f3cc18d538f91'

# Twilio phone number (for SMS)
twilio_phone_number = '+13614507868'

# Twilio WhatsApp number
twilio_whatsapp_number = '+14155238886'

# Recipient phone number
recipient_phone_number = '+265888088600'

# Recipient WhatsApp number
recipient_whatsapp_number = '+265998001217'

# Create a Twilio client
client = Client(account_sid, auth_token)

# Send SMS
def send_sms(message):
    sms_message = client.messages.create(
        body=message,
        from_=twilio_phone_number,
        to=recipient_phone_number
    )
    print("SMS sent successfully. SID:", sms_message.sid)

# Send WhatsApp message
def send_whatsapp(message):
    whatsapp_message = client.messages.create(
        body=message,
        from_=f'whatsapp:{twilio_whatsapp_number}',
        to=f'whatsapp:{recipient_whatsapp_number}'
    )
    print("WhatsApp message sent successfully. SID:", whatsapp_message.sid)

# Test sending SMS
#send_sms("Hello, this is an SMS message!")

# Test sending WhatsApp message
#send_whatsapp("Hello, this is a WhatsApp message!")
