import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'https://fleet.daserdesign.ro/api';

  Future<Map<String, dynamic>> _post(String endpoint, Map<String, dynamic> body) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: {
        'Content-Type': 'application/json',
        'Cookie': 'PHPSESSID=$token',
      },
      body: jsonEncode(body),
    ).timeout(const Duration(seconds: 15));

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      final errorData = jsonDecode(response.body);
      throw Exception(errorData['error'] ?? 'Eroare server (${response.statusCode})');
    }
  }

  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({'email': email, 'password': password}),
      ).timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', data['token']);
        await prefs.setString('user_name', data['user']['name']);
        return data;
      } else {
        final errorData = jsonDecode(response.body);
        throw Exception(errorData['error'] ?? 'Eroare login (${response.statusCode})');
      }
    } catch (e) {
      if (e.toString().contains('Failed to fetch') || e.toString().contains('XMLHttpRequest')) {
        throw Exception('Eroare conexiune (CORS). Rulează aplicația pe telefon.');
      }
      rethrow;
    }
  }

  Future<Map<String, dynamic>> getDashboard() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.get(
      Uri.parse('$baseUrl/driver/dashboard'),
      headers: {
        'Content-Type': 'application/json',
        'Cookie': 'PHPSESSID=$token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to load dashboard');
    }
  }

  Future<void> startTrip(int vehicleId, int startOdometer) async {
    await _post('/driver/trip/start', {
      'vehicle_id': vehicleId,
      'start_odometer': startOdometer,
    });
  }

  Future<void> endTrip(int endOdometer) async {
    await _post('/driver/trip/end', {
      'end_odometer': endOdometer,
    });
  }

  Future<void> logFueling({
    required int vehicleId,
    required double liters,
    required double cost,
    required int odometer,
    int isFull = 1,
    String? photoPath, // New parameter for photo
  }) async {
    if (photoPath == null) {
      await _post('/driver/fueling', {
        'vehicle_id': vehicleId,
        'liters': liters,
        'cost': cost,
        'odometer': odometer,
        'is_full': isFull,
      });
      return;
    }

    // Handle Multipart for Photo
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/driver/fueling'));
    request.headers.addAll({
      'Cookie': 'PHPSESSID=$token',
    });

    request.fields['vehicle_id'] = vehicleId.toString();
    request.fields['liters'] = liters.toString();
    request.fields['cost'] = cost.toString();
    request.fields['odometer'] = odometer.toString();
    request.fields['is_full'] = isFull.toString();

    request.files.add(await http.MultipartFile.fromPath('receipt_photo', photoPath));

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);

    if (response.statusCode != 200) {
      final errorData = jsonDecode(response.body);
      throw Exception(errorData['error'] ?? 'Eroare upload bon (${response.statusCode})');
    }
  }

  Future<void> reportDamage(int vehicleId, String description) async {
    await _post('/driver/damage', {
      'vehicle_id': vehicleId,
      'description': description,
    });
  }
}
