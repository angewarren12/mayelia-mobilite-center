@echo off
REM Lancer Chrome en mode Kiosk avec impression automatique
REM Pour la borne de prise de tickets Mayelia

echo Demarrage de la borne Kiosk...
echo.

REM Fermer toutes les instances de Chrome existantes
taskkill /F /IM chrome.exe 2>nul

REM Attendre 2 secondes
timeout /t 2 /nobreak >nul

REM Lancer Chrome en mode Kiosk
"C:\Program Files\Google\Chrome\Application\chrome.exe" ^
  --kiosk ^
  --kiosk-printing ^
  --disable-pinch ^
  --overscroll-history-navigation=0 ^
  --disable-features=TranslateUI ^
  --no-first-run ^
  --disable-infobars ^
  --disable-session-crashed-bubble ^
  --disable-translate ^
  --disable-sync ^
  --disable-background-networking ^
  "http://127.0.0.1:8000/qms/kiosk"

REM Si Chrome se ferme, relancer automatiquement
if errorlevel 1 (
    echo Chrome s'est ferme. Relancement dans 3 secondes...
    timeout /t 3 /nobreak >nul
    goto :start
)
