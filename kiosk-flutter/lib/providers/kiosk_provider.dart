import 'package:flutter/foundation.dart';
import '../models/ticket.dart';
import '../models/service.dart';
import '../models/centre.dart';
import '../services/api_service.dart';
import '../services/bluetooth_service.dart';
import '../services/print_service.dart';

enum KioskStep {
  home, // Écran d'accueil
  serviceSelection, // Sélection service
  rdvInput, // Saisie numéro RDV
  confirmation, // Confirmation/impression
}

class KioskProvider with ChangeNotifier {
  final ApiService apiService;
  final BluetoothService bluetoothService;
  final PrintService printService;

  KioskStep _step = KioskStep.home;
  bool _loading = false;
  bool _initializing = true; // Indique si l'initialisation est en cours
  String _errorMessage = '';
  String _rdvNumber = '';

  Centre? _centre;
  List<Service> _services = [];
  Service? _selectedService;
  Ticket? _currentTicket;

  KioskStep get step => _step;
  bool get loading => _loading;
  bool get initializing => _initializing;
  String get errorMessage => _errorMessage;
  String get rdvNumber => _rdvNumber;
  Centre? get centre => _centre;
  List<Service> get services => _services;
  Service? get selectedService => _selectedService;
  Ticket? get currentTicket => _currentTicket;

  bool get isFifoMode => _centre?.qmsMode == 'fifo';

  KioskProvider({
    required this.apiService,
    required this.bluetoothService,
    required this.printService,
  });

  Future<void> initialize(int centreId, String? centreNom) async {
    _initializing = true;
    _errorMessage = '';
    notifyListeners();

    try {
      // Charger les infos du centre depuis l'API pour obtenir le qms_mode
      final centreInfo = await apiService.getCentreInfo(centreId);
      if (centreInfo != null) {
        _centre = Centre.fromJson(centreInfo);
        debugPrint(
          'Centre chargé: ${_centre!.nom}, Mode QMS: ${_centre!.qmsMode}',
        );
      } else {
        // Fallback si l'API échoue
        _centre = Centre(id: centreId, nom: centreNom ?? 'Centre');
        debugPrint('Utilisation du centre par défaut (API échouée)');
        _errorMessage = 'Mode QMS non détecté. Utilisation du mode par défaut.';
      }

      // Charger les services après avoir chargé le centre
      await _loadServices();
    } catch (e) {
      debugPrint('Erreur lors de l\'initialisation: $e');
      _centre = Centre(id: centreId, nom: centreNom ?? 'Centre');
      _errorMessage = 'Erreur lors du chargement des informations du centre.';
    } finally {
      _initializing = false;
      notifyListeners();
    }
  }

  Future<void> _loadServices() async {
    if (_centre == null) return;

    try {
      _services = await apiService.getServices(_centre!.id);
      debugPrint('Services chargés: ${_services.length}');
      if (_services.isEmpty) {
        debugPrint('Aucun service trouvé pour le centre ${_centre!.id}');
        _errorMessage =
            'Aucun service disponible pour ce centre. Veuillez contacter l\'administrateur.';
      } else {
        _errorMessage =
            ''; // Réinitialiser l'erreur si des services sont trouvés
      }
    } catch (e) {
      debugPrint('Erreur lors du chargement des services: $e');
      _errorMessage = 'Erreur lors du chargement des services: ${e.toString()}';
    }
    notifyListeners();
  }

  Future<void> selectType(String type) async {
    debugPrint('selectType appelé: $type');
    _errorMessage = '';

    if (type == 'sans_rdv') {
      // Attendre que les services soient chargés
      if (_services.isEmpty && _centre != null) {
        debugPrint('Services non chargés, chargement en cours...');
        await _loadServices();
      }

      if (_services.isEmpty) {
        _errorMessage =
            'Aucun service disponible pour ce centre.\nVeuillez contacter l\'administrateur.';
        notifyListeners();
        return;
      }

      if (isFifoMode || _services.length <= 1) {
        // Mode FIFO ou un seul service : créer directement le ticket
        debugPrint('Création du ticket avec service: ${_services.first.id}');
        await _createTicket('sans_rdv', serviceId: _services.first.id);
      } else {
        // Plusieurs services : afficher la sélection
        debugPrint('Affichage de la sélection de services');
        _step = KioskStep.serviceSelection;
        notifyListeners();
      }
    } else if (type == 'rdv') {
      _step = KioskStep.rdvInput;
      _rdvNumber = '';
      _errorMessage = '';
      notifyListeners();
    }
  }

  Future<void> selectService(int serviceId) async {
    _selectedService = _services.firstWhere((s) => s.id == serviceId);
    await _createTicket('sans_rdv', serviceId: serviceId);
  }

  void appendToRdvNumber(String digit) {
    _rdvNumber += digit;
    notifyListeners();
  }

  void backspaceRdvNumber() {
    if (_rdvNumber.isNotEmpty) {
      _rdvNumber = _rdvNumber.substring(0, _rdvNumber.length - 1);
      notifyListeners();
    }
  }

  /// Définit le numéro RDV depuis un scan QR code
  /// Le QR code peut contenir soit le numéro complet (MAYELIA-YYYY-XXXXXX)
  /// soit juste les 6 chiffres (XXXXXX)
  void setRdvNumberFromScan(String scannedCode) {
    String numero = scannedCode.trim();

    // Si le QR code contient le format complet MAYELIA-YYYY-XXXXXX
    if (numero.startsWith('MAYELIA-')) {
      final parts = numero.split('-');
      if (parts.length >= 3) {
        // Extraire les 6 derniers chiffres
        numero = parts.last;
      }
    }

    // S'assurer que c'est bien 6 chiffres
    if (numero.length == 6 && int.tryParse(numero) != null) {
      _rdvNumber = numero;
      _errorMessage = '';
      notifyListeners();
      // Vérifier automatiquement après le scan
      verifyRdv();
    } else {
      _errorMessage = 'QR Code invalide. Format attendu: MAYELIA-YYYY-XXXXXX';
      notifyListeners();
    }
  }

  Future<void> verifyRdv() async {
    if (_rdvNumber.isEmpty || _centre == null) return;

    _loading = true;
    _errorMessage = '';
    notifyListeners();

    // Construire le numéro complet au format MAYELIA-YYYY-XXXXXX
    final annee = DateTime.now().year;
    final numeroComplet = 'MAYELIA-$annee-${_rdvNumber.padLeft(6, '0')}';

    final result = await apiService.checkRdv(
      numero: numeroComplet,
      centreId: _centre!.id,
    );

    _loading = false;

    if (result['success'] == true && result['rdv'] != null) {
      final rdvData = result['rdv'] as Map<String, dynamic>;
      // Construire le numéro complet pour l'envoyer lors de la création du ticket
      final annee = DateTime.now().year;
      final numeroCompletTicket =
          'MAYELIA-$annee-${_rdvNumber.padLeft(6, '0')}';
      _createTicket(
        'rdv',
        serviceId: rdvData['service_id'] as int?,
        numeroRdv: numeroCompletTicket,
      );
    } else {
      _errorMessage = result['message'] as String? ?? 'Numéro invalide';
      notifyListeners();
    }
  }

  Future<void> _createTicket(
    String type, {
    int? serviceId,
    String? numeroRdv,
  }) async {
    if (_centre == null) {
      debugPrint('Erreur: centre est null');
      _errorMessage = 'Erreur: centre non défini';
      notifyListeners();
      return;
    }

    _loading = true;
    _errorMessage = '';
    notifyListeners();

    // 1. VÉRIFICATION PRÉALABLE DE L'IMPRIMANTE
    // On ne crée pas le ticket en BD si l'imprimante n'est pas prête
    bool printerReady = await bluetoothService.connect();
    if (!printerReady) {
      _loading = false;
      _errorMessage = 'IMPRIMANTE DÉCONNECTÉE. Veuillez vérifier la connexion Bluetooth et le papier.';
      notifyListeners();
      return;
    }

    debugPrint(
      'Création du ticket: type=$type, serviceId=$serviceId, numeroRdv=$numeroRdv',
    );

    try {
      final result = await apiService.createTicket(
        centreId: _centre!.id,
        type: type,
        serviceId: serviceId,
        numeroRdv: numeroRdv,
      );

      debugPrint('Résultat de createTicket: $result');

      if (result['success'] == true && result['ticket'] != null) {
        _currentTicket = result['ticket'] as Ticket;
        _step = KioskStep.confirmation;
        _errorMessage = '';
        _loading = false;
        notifyListeners();

        debugPrint('Ticket créé avec succès: ${_currentTicket?.numero}');

        // 2. TENTATIVE D'IMPRESSION
        bool printSuccess = await _printTicket();
        
        if (!printSuccess) {
          _errorMessage = "L'impression a échoué. Veuillez vérifier l'imprimante et appuyer sur RÉ-IMPRIMER.";
          notifyListeners();
        } else {
          // Succès total : Retourner à l'accueil après 5 secondes
          Future.delayed(const Duration(seconds: 5), () {
            if (_step == KioskStep.confirmation) {
              reset();
            }
          });
        }
      } else {
        _loading = false;
        final errorMsg =
            result['message'] as String? ??
            'Erreur lors de la création du ticket';
        debugPrint('Erreur lors de la création du ticket: $errorMsg');
        _errorMessage = errorMsg;
        notifyListeners();
      }
    } catch (e) {
      _loading = false;
      debugPrint('Exception lors de la création du ticket: $e');
      _errorMessage = 'Erreur de connexion: ${e.toString()}';
      notifyListeners();
    }
  }

  Future<bool> _printTicket() async {
    if (_currentTicket == null || _centre == null) return false;

    final serviceNom = _selectedService?.nom;
    return await printService.printTicket(_currentTicket!, _centre!.nom, serviceNom);
  }

  /// Permet de retenter l'impression en cas d'échec sans recréer de numéro en base
  Future<void> retryPrint() async {
    if (_currentTicket == null) return;
    
    _loading = true;
    _errorMessage = 'Tentative de ré-impression...';
    notifyListeners();
    
    bool result = await _printTicket();
    
    _loading = false;
    if (result) {
      _errorMessage = 'Impression réussie !';
      notifyListeners();
      Future.delayed(const Duration(seconds: 3), () => reset());
    } else {
      _errorMessage = 'Échec persistant. Vérifiez la connexion Bluetooth de l\'imprimante.';
      notifyListeners();
    }
  }

  void goBack() {
    if (_step == KioskStep.serviceSelection || _step == KioskStep.rdvInput) {
      _step = KioskStep.home;
      _errorMessage = '';
      _rdvNumber = '';
      notifyListeners();
    }
  }

  void reset() {
    _step = KioskStep.home;
    _loading = false;
    _errorMessage = '';
    _rdvNumber = '';
    _selectedService = null;
    _currentTicket = null;
    notifyListeners();
  }

  void clearError() {
    _errorMessage = '';
    notifyListeners();
  }
}
