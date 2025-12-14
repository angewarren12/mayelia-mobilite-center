import 'package:esc_pos_utils/esc_pos_utils.dart';
import '../models/ticket.dart';
import 'bluetooth_service.dart';

class PrintService {
  final BluetoothService bluetoothService;

  PrintService(this.bluetoothService);

  Future<bool> printTicket(
    Ticket ticket,
    String centreNom,
    String? serviceNom,
  ) async {
    try {
      // S'assurer que la connexion Bluetooth est active
      if (!bluetoothService.isConnected) {
        final connected = await bluetoothService.connect();
        if (!connected) {
          return false;
        }
      }

      // Générer les commandes ESC/POS
      final generator = Generator(
        PaperSize.mm58,
        await CapabilityProfile.load(),
      );

      List<int> bytes = [];

      // Initialisation
      bytes += generator.reset();
      bytes += generator.setGlobalCodeTable('CP1252');

      // Format selon spécification utilisateur
      // 1ère ligne : CENTRE
      bytes += generator.text(
        'CENTRE',
        styles: PosStyles(align: PosAlign.center, bold: true),
      );

      bytes += generator.feed(1);

      // 2ème ligne : Nom du centre
      bytes += generator.text(
        centreNom,
        styles: PosStyles(
          align: PosAlign.center,
          bold: true,
          height: PosTextSize.size2,
          width: PosTextSize.size2,
        ),
      );

      bytes += generator.feed(1);

      // 3ème ligne : TICKET : numéro
      bytes += generator.text(
        'TICKET : ${ticket.numero}',
        styles: PosStyles(align: PosAlign.center, bold: true),
      );

      bytes += generator.feed(1);

      // 4ème ligne : SERVICE
      if (serviceNom != null && serviceNom.isNotEmpty) {
        bytes += generator.text(
          'SERVICE $serviceNom',
          styles: PosStyles(align: PosAlign.center, bold: true),
        );
      }

      bytes += generator.feed(1);

      // 5ème ligne : TYPE
      bytes += generator.text(
        'TYPE ${ticket.type == 'rdv' ? 'Avec RDV' : 'Sans RDV'}',
        styles: PosStyles(align: PosAlign.center, bold: true),
      );

      bytes += generator.feed(1);

      // 6ème ligne : Date et heure
      bytes += generator.text(
        _formatDate(ticket.createdAt),
        styles: PosStyles(align: PosAlign.center),
      );

      bytes += generator.feed(2);

      // QR Code (optionnel, entre le type et la date si nécessaire)
      final qrData = 'TICKET:${ticket.numero}:${ticket.centreId}';
      bytes += generator.row([PosColumn(text: '', width: 12)]);
      bytes += generator.text(
        qrData,
        styles: PosStyles(align: PosAlign.center, fontType: PosFontType.fontB),
      );

      bytes += generator.feed(2);

      // 7ème ligne : Merci de votre visite
      bytes += generator.text(
        'Merci de votre visite',
        styles: PosStyles(align: PosAlign.center, bold: true),
      );

      bytes += generator.feed(3);
      bytes += generator.cut();

      // Envoyer à l'imprimante
      return await bluetoothService.write(bytes);
    } catch (e) {
      return false;
    }
  }

  String _formatDate(DateTime date) {
    return '${date.day.toString().padLeft(2, '0')}/'
        '${date.month.toString().padLeft(2, '0')}/'
        '${date.year} ${date.hour.toString().padLeft(2, '0')}:'
        '${date.minute.toString().padLeft(2, '0')}';
  }
}
