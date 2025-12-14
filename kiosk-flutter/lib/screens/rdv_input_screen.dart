import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/kiosk_provider.dart';
import '../theme/app_theme.dart';
import 'qr_scanner_screen.dart';

class RdvInputScreen extends StatelessWidget {
  const RdvInputScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<KioskProvider>();

    return Container(
      margin: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: AppTheme.shadow2xl,
      ),
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      child: LayoutBuilder(
        builder: (context, constraints) {
          final keyboardSize = (constraints.maxWidth * 0.5).clamp(280.0, 350.0);
          final inputHeight = 70.0;
          final titleHeight = 50.0;
          final buttonsHeight = 55.0;
          final spacing = 12.0;
          final availableHeight = constraints.maxHeight;
          final keyboardHeight =
              (availableHeight -
                      inputHeight -
                      titleHeight -
                      buttonsHeight -
                      (spacing * 5))
                  .clamp(180.0, 220.0);
          final keySize = (keyboardHeight / 4).clamp(45.0, 65.0);

          return Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Numéro de Suivi',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF1F2937),
                  fontSize: 22,
                ),
                textAlign: TextAlign.center,
              ),
              SizedBox(height: spacing),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 12,
                ),
                decoration: BoxDecoration(
                  border: Border.all(color: Colors.grey[200]!, width: 3),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Text(
                      'MAYELIA-${DateTime.now().year}-',
                      style: TextStyle(
                        fontSize: 24,
                        fontFamily: 'monospace',
                        fontWeight: FontWeight.bold,
                        color: Colors.grey[600],
                      ),
                    ),
                    Text(
                      provider.rdvNumber.isEmpty
                          ? '123456'
                          : provider.rdvNumber.padLeft(6, '0'),
                      style: TextStyle(
                        fontSize: 28,
                        fontFamily: 'monospace',
                        fontWeight: FontWeight.bold,
                        color: provider.rdvNumber.isEmpty
                            ? Colors.grey[400]
                            : const Color(0xFF1F2937),
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(height: spacing),

              // Bouton Scanner QR Code
              SizedBox(
                width: keyboardSize,
                child: ElevatedButton.icon(
                  onPressed: provider.loading
                      ? null
                      : () async {
                          final result = await Navigator.of(context)
                              .push<String>(
                                MaterialPageRoute(
                                  builder: (context) => const QrScannerScreen(),
                                ),
                              );
                          if (result != null) {
                            provider.setRdvNumberFromScan(result);
                          }
                        },
                  icon: const Icon(Icons.qr_code_scanner, size: 24),
                  label: const Text(
                    'Scanner le QR Code',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.purple600,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),

              SizedBox(height: spacing),

              // Divider "OU"
              Row(
                children: [
                  Expanded(child: Divider(color: Colors.grey[300])),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Text(
                      'OU',
                      style: TextStyle(
                        color: Colors.grey[600],
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                    ),
                  ),
                  Expanded(child: Divider(color: Colors.grey[300])),
                ],
              ),

              SizedBox(height: spacing),

              // Clavier virtuel
              SizedBox(
                width: keyboardSize,
                child: GridView.count(
                  crossAxisCount: 3,
                  mainAxisSpacing: 8,
                  crossAxisSpacing: 8,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  childAspectRatio: keyboardSize / 3 / keySize,
                  children: [
                    ...List.generate(10, (index) {
                      final number = index == 9 ? '0' : '${index + 1}';
                      return _buildKeyButton(
                        label: number,
                        onTap: provider.loading
                            ? null
                            : () => provider.appendToRdvNumber(number),
                        backgroundColor: Colors.grey[100]!,
                        textColor: const Color(0xFF1F2937),
                      );
                    }),
                    _buildKeyButton(
                      icon: Icons.backspace,
                      onTap: provider.loading
                          ? null
                          : () => provider.backspaceRdvNumber(),
                      backgroundColor: Colors.red[100]!,
                      textColor: Colors.red[600]!,
                    ),
                    _buildKeyButton(
                      icon: Icons.check,
                      onTap: provider.loading
                          ? null
                          : () => provider.verifyRdv(),
                      backgroundColor: Colors.green[600]!,
                      textColor: Colors.white,
                    ),
                  ],
                ),
              ),
              SizedBox(height: spacing),
              if (provider.errorMessage.isNotEmpty)
                Padding(
                  padding: const EdgeInsets.only(bottom: 8),
                  child: Text(
                    provider.errorMessage,
                    style: TextStyle(
                      color: Colors.red[600],
                      fontWeight: FontWeight.bold,
                      fontSize: 14,
                    ),
                    textAlign: TextAlign.center,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton(
                      onPressed: provider.loading
                          ? null
                          : () => provider.goBack(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.grey[200]!,
                        foregroundColor: Colors.grey[700]!,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        minimumSize: const Size(0, 50),
                      ),
                      child: const Text(
                        'Annuler',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: provider.loading || provider.rdvNumber.isEmpty
                          ? null
                          : () => provider.verifyRdv(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.mayelia600,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        minimumSize: const Size(0, 50),
                      ),
                      child: provider.loading
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                valueColor: AlwaysStoppedAnimation<Color>(
                                  Colors.white,
                                ),
                              ),
                            )
                          : const Text(
                              'Valider',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                    ),
                  ),
                ],
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildKeyButton({
    String? label,
    IconData? icon,
    required VoidCallback? onTap,
    required Color backgroundColor,
    required Color textColor,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Container(
        decoration: BoxDecoration(
          color: backgroundColor,
          borderRadius: BorderRadius.circular(10),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 3,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Center(
          child: icon != null
              ? Icon(icon, color: textColor, size: 20)
              : Text(
                  label!,
                  style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                    color: textColor,
                  ),
                ),
        ),
      ),
    );
  }
}
