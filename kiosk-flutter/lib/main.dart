import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'theme/app_theme.dart';
import 'providers/kiosk_provider.dart';
import 'services/api_service.dart';
import 'services/bluetooth_service.dart';
import 'services/print_service.dart';
import 'screens/home_screen.dart';
import 'screens/service_selection_screen.dart';
import 'screens/rdv_input_screen.dart';
import 'screens/confirmation_screen.dart';
import 'widgets/kiosk_header.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  // Forcer l'orientation paysage (optionnel - commenter si pas souhaité)
  // SystemChrome.setPreferredOrientations([
  //   DeviceOrientation.landscapeLeft,
  //   DeviceOrientation.landscapeRight,
  // ]);

  // Désactiver la sélection de texte (comme le web)
  SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersive);

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Mayelia Kiosk',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const KioskScreen(),
    );
  }
}

class KioskScreen extends StatefulWidget {
  const KioskScreen({super.key});

  @override
  State<KioskScreen> createState() => _KioskScreenState();
}

class _KioskScreenState extends State<KioskScreen> {
  // Vous pouvez aussi les charger depuis les paramètres ou une configuration
  static const int centreId = 2;
  static const String centreNom = 'Centre de Daloa';

  late final ApiService apiService;
  late final BluetoothService bluetoothService;
  late final PrintService printService;

  @override
  void initState() {
    super.initState();
    apiService = ApiService();
    bluetoothService = BluetoothService();
    printService = PrintService(bluetoothService);
  }

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) {
        final provider = KioskProvider(
          apiService: apiService,
          bluetoothService: bluetoothService,
          printService: printService,
        );
        // Initialiser de manière asynchrone
        provider.initialize(centreId, centreNom);
        return provider;
      },
      child: Scaffold(
        backgroundColor: const Color(0xFFF9FAFB),
        body: SafeArea(
          child: Column(
            children: [
              KioskHeader(centreNom: centreNom),
              Expanded(
                child: LayoutBuilder(
                  builder: (context, constraints) {
                    return SingleChildScrollView(
                      physics: const NeverScrollableScrollPhysics(),
                      child: ConstrainedBox(
                        constraints: BoxConstraints(
                          minHeight: constraints.maxHeight,
                          maxHeight: constraints.maxHeight,
                        ),
                        child: Consumer<KioskProvider>(
                          builder: (context, provider, _) {
                            // Afficher un écran de chargement pendant l'initialisation
                            if (provider.initializing) {
                              return _buildLoadingScreen();
                            }
                            return _buildCurrentScreen(provider.step);
                          },
                        ),
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLoadingScreen() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const CircularProgressIndicator(
            strokeWidth: 4,
            valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF02913F)),
          ),
          const SizedBox(height: 24),
          Text(
            'Chargement...',
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              color: const Color(0xFF1F2937),
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Vérification du mode de gestion',
            style: Theme.of(
              context,
            ).textTheme.bodyMedium?.copyWith(color: const Color(0xFF6B7280)),
          ),
        ],
      ),
    );
  }

  Widget _buildCurrentScreen(KioskStep step) {
    switch (step) {
      case KioskStep.home:
        return const HomeScreen();
      case KioskStep.serviceSelection:
        return const ServiceSelectionScreen();
      case KioskStep.rdvInput:
        return const Center(child: RdvInputScreen());
      case KioskStep.confirmation:
        return const ConfirmationScreen();
    }
  }
}
