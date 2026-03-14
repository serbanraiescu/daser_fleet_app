<?php
// helpers
$eventColors = [
    'service' => 'bg-blue-100 text-blue-800 border-blue-200',
    'damage' => 'bg-red-100 text-red-800 border-red-200',
    'expense' => 'bg-orange-100 text-orange-800 border-orange-200',
    'inspection' => 'bg-green-100 text-green-800 border-green-200',
    'insurance' => 'bg-purple-100 text-purple-800 border-purple-200',
    'itp' => 'bg-teal-100 text-teal-800 border-teal-200'
];

$eventIcons = [
    'service' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
    'damage' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
    'expense' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'inspection' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'insurance' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>',
    'itp' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>'
];
?>

<div x-data="vehicleEvents()" class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center">
                <svg class="w-8 h-8 mr-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Vehicle Timeline <span class="ml-3 px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full border border-blue-200">BETA</span>
            </h1>
            <p class="text-slate-500 mt-1">Unified history of services, damages, and expenses.</p>
        </div>

        <!-- Vehicle Selector -->
        <div class="w-full md:w-72">
            <form action="" method="GET" x-ref="vehicleForm">
                <select name="vehicle_id" @change="$refs.vehicleForm.submit()" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white hover:bg-slate-50 transition-colors py-2.5">
                    <option value="">Select a vehicle...</option>
                    <?php foreach ($vehicles as $v): ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo (isset($selectedVehicle['id']) && $selectedVehicle['id'] == $v['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($v['license_plate'] . ' - ' . $v['make'] . ' ' . $v['model']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if (!$selectedVehicle): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center text-slate-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-lg">Please select a vehicle from the dropdown above to view its timeline.</p>
        </div>
    <?php else: ?>
        
        <!-- Vehicle Header Card -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl shadow-lg p-6 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl"></div>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center relative z-10 gap-6">
                <div>
                    <h2 class="text-3xl font-black tracking-tight mb-1"><?php echo htmlspecialchars($selectedVehicle['license_plate']); ?></h2>
                    <p class="text-slate-300 text-lg flex items-center">
                        <svg class="w-5 h-5 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <?php echo htmlspecialchars($selectedVehicle['make'] . ' ' . $selectedVehicle['model']); ?>
                    </p>
                </div>
                
                <div class="flex gap-8">
                    <div class="text-center bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/10">
                        <p class="text-slate-300 text-sm font-medium mb-1 uppercase tracking-wider">Current Odometer</p>
                        <p class="text-2xl font-bold font-mono"><?php echo number_format($selectedVehicle['current_odometer'] ?? 0, 0, ',', '.'); ?> <span class="text-sm font-normal opacity-70">km</span></p>
                    </div>
                    <?php if (!empty($selectedVehicle['next_service_km']) && $selectedVehicle['next_service_km'] > 0): ?>
                        <div class="text-center bg-blue-500/20 rounded-xl p-4 backdrop-blur-sm border border-blue-400/30">
                            <p class="text-blue-200 text-sm font-medium mb-1 uppercase tracking-wider">Next Service Due At</p>
                            <p class="text-2xl font-bold font-mono text-blue-100"><?php echo number_format($selectedVehicle['next_service_km'], 0, ',', '.'); ?> <span class="text-sm font-normal opacity-70">km</span></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Toolbar & Filters -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-white rounded-xl shadow-sm border border-slate-200 p-2 mb-8 sticky top-0 z-20">
            <div class="flex overflow-x-auto w-full md:w-auto p-1 gap-2 no-scrollbar">
                <button @click="filter = 'all'" :class="{'bg-slate-800 text-white': filter === 'all', 'text-slate-600 hover:bg-slate-100': filter !== 'all'}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    All Events
                </button>
                <button @click="filter = 'service'" :class="{'bg-blue-100 text-blue-800': filter === 'service', 'text-slate-600 hover:bg-slate-100': filter !== 'service'}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex items-center">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Service
                </button>
                <button @click="filter = 'damage'" :class="{'bg-red-100 text-red-800': filter === 'damage', 'text-slate-600 hover:bg-slate-100': filter !== 'damage'}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex items-center">
                    <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Damage
                </button>
                <button @click="filter = 'expense'" :class="{'bg-orange-100 text-orange-800': filter === 'expense', 'text-slate-600 hover:bg-slate-100': filter !== 'expense'}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex items-center">
                    <span class="w-2 h-2 rounded-full bg-orange-500 mr-2"></span> Expense
                </button>
            </div>
            
            <button @click="openModal('add')" class="mt-4 md:mt-0 px-6 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-shadow-sm flex items-center w-full md:w-auto justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Event
            </button>
        </div>

        <!-- Vertical Timeline -->
        <div class="relative max-w-4xl mx-auto">
            <!-- Central Line -->
            <div class="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-slate-200 transform md:-translate-x-1/2 z-0 hidden md:block"></div>
            
            <?php if (empty($events)): ?>
                <div class="text-center py-12 text-slate-500 relative z-10 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                    <p>No events recorded for this vehicle yet.</p>
                </div>
            <?php else: ?>
                <div class="space-y-8">
                    <?php 
                    $currentYearMonth = '';
                    foreach ($events as $index => $event): 
                        $eventDate = strtotime($event['event_date']);
                        $yearMonth = date('F Y', $eventDate);
                        
                        // Just an example style selection
                        $styleClass = $eventColors[$event['event_type']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                        $iconSvg = $eventIcons[$event['event_type']] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        
                        $isEven = $index % 2 === 0;
                    ?>
                        <!-- Month Separator (If changed) -->
                        <?php if ($yearMonth !== $currentYearMonth): ?>
                            <div class="relative z-10 flex justify-center w-full py-4 hidden md:flex" x-show="filter === 'all'">
                                <span class="bg-white px-4 py-1.5 rounded-full text-xs font-bold tracking-wider text-slate-500 uppercase shadow-sm border border-slate-200"><?php echo $yearMonth; ?></span>
                            </div>
                            <?php $currentYearMonth = $yearMonth; ?>
                        <?php endif; ?>

                        <!-- Timeline Item -->
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between w-full group" x-show="filter === 'all' || filter === '<?php echo $event['event_type']; ?>'">
                            
                            <!-- Left Side (Empty on mobile, occupied on desktop based on parity) -->
                            <div class="hidden md:block w-[45%] <?php echo $isEven ? 'text-right pr-8' : 'order-last text-left pl-8'; ?>">
                                <?php if ($isEven): ?>
                                    <div class="text-sm font-bold text-slate-500 mb-1"><?php echo date('d M Y', $eventDate); ?></div>
                                    <h3 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($event['event_subtype'] ?: ucfirst($event['event_type'])); ?></h3>
                                <?php else: ?>
                                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                                        <?php include __DIR__ . '/timeline_card_content.php'; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Center Marker -->
                            <div class="absolute left-4 md:left-1/2 transform -translate-x-1/2 w-10 h-10 rounded-full border-4 border-white shadow-sm flex items-center justify-center <?php echo explode(' ', $styleClass)[0]; ?> text-<?php echo explode('-', explode(' ', $styleClass)[1])[1]; ?>-600 z-20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <?php echo $iconSvg; ?>
                                </svg>
                            </div>

                            <!-- Right Side (Main card on mobile, occupied on desktop based on parity) -->
                            <div class="w-full pl-16 md:pl-0 md:w-[45%] <?php echo !$isEven ? 'text-right pr-8' : 'text-left pl-8'; ?>">
                                <?php if (!$isEven): ?>
                                    <!-- Desktop metadata -->
                                    <div class="hidden md:block">
                                        <div class="text-sm font-bold text-slate-500 mb-1"><?php echo date('d M Y', $eventDate); ?></div>
                                        <h3 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($event['event_subtype'] ?: ucfirst($event['event_type'])); ?></h3>
                                    </div>
                                    <!-- Mobile card (because Left Side is hidden on mobile) -->
                                    <div class="md:hidden bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                                        <div class="border-b border-slate-100 pb-3 mb-3">
                                            <div class="text-sm font-bold text-slate-500 mb-1"><?php echo date('d M Y', $eventDate); ?></div>
                                            <h3 class="text-lg font-bold text-slate-900 flex items-center">
                                                <span class="<?php echo $styleClass; ?> border px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider mr-2"><?php echo $event['event_type']; ?></span>
                                                <?php echo htmlspecialchars($event['event_subtype'] ?: ucfirst($event['event_type'])); ?>
                                            </h3>
                                        </div>
                                        <?php include __DIR__ . '/timeline_card_content.php'; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                                        <!-- Mobile Header inside card -->
                                        <div class="md:hidden border-b border-slate-100 pb-3 mb-3">
                                            <div class="text-sm font-bold text-slate-500 mb-1"><?php echo date('d M Y', $eventDate); ?></div>
                                            <h3 class="text-lg font-bold text-slate-900 flex items-center">
                                                <span class="<?php echo $styleClass; ?> border px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider mr-2"><?php echo $event['event_type']; ?></span>
                                                <?php echo htmlspecialchars($event['event_subtype'] ?: ucfirst($event['event_type'])); ?>
                                            </h3>
                                        </div>
                                        <!-- Shared Card Content -->
                                        <?php include __DIR__ . '/timeline_card_content.php'; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <!-- Modal for Add/Edit -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                
                <form :action="modalMode === 'add' ? '/tenant/vehicle-events/add' : '/tenant/vehicle-events/edit'" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="vehicle_id" value="<?php echo $selectedVehicle['id'] ?? ''; ?>">
                    <input type="hidden" name="event_id" x-model="formData.id">

                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                            <h3 class="text-xl leading-6 font-bold text-slate-900 flex items-center" id="modal-title">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                                <span x-text="modalMode === 'add' ? 'Add Vehicle Event' : 'Edit Event'"></span>
                            </h3>
                            <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-500 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Event Type *</label>
                                <select name="event_type" x-model="formData.event_type" required class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                                    <option value="service" class="font-medium">Service / Maintenance</option>
                                    <option value="damage">Damage / Accident</option>
                                    <option value="expense">General Expense</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="insurance">Insurance / RCA</option>
                                    <option value="itp">ITP</option>
                                </select>
                            </div>

                            <!-- Subtype -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Subtype / Title</label>
                                <input type="text" name="event_subtype" x-model="formData.event_subtype" placeholder="e.g. Oil Change, Front Bumper" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                            </div>

                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Date *</label>
                                <input type="date" name="event_date" x-model="formData.event_date" required class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                            </div>

                            <!-- Odometer -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Odometer at Event (km)</label>
                                <input type="number" name="odometer" x-model="formData.odometer" value="<?php echo $selectedVehicle['current_odometer'] ?? ''; ?>" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                            </div>

                            <!-- Cost -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Cost (RON)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <input type="number" step="0.01" name="cost" x-model="formData.cost" placeholder="0.00" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5 pl-4 pr-12">
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm font-medium">RON</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                                <select name="status" x-model="formData.status" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5">
                                    <option value="open">Open / Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="closed">Closed / Resolved</option>
                                </select>
                            </div>

                            <!-- Description (Full width) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Description / Notes</label>
                                <textarea name="description" x-model="formData.description" rows="3" class="w-full border-slate-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2.5" placeholder="Details about the event, replaced parts, etc..."></textarea>
                            </div>

                            <!-- Service Specific: Next Service KM -->
                            <div x-show="formData.event_type === 'service'" class="md:col-span-2 bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="w-full">
                                    <label class="block text-sm font-bold text-blue-900 mb-1">Update "Next Service Due At" (KM)</label>
                                    <p class="text-xs text-blue-700 mb-2">If you changed the oil/filters, enter at what mileage the next service is required.</p>
                                    <input type="number" name="next_service_km" class="w-full md:w-1/2 border-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2" placeholder="e.g. <?php echo ($selectedVehicle['current_odometer'] ?? 0) + 15000; ?>">
                                </div>
                            </div>

                            <!-- Photos Input -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Attach Photos (Optional)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:bg-slate-50 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none px-1">
                                                <span>Upload files</span>
                                                <input id="file-upload" name="photos[]" type="file" class="sr-only" multiple accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-xs text-slate-500">PNG, JPG up to 5MB (Max 6)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse rounded-b-2xl border-t border-slate-200">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2.5 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            <span x-text="modalMode === 'add' ? 'Save Event' : 'Update Event'"></span>
                        </button>
                        <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function vehicleEvents() {
    return {
        filter: 'all',
        isModalOpen: false,
        modalMode: 'add',
        formData: {
            id: '',
            event_type: 'service',
            event_subtype: '',
            event_date: new Date().toISOString().split('T')[0],
            odometer: '<?php echo $selectedVehicle['current_odometer'] ?? ''; ?>',
            cost: '',
            description: '',
            status: 'open'
        },
        openModal(mode, event = null) {
            this.modalMode = mode;
            if (mode === 'edit' && event) {
                this.formData = { ...event };
            } else {
                this.resetForm();
            }
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        resetForm() {
            this.formData = {
                id: '',
                event_type: 'service',
                event_subtype: '',
                event_date: new Date().toISOString().split('T')[0],
                odometer: '<?php echo $selectedVehicle['current_odometer'] ?? ''; ?>',
                cost: '',
                description: '',
                status: 'open'
            };
        }
    }
}
</script>
