import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'api_service.dart';

void main() {
  runApp(const FleetLogApp());
}

class FleetLogApp extends StatelessWidget {
  const FleetLogApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'FleetLog Mobile',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue),
        useMaterial3: true,
        textTheme: GoogleFonts.interTextTheme(),
      ),
      home: const LoginPage(),
    );
  }
}

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _apiService = ApiService();
  bool _isLoading = false;

  void _handleLogin() async {
    setState(() => _isLoading = true);
    try {
      await _apiService.login(_emailController.text, _passwordController.text);
      if (mounted) {
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (context) => const DashboardPage()),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Eroare: ${e.toString()}'), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Colors.blue, Colors.indigo],
          ),
        ),
        child: Center(
          child: SingleChildScrollView(
            child: Card(
              margin: const EdgeInsets.all(32),
              elevation: 8,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.local_shipping, size: 48, color: Colors.blue),
                    const SizedBox(height: 16),
                    const Text(
                      'FleetLog Dash',
                      style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    const Text('Driver Portal', style: TextStyle(color: Colors.grey)),
                    const SizedBox(height: 24),
                    TextField(
                      controller: _emailController,
                      decoration: const InputDecoration(
                        labelText: 'Email',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.email),
                      ),
                    ),
                    const SizedBox(height: 16),
                    TextField(
                      controller: _passwordController,
                      obscureText: true,
                      decoration: const InputDecoration(
                        labelText: 'Parolă',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.lock),
                      ),
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: _isLoading ? null : _handleLogin,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.blue,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        child: _isLoading 
                          ? const CircularProgressIndicator(color: Colors.white) 
                          : const Text('AUTENTIFICARE', style: TextStyle(fontWeight: FontWeight.bold)),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  final _apiService = ApiService();
  Map<String, dynamic>? _data;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDashboard();
  }

  void _loadDashboard() async {
    setState(() => _isLoading = true);
    try {
      final res = await _apiService.getDashboard();
      setState(() => _data = res);
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Eroare la încărcarea datelor')),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Control Panou Șofer'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadDashboard,
          ),
        ],
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : Column(
            children: [
              // Info Card
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(24),
                color: Colors.blue.shade50,
                child: Column(
                  children: [
                    const Icon(Icons.person_pin, size: 64, color: Colors.blue),
                    const SizedBox(height: 8),
                    Text(
                      'Salut, ${_data?['user_name'] ?? 'Șofer'}!',
                      style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ),
              
              // Status & Trips
              Expanded(
                child: _data?['active_trip'] != null 
                  ? _buildActiveTripUI() 
                  : _buildStartTripUI(),
              ),
            ],
          ),
    );
  }

  void _handleEndTrip(String odometer) async {
    if (odometer.isEmpty) return;
    setState(() => _isLoading = true);
    try {
      await _apiService.endTrip(int.parse(odometer));
      _loadDashboard();
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(e.toString()), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showStartTripDialog(Map<String, dynamic> vehicle) {
    final controller = TextEditingController(text: vehicle['current_odometer'].toString());
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Plecare cu ${vehicle['license_plate']}'),
        content: TextField(
          controller: controller,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(labelText: 'Confirmă Kilometraj Start'),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Anulează')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              setState(() => _isLoading = true);
              try {
                await _apiService.startTrip(vehicle['id'], int.parse(controller.text));
                _loadDashboard();
              } catch (e) {
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text(e.toString()), backgroundColor: Colors.red),
                  );
                }
              } finally {
                if (mounted) setState(() => _isLoading = false);
              }
            },
            child: const Text('START'),
          ),
        ],
      ),
    );
  }

  Widget _buildActiveTripUI() {
    final controller = TextEditingController();
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.timer, size: 80, color: Colors.orange),
            const SizedBox(height: 16),
            const Text('CURSĂ ACTIVĂ', style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.orange)),
            const SizedBox(height: 24),
            Text('Mașină: ${_data?['active_trip']['vehicle_name'] ?? 'In Curs...'}', style: const TextStyle(fontSize: 18)),
            const SizedBox(height: 32),
            TextField(
              controller: controller,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                labelText: 'Kilometraj Final',
                border: OutlineInputBorder(),
                suffixText: 'KM',
              ),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              height: 60,
              child: ElevatedButton(
                onPressed: () => _handleEndTrip(controller.text),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: const Text('ÎNCHIDE CURSA', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStartTripUI() {
    return Column(
      children: [
        const Padding(
          padding: EdgeInsets.all(16.0),
          child: Text('Selectează o mașină pentru a pleca la drum:', style: TextStyle(fontWeight: FontWeight.bold)),
        ),
        Expanded(
          child: ListView.builder(
            itemCount: (_data?['vehicles'] as List?)?.length ?? 0,
            itemBuilder: (context, index) {
              final v = _data?['vehicles'][index];
              return ListTile(
                leading: const Icon(Icons.directions_car, color: Colors.blue),
                title: Text('${v['make']} ${v['model']}'),
                subtitle: Text(v['license_plate']),
                trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                onTap: () => _showStartTripDialog(v),
              );
            },
          ),
        ),
      ],
    );
  }
}
