import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'https://fleet.daserdesign.ro/api'; // Update to your real URL

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
        throw Exception(errorData['error'] ?? 'Eroare necunoscută (${response.statusCode})');
      }
    } catch (e) {
      print('DEBUG API ERROR: $e');
      if (e.toString().contains('Failed to fetch') || e.toString().contains('XMLHttpRequest')) {
        throw Exception('Eroare conexiune (CORS). Încearcă să rulezi pe telefon sau emulator, nu în Chrome.');
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
        'Cookie': 'PHPSESSID=$token', // Using session_id as token
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to load dashboard');
    }
  }

  Future<void> startTrip(int vehicleId, int startOdometer) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.post(
      Uri.parse('$baseUrl/driver/trip/start'),
      headers: {
        'Content-Type': 'application/json',
        'Cookie': 'PHPSESSID=$token',
      },
      body: jsonEncode({
        'vehicle_id': vehicleId,
        'start_odometer': startOdometer,
      }),
    );

    if (response.statusCode != 200) {
      throw Exception(jsonDecode(response.body)['error'] ?? 'Failed to start trip');
    }
  }

  Future<void> endTrip(int endOdometer) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final response = await http.post(
      Uri.parse('$baseUrl/driver/trip/end'),
      headers: {
        'Content-Type': 'application/json',
        'Cookie': 'PHPSESSID=$token',
      },
      body: jsonEncode({
        'end_odometer': endOdometer,
      }),
    );

    if (response.statusCode != 200) {
      throw Exception(jsonDecode(response.body)['error'] ?? 'Failed to end trip');
    }
  }
}
