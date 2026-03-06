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
        title: const Text('Driver Deck'),
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
              // Info Bar
              Container(
                width: double.infinity,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                color: Colors.blue.shade50,
                child: Row(
                  children: [
                    CircleAvatar(
                      backgroundColor: Colors.blue.shade100,
                      child: Text(_data?['user_name']?.substring(0, 1).toUpperCase() ?? 'D'),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _data?['user_name'] ?? 'Șofer',
                            style: const TextStyle(fontWeight: FontWeight.bold),
                          ),
                          Text(
                            _data?['active_trip'] != null ? 'În Cursă' : 'Disponibil',
                            style: TextStyle(fontSize: 12, color: _data?['active_trip'] != null ? Colors.orange : Colors.green),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: _data?['active_trip'] != null 
                    ? _buildActiveTripUI() 
                    : _buildStartTripUI(),
                ),
              ),
            ],
          ),
    );
  }

  Widget _buildActiveTripUI() {
    final odometerController = TextEditingController();
    final trip = _data?['active_trip'];
    
    return Column(
      children: [
        Card(
          elevation: 0,
          color: Colors.orange.shade50,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16), side: BorderSide(color: Colors.orange.shade200)),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                Row(
                  children: [
                    const Icon(Icons.directions_car, color: Colors.orange),
                    const SizedBox(width: 8),
                    Text(trip['vehicle_name'] ?? 'Mașină', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                  ],
                ),
                const Divider(height: 32),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Start KM'),
                    Text('${trip['start_km']} KM', style: const TextStyle(fontWeight: FontWeight.bold)),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Start Time'),
                    Text('${trip['start_time'].toString().substring(11, 16)}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  ],
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 24),
        GridView.count(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          crossAxisCount: 2,
          crossAxisSpacing: 16,
          mainAxisSpacing: 16,
          children: [
            _actionCard(
              icon: Icons.local_gas_station,
              label: 'Log Fueling',
              color: Colors.indigo,
              onTap: () async {
                await Navigator.push(context, MaterialPageRoute(builder: (context) => FuelingPage(vehicleId: trip['vehicle_id'], vehicleName: trip['vehicle_name'])));
                _loadDashboard();
              },
            ),
            _actionCard(
              icon: Icons.report_problem,
              label: 'Report Damage',
              color: Colors.amber.shade700,
              onTap: () async {
                await Navigator.push(context, MaterialPageRoute(builder: (context) => DamageReportPage(vehicleId: trip['vehicle_id'], vehicleName: trip['vehicle_name'])));
                _loadDashboard();
              },
            ),
          ],
        ),
        const SizedBox(height: 24),
        Card(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                const Text('ÎNCHIDE CURSA', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.red)),
                const SizedBox(height: 16),
                TextField(
                  controller: odometerController,
                  keyboardType: TextInputType.number,
                  decoration: const InputDecoration(
                    labelText: 'Kilometraj Final',
                    border: OutlineInputBorder(),
                    suffixText: 'KM',
                  ),
                ),
                const SizedBox(height: 16),
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: () => _handleEndTrip(odometerController.text),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.red,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('FINALIZEAZĂ'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStartTripUI() {
    final vehicles = _data?['vehicles'] as List? ?? [];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text('Alege mașina:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        const SizedBox(height: 16),
        ...vehicles.map((v) => Card(
          margin: const EdgeInsets.only(bottom: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          child: ListTile(
            leading: const Icon(Icons.directions_car, color: Colors.blue),
            title: Text('${v['make']} ${v['model']}'),
            subtitle: Text(v['license_plate']),
            trailing: const Icon(Icons.play_circle_fill, color: Colors.green, size: 32),
            onTap: () => _showStartTripDialog(v),
          ),
        )).toList(),
        
        const SizedBox(height: 24),
        const Text('Alte acțiuni:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        const SizedBox(height: 12),
        ListTile(
          tileColor: Colors.amber.shade50,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: BorderSide(color: Colors.amber.shade200)),
          leading: const Icon(Icons.report_problem, color: Colors.amber),
          title: const Text('Raportează Daună (Fără Cursă)'),
          onTap: () {
             // Show a picker or a simplified damage report
             _showDamageVehiclePicker();
          },
        ),
      ],
    );
  }

  void _showDamageVehiclePicker() {
    final vehicles = _data?['vehicles'] as List? ?? [];
    showModalBottomSheet(
      context: context,
      builder: (context) => ListView(
        children: [
          const Padding(
            padding: EdgeInsets.all(16),
            child: Text('Alege mașina lovită:', style: TextStyle(fontWeight: FontWeight.bold)),
          ),
          ...vehicles.map((v) => ListTile(
            title: Text('${v['license_plate']} - ${v['make']}'),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(context, MaterialPageRoute(builder: (context) => DamageReportPage(vehicleId: v['id'], vehicleName: v['license_plate'])));
            },
          )).toList(),
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
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
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
        title: Text('Start Trip: ${vehicle['license_plate']}'),
        content: TextField(
          controller: controller,
          keyboardType: TextInputType.number,
          decoration: const InputDecoration(labelText: 'Kilometraj Actual'),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('ANULEAZĂ')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              setState(() => _isLoading = true);
              try {
                await _apiService.startTrip(vehicle['id'], int.parse(controller.text));
                _loadDashboard();
              } catch (e) {
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
                }
              } finally {
                if (mounted) setState(() => _isLoading = false);
              }
            },
            child: const Text('PORNEȘTE'),
          ),
        ],
      ),
    );
  }

  Widget _actionCard({required IconData icon, required String label, required Color color, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: color,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [BoxShadow(color: color.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 4))],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: Colors.white, size: 32),
            const SizedBox(height: 8),
            Text(label, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
          ],
        ),
      ),
    );
  }
}

class FuelingPage extends StatefulWidget {
  final int vehicleId;
  final String vehicleName;
  const FuelingPage({super.key, required this.vehicleId, required this.vehicleName});

  @override
  State<FuelingPage> createState() => _FuelingPageState();
}

class _FuelingPageState extends State<FuelingPage> {
  final _litersController = TextEditingController();
  final _costController = TextEditingController();
  final _odometerController = TextEditingController();
  final _apiService = ApiService();
  bool _isLoading = false;

  void _handleSave() async {
    if (_litersController.text.isEmpty || _costController.text.isEmpty || _odometerController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Toate câmpurile sunt obligatorii')));
      return;
    }

    setState(() => _isLoading = true);
    try {
      await _apiService.logFueling(
        vehicleId: widget.vehicleId,
        liters: double.parse(_litersController.text),
        cost: double.parse(_costController.text),
        odometer: int.parse(_odometerController.text),
      );
      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Alimentare salvată cu succes'), backgroundColor: Colors.green));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Log Alimentare')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Text(widget.vehicleName, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 24),
            TextField(
              controller: _litersController,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              decoration: const InputDecoration(labelText: 'Litri', border: OutlineInputBorder(), prefixIcon: Icon(Icons.gas_meter)),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _costController,
              keyboardType: const TextInputType.numberWithOptions(decimal: true),
              decoration: const InputDecoration(labelText: 'Cost Total', border: OutlineInputBorder(), prefixIcon: Icon(Icons.payments)),
            ),
            const SizedBox(height: 16),
            TextField(
              controller: _odometerController,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(labelText: 'Kilometraj', border: OutlineInputBorder(), prefixIcon: Icon(Icons.speed)),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _handleSave,
                style: ElevatedButton.styleFrom(backgroundColor: Colors.indigo, foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                child: _isLoading ? const CircularProgressIndicator(color: Colors.white) : const Text('SALVEAZĂ ALIMENTARE', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class DamageReportPage extends StatefulWidget {
  final int vehicleId;
  final String vehicleName;
  const DamageReportPage({super.key, required this.vehicleId, required this.vehicleName});

  @override
  State<DamageReportPage> createState() => _DamageReportPageState();
}

class _DamageReportPageState extends State<DamageReportPage> {
  final _descController = TextEditingController();
  final _apiService = ApiService();
  bool _isLoading = false;

  void _handleSave() async {
    if (_descController.text.isEmpty) return;

    setState(() => _isLoading = true);
    try {
      await _apiService.reportDamage(widget.vehicleId, _descController.text);
      if (mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Raport trimis cu succes'), backgroundColor: Colors.green));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Raportează Daună')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Text(widget.vehicleName, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 24),
            TextField(
              controller: _descController,
              maxLines: 5,
              decoration: const InputDecoration(
                labelText: 'Descriere Daună',
                hintText: 'Ex: Zgârietură bară spate, parbriz fisurat...',
                border: OutlineInputBorder(),
              ),
            ),
            const SizedBox(height: 32),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _handleSave,
                style: ElevatedButton.styleFrom(backgroundColor: Colors.amber.shade700, foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                child: _isLoading ? const CircularProgressIndicator(color: Colors.white) : const Text('TRIMITE RAPORT', style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
