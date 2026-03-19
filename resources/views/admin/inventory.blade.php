@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="space-y-6">

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Items Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-boxes mr-2"></i>
                    Total Items
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $inventories->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All inventory items</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-box text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Low Stock
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-red-600">{{ $inventories->where('quantity', '<=', $inventories->pluck('low_stock_threshold'))->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Items below threshold</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-exclamation text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Stock Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    In Stock
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-green-600">{{ $inventories->where('quantity', '>', $inventories->pluck('low_stock_threshold'))->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Healthy inventory levels</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIONS BAR -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventory Items</h2>
            <p class="text-gray-500 text-sm mt-1">Manage your medical supplies and equipment</p>
        </div>
        <button id="showAddModal" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200">
            <i class="fa fa-plus-circle"></i>
            Add New Item
        </button>
    </div>

    <!-- INVENTORY TABLE -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Item Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Threshold</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inventories as $item)
                        @php
                            $isLowStock = $item->quantity <= $item->low_stock_threshold;
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $isLowStock ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fa fa-capsules text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->category)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                        {{ $item->category }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono font-medium {{ $isLowStock ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $item->unit ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-gray-600">{{ $item->low_stock_threshold }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isLowStock)
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold flex items-center w-fit mx-auto">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                        Low Stock
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold flex items-center w-fit mx-auto">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        class="editBtn w-8 h-8 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition flex items-center justify-center"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-category="{{ $item->category }}"
                                        data-quantity="{{ $item->quantity }}"
                                        data-unit="{{ $item->unit }}"
                                        data-threshold="{{ $item->low_stock_threshold }}"
                                        title="Edit item"
                                    >
                                        <i class="fa fa-pen text-sm"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.inventory.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition flex items-center justify-center" title="Delete item">
                                            <i class="fa fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fa fa-box-open text-5xl mb-3"></i>
                                    <p class="text-lg font-medium">No inventory items found</p>
                                    <p class="text-sm">Click the "Add New Item" button to create your first item</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="inventoryModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative animate-fade-in">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800" id="modalTitle">Add New Item</h2>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="modalForm" method="POST" action="{{ route('admin.inventory.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="inventory_id" id="inventoryId" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Item Name -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fa fa-tag text-blue-500 mr-1"></i>
                        Item Name
                    </label>
                    <input type="text" name="name" id="name" 
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                        placeholder="e.g., Paracetamol"
                        required>
                </div>

                <!-- Category -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fa fa-folder text-yellow-500 mr-1"></i>
                        Category
                    </label>
                    <input type="text" name="category" id="category" 
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                        placeholder="e.g., Medicine">
                </div>

                <!-- Quantity -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fa fa-cubes text-green-500 mr-1"></i>
                        Quantity
                    </label>
                    <input type="number" name="quantity" id="quantity" 
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                        placeholder="0"
                        min="0" 
                        required>
                </div>

                <!-- Unit -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fa fa-balance-scale text-purple-500 mr-1"></i>
                        Unit
                    </label>
                    <input type="text" name="unit" id="unit" 
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                        placeholder="e.g., tablet, bottle">
                </div>

                <!-- Low Stock Threshold -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fa fa-exclamation-triangle text-red-500 mr-1"></i>
                        Low Stock Threshold
                    </label>
                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" 
                        class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                        placeholder="e.g., 10"
                        min="0">
                    <p class="text-xs text-gray-500 mt-1">Alert when quantity drops below this number</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button" id="cancelForm" 
                    class="px-5 py-2.5 border-2 border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200 flex items-center gap-2"
                    id="formSubmitBtn">
                    <i class="fa fa-save"></i>
                    Add Item
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script>
    const showAddModalBtn = document.getElementById('showAddModal');
    const inventoryModal = document.getElementById('inventoryModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelFormBtn = document.getElementById('cancelForm');
    const modalForm = document.getElementById('modalForm');
    const formMethod = document.getElementById('formMethod');
    const inventoryIdInput = document.getElementById('inventoryId');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('formSubmitBtn');

    // Show Add Modal
    showAddModalBtn.addEventListener('click', () => {
        inventoryModal.classList.remove('hidden');
        modalForm.reset();
        formMethod.value = 'POST';
        modalForm.action = "{{ route('admin.inventory.store') }}";
        modalTitle.textContent = 'Add New Item';
        submitBtn.innerHTML = '<i class="fa fa-save"></i> Add Item';
    });

    // Close Modal
    closeModalBtn.addEventListener('click', () => inventoryModal.classList.add('hidden'));
    cancelFormBtn.addEventListener('click', () => inventoryModal.classList.add('hidden'));

    // Edit buttons
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const category = btn.dataset.category;
            const quantity = btn.dataset.quantity;
            const unit = btn.dataset.unit;
            const threshold = btn.dataset.threshold;

            inventoryModal.classList.remove('hidden');
            formMethod.value = 'PUT';
            modalForm.action = `/admin/inventory/${id}`;
            modalTitle.textContent = 'Edit Item';
            submitBtn.innerHTML = '<i class="fa fa-save"></i> Update Item';

            inventoryIdInput.value = id;
            document.getElementById('name').value = name;
            document.getElementById('category').value = category;
            document.getElementById('quantity').value = quantity;
            document.getElementById('unit').value = unit;
            document.getElementById('low_stock_threshold').value = threshold;
        });
    });

    // Close modal when clicking outside
    inventoryModal.addEventListener('click', (e) => {
        if (e.target === inventoryModal) {
            inventoryModal.classList.add('hidden');
        }
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
</style>
@endsection