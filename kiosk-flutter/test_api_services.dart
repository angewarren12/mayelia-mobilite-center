import 'dart:convert';
import 'package:http/http.dart' as http;

void main() async {
  const String baseUrl = 'https://rendez-vous.mayeliamobilite.com';
  const int centreId = 1;

  // Tester avec l'ancienne URL d'abord
  final urlOld = '$baseUrl/qms/api/services/$centreId';
  // Puis avec la nouvelle URL API
  final urlNew = '$baseUrl/api/qms/services/$centreId';

  print('🧪 Test de l\'API des services');
  print('═══════════════════════════════════════════════');
  print('Centre ID: $centreId');
  print('');

  // Test 1: Ancienne URL
  print('🔹 TEST 1: Ancienne URL (web.php)');
  print('URL: $urlOld');
  await testUrl(urlOld);

  print('');
  print('═══════════════════════════════════════════════');
  print('');

  // Test 2: Nouvelle URL API
  print('🔹 TEST 2: Nouvelle URL (api.php)');
  print('URL: $urlNew');
  await testUrl(urlNew);
}

Future<void> testUrl(String url) async {
  try {
    print('📡 Envoi de la requête...');
    final response = await http.get(
      Uri.parse(url),
      headers: {'Accept': 'application/json'},
    );

    print('📊 Statut HTTP: ${response.statusCode}');
    print('');

    if (response.statusCode == 200) {
      print('✅ Réponse reçue avec succès');
      print('');
      print('📄 Corps de la réponse (raw):');
      print('─────────────────────────────────────────────');
      print(response.body);
      print('─────────────────────────────────────────────');
      print('');

      try {
        final data = jsonDecode(response.body);
        print('📦 Type de données: ${data.runtimeType}');
        print('');

        if (data is List) {
          print('✅ Format: Liste (Array)');
          print('📊 Nombre de services: ${data.length}');
          print('');

          if (data.isEmpty) {
            print('⚠️  PROBLÈME: La liste est vide!');
            print('   Cela signifie qu\'aucun service n\'est configuré');
            print('   pour ce centre dans la base de données.');
          } else {
            print('📋 Liste des services:');
            for (var i = 0; i < data.length; i++) {
              final service = data[i];
              print(
                '   ${i + 1}. ID: ${service['id']}, Nom: ${service['nom']}',
              );
            }
          }
        } else if (data is Map) {
          print('✅ Format: Objet (Map)');
          print('🔑 Clés disponibles: ${data.keys.join(', ')}');
          print('');

          if (data.containsKey('services')) {
            final services = data['services'] as List?;
            print(
              '📊 Nombre de services dans [services]: ${services?.length ?? 0}',
            );
            if (services != null && services.isNotEmpty) {
              print('📋 Liste des services:');
              for (var i = 0; i < services.length; i++) {
                final service = services[i];
                print(
                  '   ${i + 1}. ID: ${service['id']}, Nom: ${service['nom']}',
                );
              }
            }
          } else if (data.containsKey('success')) {
            print('ℹ️  Réponse avec clé [success]: ${data['success']}');
            if (data.containsKey('message')) {
              print('ℹ️  Message: ${data['message']}');
            }
          } else {
            print('⚠️  Format non reconnu. Structure complète:');
            print(data);
          }
        } else {
          print('⚠️  Type de données inattendu: ${data.runtimeType}');
          print('📄 Contenu: $data');
        }
      } catch (e) {
        print('❌ Erreur lors du parsing JSON: $e');
      }
    } else {
      print('❌ Erreur HTTP ${response.statusCode}');
      print('📄 Corps de la réponse:');
      print(response.body);
    }
  } catch (e) {
    print('❌ Erreur de connexion: $e');
  }
}
