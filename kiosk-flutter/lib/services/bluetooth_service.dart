import 'dart:typed_data';
import 'dart:async';
import 'package:flutter/foundation.dart';
import 'package:flutter_bluetooth_serial/flutter_bluetooth_serial.dart';
import '../config/printer_config.dart';

class BluetoothService {
  BluetoothConnection? _connection;
  bool _isConnected = false;
  BluetoothDevice? _lastDevice;
  Timer? _reconnectTimer;

  bool get isConnected => _isConnected;

  BluetoothService() {
    // Surveiller l'état de la connexion de manière proactive
    _startKeepAlive();
  }

  void _startKeepAlive() {
    _reconnectTimer?.cancel();
    _reconnectTimer = Timer.periodic(const Duration(seconds: 10), (timer) async {
      if (!_isConnected && _lastDevice != null) {
        debugPrint('Tentative de reconnexion automatique à l\'imprimante...');
        await connect(device: _lastDevice);
      }
    });
  }

  Future<bool> isEnabled() async {
    try {
      final state = await FlutterBluetoothSerial.instance.state;
      return state == BluetoothState.STATE_ON;
    } catch (e) {
      return false;
    }
  }

  Future<bool> enable() async {
    try {
      return await FlutterBluetoothSerial.instance.requestEnable() ?? false;
    } catch (e) {
      return false;
    }
  }

  Future<List<BluetoothDevice>> getPairedDevices() async {
    try {
      final devices = await FlutterBluetoothSerial.instance.getBondedDevices();
      return devices.toList();
    } catch (e) {
      return [];
    }
  }

  Future<BluetoothDevice?> findPrinter() async {
    try {
      final devices = await getPairedDevices();
      for (var device in devices) {
        final deviceName = device.name;
        if (deviceName != null && 
            (deviceName == PrinterConfig.printerName || 
            deviceName.contains('BT') ||
            deviceName.contains('Printer'))) {
          return device;
        }
      }
      return devices.isNotEmpty ? devices.first : null;
    } catch (e) {
      return null;
    }
  }

  Future<bool> connect({BluetoothDevice? device}) async {
    try {
      // Si déjà connecté, on vérifie si la connexion est réellement active
      if (_isConnected && _connection != null && _connection!.isConnected) {
        return true;
      }

      if (device == null) {
        device = await findPrinter();
        if (device == null) {
          return false;
        }
      }

      _lastDevice = device;
      
      // Fermer l'ancienne connexion si elle existe
      await _connection?.finish();
      
      _connection = await BluetoothConnection.toAddress(device.address).timeout(
        const Duration(seconds: 5),
        onTimeout: () {
          throw TimeoutException('Connexion Bluetooth expirée');
        },
      );
      
      _isConnected = _connection!.isConnected;

      // Écouter la déconnexion inattendue
      _connection!.input?.listen(null).onDone(() {
        _isConnected = false;
        debugPrint('Connexion Bluetooth perdue.');
      });

      return _isConnected;
    } catch (e) {
      debugPrint('Erreur de connexion Bluetooth : $e');
      _isConnected = false;
      return false;
    }
  }

  Future<void> disconnect() async {
    try {
      _reconnectTimer?.cancel();
      await _connection?.finish();
      _connection = null;
      _isConnected = false;
    } catch (e) {
      // Ignore
    }
  }

  Future<bool> write(List<int> data) async {
    // Tentative de reconnexion si déconnecté avant d'écrire
    if (!_isConnected || _connection == null || !_connection!.isConnected) {
      bool reconnected = await connect(device: _lastDevice);
      if (!reconnected) return false;
    }

    try {
      _connection!.output.add(Uint8List.fromList(data));
      await _connection!.output.allSent;
      return true;
    } catch (e) {
      _isConnected = false;
      return false;
    }
  }
}

