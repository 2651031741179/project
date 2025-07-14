import mysql.connector
from datetime import datetime
import platform
import psutil
import socket

def get_device_info():
    info = {}
    info['DeviceName'] = platform.node()
    info['os'] = platform.system() + " " + platform.release()
    info['CPU'] = platform.processor()
    info['ram'] = round(psutil.virtual_memory().total / (1024 ** 3))  # GB
    info['Storage'] = round(psutil.disk_usage('/').total / (1024 ** 3))  # GB
    info['IP'] = socket.gethostbyname(socket.gethostname())
    return info

def connect_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",  # กรณี XAMPP ไม่มีรหัสผ่าน root
        database="test"
    )

def insert_device(user_id, device_info):
    conn = connect_db()
    cursor = conn.cursor()
    
    sql = """
    INSERT INTO Devices (UserID, DeviceName, os, CPU, ram, Storage, CreatedAt)
    VALUES (%s, %s, %s, %s, %s, %s, %s)
    """
    created_at = datetime.now()
    vals = (
        user_id,
        device_info['DeviceName'],
        device_info['os'],
        device_info['CPU'],
        device_info['ram'],
        device_info['Storage'],
        created_at
    )
    cursor.execute(sql, vals)
    conn.commit()
    print(f"Inserted DeviceID: {cursor.lastrowid}")
    cursor.close()
    conn.close()

if __name__ == "__main__":
    user_id = 1  # กำหนด user ที่ต้องการบันทึก
    device_info = get_device_info()
    print("Device Info:", device_info)
    insert_device(user_id, device_info)
