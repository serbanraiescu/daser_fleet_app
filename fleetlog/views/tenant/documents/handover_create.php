<?php
// Prepare a JSON object for vehicle data to use in JS auto-population
$vehicleData = [];
foreach ($vehicles as $v) {
    $vehicleData[$v['id']] = [
        'plate' => $v['license_plate'],
        'model' => $v['make'] . ' ' . $v['model'],
        'odometer' => $v['current_odometer']
    ];
}
?>

<div class="max-w-4xl mx-auto" x-data="{ 
    vehicles: <?php echo htmlspecialchars(json_encode($vehicleData)); ?>,
    selectedVehicleId: '',
    plate: '',
    model: '',
    odometer: 0,
    allDocs: false,
    regCert: false,
    insurance: false,
    itp: false,
    rovinieta: false,
    
    updateVehicle() {
        if (this.selectedVehicleId && this.vehicles[this.selectedVehicleId]) {
            let v = this.vehicles[this.selectedVehicleId];
            this.plate = v.plate;
            this.model = v.model;
            this.odometer = v.odometer;
        }
    },
    
    toggleAllDocs() {
        this.regCert = this.allDocs;
        this.insurance = this.allDocs;
        this.itp = this.allDocs;
        this.rovinieta = this.allDocs;
    }
}">
    <div class="mb-8">
        <a href="/tenant/documents" class="text-slate-500 hover:text-slate-900 flex items-center mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <?php echo __('cancel'); ?>
        </a>
        <h1 class="text-3xl font-black text-slate-900"><?php echo __('generate_pv'); ?></h1>
    </div>

    <form action="/tenant/documents/handover/add" method="POST" class="space-y-6">
        <!-- 1. Selection & Core Info -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2"><?php echo __('select_vehicle'); ?></label>
                    <select name="vehicle_id" x-model="selectedVehicleId" @change="updateVehicle()" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value=""><?php echo __('choose_vehicle'); ?></option>
                        <?php foreach ($vehicles as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['license_plate'] . ' - ' . $v['make'] . ' ' . $v['model']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Selectează Șoferul</label>
                    <select name="driver_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="">-- Alege șofer --</option>
                        <?php foreach ($drivers as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Pre-populated/Editable info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('vehicle_plate'); ?></label>
                    <input type="text" name="vehicle_plate" x-model="plate" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Model</label>
                    <input type="text" name="vehicle_model" x-model="model" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo __('current_odometer'); ?></label>
                    <input type="number" name="odometer" x-model="odometer" required class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold">
                </div>
            </div>
        </div>

        <!-- 2. Fuel Level -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Nivel Combustibil (Fuel Level)</label>
            <div class="grid grid-cols-5 gap-2">
                <?php 
                $levels = ['empty', '25', '50', '75', 'full'];
                foreach ($levels as $lvl): 
                ?>
                <label class="cursor-pointer group">
                    <input type="radio" name="fuel_level" value="<?php echo $lvl; ?>" <?php echo $lvl === '50' ? 'checked' : ''; ?> class="peer hidden">
                    <div class="text-center py-3 rounded-xl border-2 border-slate-100 bg-slate-50 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all text-xs font-black uppercase tracking-widest">
                        <?php echo __("fuel_$lvl"); ?>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3. Documents -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <label class="text-xs font-black text-slate-500 uppercase tracking-widest">Documente prezente în vehicul</label>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" x-model="allDocs" @change="toggleAllDocs()" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-xs font-black text-indigo-600 uppercase tracking-widest"><?php echo __('all_docs_present'); ?></span>
                </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:bg-white hover:border-indigo-200 transition-all">
                    <input type="checkbox" name="doc_registration" x-model="regCert" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="ml-3 text-sm font-bold text-slate-700"><?php echo __('reg_cert'); ?></span>
                </label>
                <label class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:bg-white hover:border-indigo-200 transition-all">
                    <input type="checkbox" name="doc_insurance" x-model="insurance" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="ml-3 text-sm font-bold text-slate-700"><?php echo __('insurance_rca'); ?></span>
                </label>
                <label class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:bg-white hover:border-indigo-200 transition-all">
                    <input type="checkbox" name="doc_itp" x-model="itp" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="ml-3 text-sm font-bold text-slate-700"><?php echo __('itp_label'); ?></span>
                </label>
                <label class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-100 cursor-pointer hover:bg-white hover:border-indigo-200 transition-all">
                    <input type="checkbox" name="doc_rovinieta" x-model="rovinieta" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <span class="ml-3 text-sm font-bold text-slate-700"><?php echo __('rovinieta_label'); ?></span>
                </label>
            </div>
        </div>

        <!-- 4. Condition -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4"><?php echo __('aesthetic_cond_label'); ?></label>
                <div class="space-y-2">
                    <?php 
                    $aesthetic = [
                        'good' => 'aesthetic_good',
                        'minor_wear' => 'aesthetic_minor_wear',
                        'damages' => 'aesthetic_damages'
                    ];
                    foreach ($aesthetic as $val => $key): 
                    ?>
                    <label class="flex items-center p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-indigo-200 transition-all">
                        <input type="radio" name="aesthetic_condition" value="<?php echo $val; ?>" <?php echo $val === 'good' ? 'checked' : ''; ?> class="w-4 h-4 text-indigo-600">
                        <span class="ml-3 text-sm font-bold text-slate-700"><?php echo __($key); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-4"><?php echo __('mech_cond_label'); ?></label>
                <div class="space-y-2">
                    <?php 
                    $mech = [
                        'ok' => 'mech_ok',
                        'needs_check' => 'mech_check',
                        'issue' => 'mech_issue'
                    ];
                    foreach ($mech as $val => $key): 
                    ?>
                    <label class="flex items-center p-3 rounded-xl border border-slate-100 cursor-pointer hover:border-indigo-200 transition-all">
                        <input type="radio" name="mechanical_condition" value="<?php echo $val; ?>" <?php echo $val === 'ok' ? 'checked' : ''; ?> class="w-4 h-4 text-indigo-600">
                        <span class="ml-3 text-sm font-bold text-slate-700"><?php echo __($key); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 5. Notes -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2"><?php echo __('notes_optional'); ?></label>
            <textarea name="notes" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="<?php echo __('any_specific_details'); ?>"></textarea>
        </div>

        <!-- Action -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-lg shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition-all">
                <?php echo __('generate_pv'); ?>
            </button>
        </div>
    </form>
</div>
