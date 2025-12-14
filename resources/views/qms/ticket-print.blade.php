<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket #{{ $ticket->numero }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 48mm;
            margin: 0;
            padding: 5mm 5mm 3mm 5mm;
            text-align: center;
        }
        .header {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        .ticket-num {
            font-size: 42px;
            font-weight: 900;
            margin: 8px 0;
            padding: 8px 0;
            letter-spacing: 2px;
        }
        .ticket-num-border {
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 8px 0;
        }
        .info {
            font-size: 11px;
            margin: 6px 0;
            line-height: 1.3;
        }
        .qrcode {
            margin: 6px auto;
            width: 70px;
            height: 70px;
        }
        .qrcode img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .priority {
            font-weight: bold;
            font-size: 11px;
            margin-top: 6px;
            background: #000;
            color: #fff;
            padding: 4px;
            line-height: 1.2;
        }
        .footer {
            margin-top: 8px;
            font-size: 9px;
            padding-top: 6px;
            line-height: 1.3;
        }
        .separator {
            border-top: 1px solid #000;
            margin: 6px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        ONECI - {{ $ticket->centre->nom }}<br>
        {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="info">VOTRE NUMÉRO</div>
    
    <div class="ticket-num-border">
        <div class="ticket-num">
            {{ $ticket->numero }}
        </div>
    </div>

    @if(isset($qrCodeBase64))
    <div class="qrcode">
        <img src="{{ $qrCodeBase64 }}" alt="QR Code">
    </div>
    @endif

    <div class="info">
        {{ $ticket->service->nom ?? 'Service Standard' }}
    </div>

    @if($ticket->type === 'rdv')
    <div class="priority">
        ⭐ PRIORITAIRE - RDV ⭐
    </div>
    @endif
    
    <div class="separator"></div>
    
    <div class="footer">
        Veuillez patienter dans la salle d'attente.<br>
        Votre numéro sera affiché à l'écran.
    </div>

    <script>
        // Impression automatique immédiate - Compatible Android et Desktop
        (function() {
            // Détecter si on est sur Android
            const isAndroid = /Android/i.test(navigator.userAgent);
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            // Attendre que tout soit chargé
            window.addEventListener('load', function() {
                // Délai pour garantir le rendu complet (images, QR code, etc.)
                setTimeout(function() {
                    if (window.matchMedia && window.matchMedia('print').matches === false) {
                        // Sur Android/Mobile, window.print() ouvre généralement le menu de partage
                        // qui permettra de sélectionner l'imprimante Bluetooth
                        if (navigator.print) {
                            // API Web Printing (expérimental, Chrome 98+ desktop et certains Android)
                            navigator.print().catch(function(err) {
                                console.log('Web Printing API non disponible, utilisation de window.print()');
                                window.print();
                            });
                        } else {
                            // window.print() standard
                            // Sur Android : ouvre le menu de partage avec l'imprimante
                            // Sur Desktop avec --kiosk-printing : impression directe
                            window.print();
                        }
                    }
                }, 300); // Délai pour garantir le chargement complet du QR code
            });
            
            // Gérer la fermeture après impression (si dans une iframe ou popup)
            window.addEventListener('afterprint', function() {
                if (window.opener || window !== window.top) {
                    setTimeout(function() {
                        if (window.opener) {
                            window.close();
                        }
                    }, 500);
                }
            });
        })();
    </script>
</body>
</html>
