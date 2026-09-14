import os
import requests
from dotenv import load_dotenv
from flask import Flask, jsonify, request
from flask_cors import CORS

load_dotenv()

app = Flask(__name__)
CORS(app)

OLLAMA_URL = os.getenv("OLLAMA_URL", "http://127.0.0.1:11434")
MODEL = os.getenv("OLLAMA_MODEL", "llama3.2:3b")

SYSTEM_PROMPT = """
Ton rôle est d'aider un analyste à :
- Comprendre les résultats de reconnaissance ;
- Expliquer les ports et services ;
- Expliquer les vulnérabilités ;
- Interpréter les résultats Nmap, Nuclei, Subfinder et Gobuster ;
- Proposer des recommandations défensives ;
- Prioriser les vulnérabilités.

Ne propose pas d'attaque contre des systèmes non autorisés.
Réponds de manière claire, concise et pédagogique.
"""

@app.get("/health")
def health():
    return jsonify({
        "status": "ok",
        "service": "recon-ai",
        "model": MODEL
    })

@app.post("/api/chat")
def chat():
    data = request.get_json(silent=True) or {}
    message = data.get("message", "").strip()

    if not message:
        return jsonify({"error": "Le message est obligatoire."}), 400

    payload = {
        "model": MODEL,
        "stream": False,
        "messages": [
            {"role": "system", "content": SYSTEM_PROMPT},
            {"role": "user", "content": message}
        ]
    }

    try:
        response = requests.post(
            f"{OLLAMA_URL}/api/chat",
            json=payload,
            timeout=120
        )
        response.raise_for_status()
        result = response.json()
        answer = result.get("message", {}).get("content", "")

        return jsonify({
            "success": True,
            "answer": answer,
            "model": MODEL
        })
    except requests.RequestException as error:
        return jsonify({
            "success": False,
            "error": str(error)
        }), 500

@app.post("/api/analyze-scan")
def analyze_scan():
    data = request.get_json(silent=True) or {}
    scan_type = data.get("scan_type", "unknown")
    target = data.get("target", "unknown")
    results = data.get("results", [])

    if not results:
        return jsonify({"error": "Aucun résultat fourni."}), 400

    prompt = f"""
Analyse les résultats suivants d'un scan de cybersécurité autorisé.
Cible: {target}
Type de scan: {scan_type}
Résultats:
{results}

Donne :
1. Un résumé synthétique ;
2. Les principaux risques identifiés ;
3. Le niveau de priorité ;
4. Des recommandations défensives claires ;
5. Une conclusion courte.

Ne propose aucune exploitation offensive.
"""

    payload = {
        "model": MODEL,
        "stream": False,
        "messages": [
            {"role": "system", "content": SYSTEM_PROMPT},
            {"role": "user", "content": prompt}
        ]
    }

    try:
        response = requests.post(
            f"{OLLAMA_URL}/api/chat",
            json=payload,
            timeout=180
        )
        response.raise_for_status()
        result = response.json()

        return jsonify({
            "success": True,
            "analysis": result["message"]["content"]
        })
    except requests.RequestException as error:
        return jsonify({
            "success": False,
            "error": str(error)
        }), 500

if __name__ == "__main__":
    host = os.getenv("FLASK_HOST", "127.0.0.1")
    port = int(os.getenv("FLASK_PORT", 8088))
    app.run(
        host=host,
        port=port,
        debug=False
    )