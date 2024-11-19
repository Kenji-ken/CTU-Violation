#!/usr/bin/python3
# read_rfid.py
from mfrc522 import SimpleMFRC522
import RPi.GPIO as GPIO
import json
import sys

def read_card():
    reader = SimpleMFRC522()
    try:
        id, text = reader.read_no_block()
        if id:
            return {
                "card_present": True,
                "card_id": str(id),
                "card_text": text.strip() if text else ""
            }
        return {"card_present": False}
    finally:
        GPIO.cleanup()

if __name__ == "__main__":
    result = read_card()
    print(json.dumps(result))