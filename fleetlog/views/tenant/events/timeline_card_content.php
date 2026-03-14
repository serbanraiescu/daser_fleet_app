<div class="flex justify-between items-start mb-2">
    <div>
        <!-- Desktop Badge -->
        <span class="hidden md:inline-block <?php echo $styleClass; ?> border px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider mb-2">
            <?php echo $event['is_fueling'] ? __('fueling_event') : __('filter_' . $event['event_type']); ?>
        </span>
        
        <?php if (!empty($event['cost']) && $event['cost'] > 0): ?>
            <p class="text-xs font-semibold text-slate-500 bg-slate-100 rounded border border-slate-200 px-2 py-1 inline-block uppercase tracking-wide">
                <?php echo __('cost_label'); ?> <span class="text-slate-700"><?php echo number_format($event['cost'], 2, ',', '.'); ?> RON</span>
            </p>
        <?php elseif (!empty($event['total_price']) && $event['total_price'] > 0): ?>
            <p class="text-xs font-semibold text-slate-500 bg-slate-100 rounded border border-slate-200 px-2 py-1 inline-block uppercase tracking-wide">
                <?php echo __('cost_label'); ?> <span class="text-slate-700"><?php echo number_format($event['total_price'], 2, ',', '.'); ?> RON</span>
            </p>
        <?php endif; ?>
    </div>
    
    <!-- Actions Menu (Hide for fuelings for now as they are read-only in this timeline) -->
    <?php if (!$event['is_fueling']): ?>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
            </button>
            <div x-show="open" style="display: none;" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 z-50 py-1"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95">
                <button @click="openModal('edit', {
                    id: '<?php echo $event['id']; ?>',
                    event_type: '<?php echo $event['event_type']; ?>',
                    event_subtype: '<?php echo htmlspecialchars(addslashes($event['event_subtype'] ?? '')); ?>',
                    event_date: '<?php echo $event['event_date']; ?>',
                    odometer: '<?php echo $event['odometer']; ?>',
                    cost: '<?php echo $event['cost']; ?>',
                    description: `<?php echo htmlspecialchars(addslashes($event['description'] ?? '')); ?>`,
                    status: '<?php echo $event['status']; ?>'
                }); open = false" class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    <?php echo __('edit_event'); ?>
                </button>
                <form action="/tenant/vehicle-events/delete" method="POST" onsubmit="return confirm('<?php echo htmlspecialchars(__('confirm_delete_event'), ENT_QUOTES); ?>');">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <input type="hidden" name="vehicle_id" value="<?php echo $event['vehicle_id']; ?>">
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center font-medium">
                        <svg class="w-4 h-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        <?php echo __('delete_event'); ?>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Fueling Details -->
<?php if ($event['is_fueling']): ?>
    <div class="bg-lime-50 rounded-lg p-3 mb-3 border border-lime-100">
        <div class="flex items-center justify-between text-sm">
            <div class="flex items-center text-lime-800">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a2 2 0 00-1.96 1.414l-.727 2.903a2 2 0 01-1.96 1.414h-3.344a2 2 0 01-1.96-1.414l-.727-2.903a2 2 0 00-1.96-1.414l-2.387.477a2 2 0 00-1.022.547L3 18v2a2 2 0 002 2h14a2 2 0 002-2v-2l-1.572-2.572z"></path></svg>
                <span class="font-bold"><?php echo number_format($event['liters'], 2, ',', '.'); ?> L</span>
            </div>
            <?php if (!empty($event['total_price']) && $event['liters'] > 0): ?>
                <div class="text-slate-500 text-xs">
                    <?php echo __('price_per_l'); ?> <?php echo number_format($event['total_price'] / $event['liters'], 2, ',', '.'); ?> RON
                </div>
            <?php endif; ?>
            <?php if ($event['is_full']): ?>
                <span class="bg-lime-200 text-lime-900 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><?php echo __('full_tank'); ?></span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($event['description'])): ?>
    <p class="text-slate-600 text-sm mb-3">
        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
    </p>
<?php endif; ?>

<div class="flex flex-wrap items-center gap-3 mt-4 pt-3 border-t border-slate-100">
    <?php if (!empty($event['odometer'])): ?>
        <div class="flex items-center text-xs text-slate-500 font-medium bg-slate-50 px-2 py-1 rounded-md border border-slate-200">
            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <?php echo number_format($event['odometer'], 0, ',', '.'); ?> km
        </div>
    <?php endif; ?>

    <?php if (!$event['is_fueling']): ?>
        <div class="flex items-center text-xs text-slate-500 font-medium bg-slate-50 px-2 py-1 rounded-md border border-slate-200">
            <span class="w-2 h-2 rounded-full mr-1.5 
                <?php 
                    echo $event['status'] === 'closed' ? 'bg-green-500' : 
                        ($event['status'] === 'in_progress' ? 'bg-yellow-500' : 'bg-slate-400'); 
                ?>">
            </span>
            <?php echo __('status_' . $event['status']); ?>
        </div>
    <?php endif; ?>
    
    <?php 
        $user_name = $event['created_by_name'] ?? ($event['driver_name'] ?? null);
        if ($user_name): 
    ?>
        <div class="text-xs text-slate-400 font-medium ml-auto flex items-center">
            <svg class="w-3.5 h-3.5 mr-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <?php echo htmlspecialchars($user_name); ?>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($event['photos'])): ?>
    <div class="mt-4 flex gap-2 overflow-x-auto pb-2 no-scrollbar">
        <?php foreach ($event['photos'] as $photo): ?>
            <a href="/<?php echo $photo['path']; ?>" target="_blank" class="flex-shrink-0 relative group">
                <img src="/<?php echo $photo['path']; ?>" alt="Event photo" class="w-16 h-16 object-cover rounded-lg border border-slate-200 shadow-sm group-hover:border-blue-400 transition-colors">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php elseif (!empty($event['receipt_photo'])): ?>
    <div class="mt-4">
        <a href="/<?php echo $event['receipt_photo']; ?>" target="_blank" class="flex-shrink-0 relative group inline-block">
            <img src="/<?php echo $event['receipt_photo']; ?>" alt="Receipt photo" class="w-16 h-16 object-cover rounded-lg border border-slate-200 shadow-sm group-hover:border-blue-400 transition-colors">
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
            </div>
        </a>
    </div>
<?php endif; ?>
