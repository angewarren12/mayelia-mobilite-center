import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/kiosk_provider.dart';
import '../theme/app_theme.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<KioskProvider>();
    final isFifo = provider.isFifoMode;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
      child: Column(
        children: [
          // Affichage des erreurs
          if (provider.errorMessage.isNotEmpty)
            Container(
              margin: const EdgeInsets.only(bottom: 16),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.red[50],
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.red[400]!, width: 2),
                boxShadow: [
                  BoxShadow(
                    color: Colors.red[100]!,
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.error_outline, color: Colors.red[700], size: 28),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Erreur',
                          style: TextStyle(
                            color: Colors.red[900],
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          provider.errorMessage,
                          style: TextStyle(
                            color: Colors.red[700],
                            fontSize: 14,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: Icon(Icons.close, color: Colors.red[700], size: 24),
                    onPressed: () {
                      // Réinitialiser l'erreur
                      provider.clearError();
                    },
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                  ),
                ],
              ),
            ),
          // Grille des options
          Expanded(
            child: LayoutBuilder(
              builder: (context, constraints) {
                final cardHeight = isFifo
                    ? constraints.maxHeight.clamp(300.0, 500.0)
                    : (constraints.maxHeight / 1.2).clamp(250.0, 400.0);

                // Calculer childAspectRatio de manière sécurisée
                final crossAxisCount = isFifo ? 1 : 2;
                final cardWidth = (constraints.maxWidth - (crossAxisCount - 1) * 16) / crossAxisCount;
                final aspectRatio = cardWidth / cardHeight;
                final safeAspectRatio = aspectRatio > 0 ? aspectRatio : 1.0;

                return GridView.count(
                  crossAxisCount: crossAxisCount,
                  mainAxisSpacing: 16,
                  crossAxisSpacing: 16,
                  childAspectRatio: safeAspectRatio,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  children: [
                    // Option 1: Sans RDV ou FIFO
                    _buildOptionCard(
                      context: context,
                      icon: Icons.confirmation_number,
                      title: isFifo ? 'PRENDRE UN TICKET' : 'SANS RENDEZ-VOUS',
                      subtitle: isFifo
                          ? 'Ticket pour service standard'
                          : 'File d\'attente standard',
                      color: AppTheme.mayelia500,
                      backgroundColor: AppTheme.mayelia100,
                      onTap: provider.loading
                          ? null
                          : () async {
                              await provider.selectType('sans_rdv');
                            },
                      isLoading: provider.loading,
                    ),

                    // Option 2: Avec RDV (seulement si pas FIFO)
                    if (!isFifo)
                      _buildOptionCard(
                        context: context,
                        icon: Icons.calendar_today,
                        title: 'J\'AI UN RENDEZ-VOUS',
                        subtitle: 'Scanner ou saisir numéro',
                        color: AppTheme.purple600,
                        backgroundColor: AppTheme.purple100,
                        onTap: () => provider.selectType('rdv'),
                        isLoading: false,
                      ),
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

Widget _buildOptionCard({
  required BuildContext context,
  required IconData icon,
  required String title,
  required String subtitle,
  required Color color,
  required Color backgroundColor,
  required VoidCallback? onTap,
  required bool isLoading,
}) {
  return InkWell(
    onTap: onTap,
    borderRadius: BorderRadius.circular(24),
    child: Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border(bottom: BorderSide(color: color, width: 8)),
        boxShadow: AppTheme.shadowXl,
      ),
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Flexible(
              child: Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: isLoading ? Colors.grey[100] : backgroundColor,
                  shape: BoxShape.circle,
                ),
                child: isLoading
                    ? const SizedBox(
                        width: 40,
                        height: 40,
                        child: CircularProgressIndicator(
                          strokeWidth: 3,
                          valueColor: AlwaysStoppedAnimation<Color>(
                            Colors.grey,
                          ),
                        ),
                      )
                    : Icon(icon, size: 40, color: color),
              ),
            ),
            const SizedBox(height: 16),
            Flexible(
              child: Text(
                title,
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF1F2937),
                  fontSize: 28,
                ),
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(height: 8),
            Flexible(
              child: Text(
                subtitle,
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                  color: const Color(0xFF6B7280),
                  fontSize: 16,
                ),
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    ),
  );
}
