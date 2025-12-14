import 'dart:typed_data';
import 'package:flutter_bluetooth_serial/flutter_bluetooth_serial.dart';
import '../config/printer_config.dart';

class BluetoothService {
  BluetoothConnection? _connection;
  bool _isConnected = false;

  bool get isConnected => _isConnected;

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
      if (device == null) {
        device = await findPrinter();
        if (device == null) {
          return false;
        }
      }

      _connection = await BluetoothConnection.toAddress(device.address);
      _isConnected = _connection!.isConnected;
      return _isConnected;
    } catch (e) {
      _isConnected = false;
      return false;
    }
  }

  Future<void> disconnect() async {
    try {
      await _connection?.close();
      _connection = null;
      _isConnected = false;
    } catch (e) {
      // Ignore
    }
  }

  Future<bool> write(List<int> data) async {
    if (!_isConnected || _connection == null) {
      return false;
    }

    try {
      _connection!.output.add(Uint8List.fromList(data));
      await _connection!.output.allSent;
      return true;
    } catch (e) {
      return false;
    }
  }
}

