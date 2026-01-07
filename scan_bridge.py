import os
import base64
import time
from flask import Flask, jsonify
from flask_cors import CORS
import win32com.client # Nécessite pywin32

app = Flask(__name__)
CORS(app) # Autorise les requêtes depuis la plateforme web

# Configuration WIA (Windows Image Acquisition)
WIA_DEVICE_TYPE_SCANNER = 1

@app.route('/scan', methods=['GET'])
def scan():
    try:
        # Initialisation du gestionnaire de périphériques WIA
        device_manager = win32com.client.Dispatch("Wia.DeviceManager")
        
        # Trouver le premier scanner disponible
        scanner = None
        for device_info in device_manager.DeviceInfos:
            if device_info.Type == WIA_DEVICE_TYPE_SCANNER:
                scanner = device_info.Connect()
                break
        
        if not scanner:
            return jsonify({"success": False, "message": "Aucun scanner détecté"}), 404

        # Configurer et lancer le scan
        item = scanner.Items[1]
        image_file = item.Transfer("{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}") # Format JPG
        
        # Sauvegarder temporairement
        temp_path = os.path.join(os.getcwd(), "temp_scan.jpg")
        if os.path.exists(temp_path):
            os.remove(temp_path)
        image_file.SaveFile(temp_path)
        
        # Lire et encoder en base64
        with open(temp_path, "rb") as f:
            encoded_image = base64.b64encode(f.read()).decode('utf-8')
        
        return jsonify({
            "success": True,
            "image": encoded_image,
            "format": "jpg"
        })

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

if __name__ == '__main__':
    print("--- PONT DE SCAN MAYELIA LANCI ---")
    print("Le pont écoute sur http://localhost:18622")
    print("Appuyez sur Ctrl+C pour arrêter.")
    app.run(port=18622)
