import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../theme/app_theme.dart';

class QrScannerScreen extends StatefulWidget {
  final Function(String)? onScan;

  const QrScannerScreen({super.key, this.onScan});

  @override
  State<QrScannerScreen> createState() => _QrScannerScreenState();
}

class _QrScannerScreenState extends State<QrScannerScreen> {
  final MobileScannerController _controller = MobileScannerController(
    detectionSpeed: DetectionSpeed.noDuplicates,
    facing: CameraFacing.back,
  );

  bool _isProcessing = false;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _handleBarcode(BarcodeCapture barcodeCapture) {
    if (_isProcessing) return;

    final List<Barcode> barcodes = barcodeCapture.barcodes;
    if (barcodes.isEmpty) return;

    final String? code = barcodes.first.rawValue;
    if (code == null || code.isEmpty) return;

    setState(() {
      _isProcessing = true;
    });

    // Arrêter le scanner
    _controller.stop();

    // Extraire le numéro du QR code
    // Le QR code contient : MAYELIA-YYYY-XXXXXX
    String numero = code.trim();

    // Si le QR code contient le format complet, on l'utilise directement
    if (numero.startsWith('MAYELIA-')) {
      // Extraire les 6 derniers chiffres après MAYELIA-YYYY-
      final parts = numero.split('-');
      if (parts.length >= 3) {
        // Prendre la dernière partie (les 6 chiffres)
        numero = parts.last;
      }
    }

    // Fermer l'écran et retourner le résultat
    if (mounted) {
      Navigator.of(context).pop(numero);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        children: [
          // Scanner de QR code
          MobileScanner(controller: _controller, onDetect: _handleBarcode),

          // Overlay avec zone de scan
          SafeArea(
            child: Column(
              children: [
                // En-tête
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [
                        Colors.black.withValues(alpha: 0.7),
                        Colors.transparent,
                      ],
                    ),
                  ),
                  child: Row(
                    children: [
                      IconButton(
                        icon: const Icon(
                          Icons.arrow_back,
                          color: Colors.white,
                          size: 28,
                        ),
                        onPressed: () => Navigator.of(context).pop(),
                      ),
                      const Expanded(
                        child: Text(
                          'Scanner le QR Code',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 20,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      const SizedBox(
                        width: 48,
                      ), // Pour équilibrer le bouton retour
                    ],
                  ),
                ),

                const Spacer(),

                // Instructions et zone de scan
                Container(
                  padding: const EdgeInsets.all(32),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [
                        Colors.transparent,
                        Colors.black.withValues(alpha: 0.8),
                      ],
                    ),
                  ),
                  child: Column(
                    children: [
                      // Zone de scan visuelle
                      Container(
                        width: 280,
                        height: 280,
                        decoration: BoxDecoration(
                          border: Border.all(
                            color: AppTheme.mayelia500,
                            width: 3,
                          ),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Stack(
                          children: [
                            // Coins arrondis
                            Positioned(
                              top: 0,
                              left: 0,
                              child: Container(
                                width: 40,
                                height: 40,
                                decoration: BoxDecoration(
                                  border: Border(
                                    top: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                    left: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                  ),
                                  borderRadius: const BorderRadius.only(
                                    topLeft: Radius.circular(16),
                                  ),
                                ),
                              ),
                            ),
                            Positioned(
                              top: 0,
                              right: 0,
                              child: Container(
                                width: 40,
                                height: 40,
                                decoration: BoxDecoration(
                                  border: Border(
                                    top: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                    right: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                  ),
                                  borderRadius: const BorderRadius.only(
                                    topRight: Radius.circular(16),
                                  ),
                                ),
                              ),
                            ),
                            Positioned(
                              bottom: 0,
                              left: 0,
                              child: Container(
                                width: 40,
                                height: 40,
                                decoration: BoxDecoration(
                                  border: Border(
                                    bottom: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                    left: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                  ),
                                  borderRadius: const BorderRadius.only(
                                    bottomLeft: Radius.circular(16),
                                  ),
                                ),
                              ),
                            ),
                            Positioned(
                              bottom: 0,
                              right: 0,
                              child: Container(
                                width: 40,
                                height: 40,
                                decoration: BoxDecoration(
                                  border: Border(
                                    bottom: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                    right: BorderSide(
                                      color: AppTheme.mayelia500,
                                      width: 4,
                                    ),
                                  ),
                                  borderRadius: const BorderRadius.only(
                                    bottomRight: Radius.circular(16),
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 32),

                      // Instructions
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.95),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Column(
                          children: [
                            Icon(
                              Icons.qr_code_scanner,
                              size: 48,
                              color: AppTheme.mayelia600,
                            ),
                            const SizedBox(height: 12),
                            const Text(
                              'Positionnez le QR code du reçu\nau centre du cadre',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
                                color: Color(0xFF1F2937),
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              'Le scan se fera automatiquement',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
