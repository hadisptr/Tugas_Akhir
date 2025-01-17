from flask import Flask, render_template, request, jsonify
import cv2
import numpy as np
from keras.preprocessing import image
import warnings
from keras.models import load_model
import base64
import webbrowser

warnings.filterwarnings("ignore")

app = Flask(__name__)

# Load the model
model = load_model("best_model.h5")

# Initialize face detector using Haar Cascade
face_haar_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

emotions = ('angry', 'disgust', 'fear', 'happy', 'sad', 'surprise', 'neutral')

emotion_counts = {'angry': 0, 'disgust': 0, 'fear': 0, 'happy': 0, 'sad': 0, 'surprise': 0, 'neutral': 0}

# ----------------------------

@app.route('/')
def index():
    return render_template('index.php')

# ----------------------------

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json
    img_data = base64.b64decode(data['image'].split(',')[1])
    np_img = np.frombuffer(img_data, np.uint8)
    img = cv2.imdecode(np_img, cv2.IMREAD_COLOR)
    
    gray_img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    faces_detected = face_haar_cascade.detectMultiScale(gray_img, scaleFactor=1.3, minNeighbors=5)
    
    if len(faces_detected) == 0:
        return jsonify({'emotion': 'No face detected'})
    
    x, y, w, h = faces_detected[0]
    roi_gray = gray_img[y:y + h, x:x + w]  # Ensure correct ROI dimensions
    roi_gray = cv2.resize(roi_gray, (224, 224))
    img_pixels = image.img_to_array(roi_gray)
    img_pixels = np.expand_dims(img_pixels, axis=0)
    img_pixels /= 255
    
    predictions = model.predict(img_pixels)
    max_index = np.argmax(predictions[0])
    predicted_emotion = emotions[max_index]
    
    # Update detected emotion counts
    emotion_counts[predicted_emotion] += 1

    # Check if 10 facial expressions have been detected
    if sum(emotion_counts.values()) == 20:
        highest_emotion = max(emotion_counts, key=emotion_counts.get)
        if highest_emotion in ['happy', 'angry', 'sad', 'neutral']:
            youtube_links = {
                'happy': "https://www.youtube.com/watch?v=qcDvzifP_gk",
                'angry': "https://www.youtube.com/watch?v=f1De87ETXwo",
                'sad': "https://www.youtube.com/watch?v=oAVhUAaVCVQ",
                'neutral': "https://youtu.be/I_Rx6A2uK7U?si=7Ud9vWT_WGEFH_r9"
            }
            webbrowser.open(youtube_links[highest_emotion])
        return jsonify({'emotion': predicted_emotion, 'done': True})

    return jsonify({'emotion': predicted_emotion, 'done': False})

@app.route('/reset', methods=['POST'])
def reset():
    global emotion_counts
    emotion_counts = {'angry': 0, 'disgust': 0, 'fear': 0, 'happy': 0, 'sad': 0, 'surprise': 0, 'neutral': 0}
    return jsonify({'reset': True})

if __name__ == "__main__":
    app.run(debug=True)
