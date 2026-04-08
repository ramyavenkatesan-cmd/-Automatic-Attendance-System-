import cv2
import numpy as np
import face_recognition
import os
from datetime import datetime
import mysql.connector

# DB CONNECTION
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="attendance_system"
)
cursor = db.cursor()

path = r'C:\xampp\htdocs\FaceAttendance\images'
images = []
classNames = []

# LOAD IMAGES
for img in os.listdir(path):
    curImg = cv2.imread(f'{path}/{img}')
    images.append(curImg)
    classNames.append(os.path.splitext(img)[0])

# ENCODING
def findEncodings(images):
    encodeList = []
    for img in images:
        img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
        encode = face_recognition.face_encodings(img)[0]
        encodeList.append(encode)
    return encodeList

encodeListKnown = findEncodings(images)
print("Encoding Complete")

# MARK ATTENDANCE
def markAttendance(name):
    today = datetime.now().strftime('%Y-%m-%d')

    cursor.execute("SELECT id FROM users WHERE name=%s", (name,))
    result = cursor.fetchone()

    if result:
        student_id = result[0]

        cursor.execute("""
            SELECT * FROM attendance 
            WHERE student_id=%s AND date=%s
        """, (student_id, today))

        if cursor.fetchone() is None:
            cursor.execute("""
                INSERT INTO attendance (student_id, subject, date, status)
                VALUES (%s, %s, %s, %s)
            """, (student_id, "DBMS", today, "Present"))
            db.commit()
            print(name, "Marked Present")

# CAMERA
cap = cv2.VideoCapture(0)

while True:
    success, img = cap.read()
    imgS = cv2.resize(img, (0,0), None, 0.25, 0.25)
    imgS = cv2.cvtColor(imgS, cv2.COLOR_BGR2RGB)

    facesCurFrame = face_recognition.face_locations(imgS)
    encodesCurFrame = face_recognition.face_encodings(imgS, facesCurFrame)

    for encodeFace, faceLoc in zip(encodesCurFrame, facesCurFrame):
        matches = face_recognition.compare_faces(encodeListKnown, encodeFace)
        faceDis = face_recognition.face_distance(encodeListKnown, encodeFace)

        matchIndex = np.argmin(faceDis)

        if matches[matchIndex]:
            name = classNames[matchIndex].upper()

            y1,x2,y2,x1 = faceLoc
            y1,x2,y2,x1 = y1*4,x2*4,y2*4,x1*4

            cv2.rectangle(img,(x1,y1),(x2,y2),(0,255,0),2)
            cv2.putText(img,name,(x1,y1-10),
                        cv2.FONT_HERSHEY_SIMPLEX,1,(0,255,0),2)

            markAttendance(name)

    cv2.imshow('Face Attendance', img)

    if cv2.waitKey(1) == 13:
        break

cap.release()
cv2.destroyAllWindows()