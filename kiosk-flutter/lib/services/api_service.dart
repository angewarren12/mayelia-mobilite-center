import 'dart:convert';
import 'dart:developer' as developer;
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/ticket.dart';
import '../models/service.dart';

class ApiService {
  final String baseUrl;

  ApiService({String? baseUrl}) : baseUrl = baseUrl ?? ApiConfig.baseUrl;

  Future<Map<String, dynamic>?> getCentreInfo(int centreId) async {
    try {
      final url = ApiConfig.getCentreInfo(centreId);
      developer.log('API getCentreInfo - URL: $url');

      final response = await http.get(
        Uri.parse(url),
        headers: {'Accept': 'application/json'},
      );

      developer.log('API getCentreInfo - Status: ${response.statusCode}');
      developer.log('API getCentreInfo - Response: ${response.body}');

      if (response.statusCode == 200) {
        return jsonDecode(response.body) as Map<String, dynamic>;
      }
      return null;
    } catch (e) {
      developer.log('API getCentreInfo - Exception: $e', name: 'ERROR');
      return null;
    }
  }

  Future<Map<String, dynamic>> checkRdv({
    required String numero,
    required int centreId,
  }) async {
    try {
      final response = await http.post(
        Uri.parse(ApiConfig.checkRdv()),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({'numero': numero, 'centre_id': centreId}),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body) as Map<String, dynamic>;
      } else {
        return {
          'success': false,
          'message': 'Erreur lors de la vérification du RDV',
        };
      }
    } catch (e) {
      return {'success': false, 'message': 'Erreur de connexion: $e'};
    }
  }

  Future<Map<String, dynamic>> createTicket({
    required int centreId,
    required String type,
    int? serviceId,
    String? numeroRdv,
  }) async {
    try {
      final body = {
        'centre_id': centreId,
        'type': type,
        if (serviceId != null) 'service_id': serviceId,
        if (numeroRdv != null && numeroRdv.isNotEmpty) 'numero_rdv': numeroRdv,
      };

      developer.log('API createTicket - URL: ${ApiConfig.storeTicket()}');
      developer.log('API createTicket - Body: $body');

      final response = await http.post(
        Uri.parse(ApiConfig.storeTicket()),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(body),
      );

      developer.log('API createTicket - Status: ${response.statusCode}');
      developer.log('API createTicket - Response: ${response.body}');

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body) as Map<String, dynamic>;
        if (data['success'] == true && data['ticket'] != null) {
          data['ticket'] = Ticket.fromJson(
            data['ticket'] as Map<String, dynamic>,
          );
        }
        return data;
      } else {
        final errorData = jsonDecode(response.body) as Map<String, dynamic>?;
        return {
          'success': false,
          'message':
              errorData?['message'] ??
              'Erreur lors de la création du ticket (${response.statusCode})',
        };
      }
    } catch (e) {
      developer.log('API createTicket - Exception: $e', name: 'ERROR');
      return {'success': false, 'message': 'Erreur de connexion: $e'};
    }
  }

  Future<List<Service>> getServices(int centreId) async {
    try {
      final url = ApiConfig.getServices(centreId);
      developer.log('API getServices - URL: $url');

      final response = await http.get(
        Uri.parse(url),
        headers: {'Accept': 'application/json'},
      );

      developer.log('API getServices - Status: ${response.statusCode}');
      developer.log('API getServices - Response: ${response.body}');

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        developer.log(
          'API getServices - Decoded data type: ${data.runtimeType}',
        );
        developer.log('API getServices - Decoded data: $data');

        // L'API retourne directement un array ou un objet avec 'services'
        if (data is List) {
          final services = data
              .map((json) => Service.fromJson(json as Map<String, dynamic>))
              .toList();
          developer.log(
            'API getServices - Parsed ${services.length} services from List',
          );
          return services;
        } else if (data is Map) {
          if (data.containsKey('services')) {
            final services = ((data['services'] as List)
                .map((json) => Service.fromJson(json as Map<String, dynamic>))
                .toList());
            developer.log(
              'API getServices - Parsed ${services.length} services from Map[services]',
            );
            return services;
          } else if (data.containsKey('success') &&
              data['success'] == true &&
              data.containsKey('data')) {
            // Gérer le cas où l'API retourne {success: true, data: [...]}
            final services = ((data['data'] as List)
                .map((json) => Service.fromJson(json as Map<String, dynamic>))
                .toList());
            developer.log(
              'API getServices - Parsed ${services.length} services from Map[data]',
            );
            return services;
          }
        }
        developer.log(
          'API getServices - No services found in response structure',
        );
      } else {
        developer.log(
          'API getServices - Non-200 status: ${response.statusCode}',
          name: 'ERROR',
        );
      }
      return [];
    } catch (e) {
      developer.log('API getServices - Exception: $e', name: 'ERROR');
      return [];
    }
  }
}
