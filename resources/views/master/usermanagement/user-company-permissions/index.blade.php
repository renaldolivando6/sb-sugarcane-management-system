{{-- resources/views/master/usermanagement/user-company-permissions/index.blade.php --}}
<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <x-slot:navbar>{{ $navbar }}</x-slot:navbar>
    <x-slot:nav>{{ $nav }}</x-slot:nav>

    <!-- Success/Error Notifications -->
    @if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer hover:bg-green-200 rounded"
            @click="show = false">&times;</span>
    </div>
    @endif

    @if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition
        class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer hover:bg-red-200 rounded"
            @click="show = false">&times;</span>
    </div>
    @endif

    <div x-data="{
        open: false,
        companyModal: false,
        viewCompaniesModal: false,
        selectedUser: null,
        selectedUserName: '',
        selectedCompanies: [],
        viewCompanies: [],
        isLoadingCompanies: false,
        form: {
            userid: '',
            companycodes: []
        },
        availableCompanies: @js($companies->toArray()),
        
        resetForm() {
            this.form = {
                userid: '',
                companycodes: []
            };
            this.open = true;
        },
        
        showCompaniesModal(data) {
            this.selectedUser = data.userid;
            this.selectedUserName = data.name;
            this.viewCompanies = data.companies || [];
            this.viewCompaniesModal = true;
        },
        
        editCompanies(user) {
            this.selectedUser = user.userid;
            this.selectedUserName = user.name;
            this.selectedCompanies = user.userCompanies.map(uc => uc.companycode);
            this.isLoadingCompanies = false;
            this.companyModal = true;
        },
        
        toggleCompany(companycode) {
            const index = this.selectedCompanies.indexOf(companycode);
            if (index > -1) {
                this.selectedCompanies.splice(index, 1);
            } else {
                this.selectedCompanies.push(companycode);
            }
        },
        
        isCompanySelected(companycode) {
            return this.selectedCompanies.includes(companycode);
        },
        
        selectAllCompanies() {
            this.selectedCompanies = this.availableCompanies.map(company => company.companycode);
        },
        
        deselectAllCompanies() {
            this.selectedCompanies = [];
        }
    }" class="mx-auto py-4 bg-white rounded-md shadow-md">

        <!-- Header Controls -->
        <div class="px-4 py-4 border-b border-gray-200">
            <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
                
                <!-- Tambah Data Button -->
                <div class="flex justify-start">
                    <button @click="resetForm()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-2 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="hidden sm:inline">Tambah Company Access</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>

                <!-- Search and Controls -->
                <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4">
                    
                    <!-- Search Form -->
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        <label for="search" class="text-xs font-medium text-gray-700 whitespace-nowrap">Cari:</label>
                        <input type="text" name="search" id="search"
                            value="{{ request('search') }}"
                            placeholder="User ID, Nama User, Company..."
                            class="text-xs w-full sm:w-48 md:w-64 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                            onkeydown="if(event.key==='Enter') this.form.submit()" />
                        @if(request('search'))
                            <a href="{{ route('usermanagement.usercompany.index') }}" 
                               class="text-gray-500 hover:text-gray-700 px-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                        @if(request('perPage'))
                            <input type="hidden" name="perPage" value="{{ request('perPage') }}">
                        @endif
                    </form>

                    <!-- Per Page Form -->
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        <label for="perPage" class="text-xs font-medium text-gray-700 whitespace-nowrap">Per halaman:</label>
                        <select name="perPage" id="perPage" onchange="this.form.submit()"
                            class="text-xs w-20 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-2 py-2">
                            <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="px-4 py-4">
            <div class="overflow-x-auto rounded-md border border-gray-300">
                <table class="min-w-full bg-white text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-3 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                            <th class="py-3 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama User</th>
                            <th class="py-3 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th class="py-3 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Companies</th>
                            <th class="py-3 px-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($result as $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-3 px-3 text-sm font-medium text-gray-900">{{ $user->userid }}</td>
                            <td class="py-3 px-3 text-sm text-gray-700">
                                <div class="font-medium">{{ $user->name }}</div>
                            </td>
                            <td class="py-3 px-3 text-sm text-gray-700">
                                @if($user->jabatan)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $user->jabatan->namajabatan }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center text-sm text-gray-700">
                                <button @click="showCompaniesModal({ userid: '{{ $user->userid }}', name: '{{ addslashes($user->name) }}', companies: {{ json_encode($user->userCompanies->toArray()) }} })"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->userCompanies->count() > 0 ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} transition-colors cursor-pointer">
                                    <span>{{ $user->userCompanies->count() }} companies</span>
                                    <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Companies Button -->
                                    <button @click='editCompanies({
                                            userid: "{{ $user->userid }}",
                                            name: "{{ $user->name }}",
                                            userCompanies: @json($user->userCompanies->toArray())
                                        })'
                                        class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md p-2 transition-all duration-150"
                                        title="Edit Companies">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0h3m2 0h5M9 7h6m-6 4h6m-6 4h6"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Tidak ada data company access</p>
                                    <p class="text-sm">{{ request('search') ? 'Tidak ada hasil untuk pencarian "'.request('search').'"' : 'Belum ada user yang memiliki company access' }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($result->hasPages())
            <div class="mt-6">
                {{ $result->appends(request()->query())->links() }}
            </div>
            @else
            <div class="mt-4 flex items-center justify-between text-sm text-gray-700">
                <p>Menampilkan <span class="font-medium">{{ $result->count() }}</span> dari <span class="font-medium">{{ $result->total() }}</span> data</p>
            </div>
            @endif
        </div>

        <!-- Create Modal -->
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" x-cloak
            @keydown.window.escape="open = false">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Tambah Company Access</h3>
                    <button @click="open = false"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form action="{{ route('usermanagement.usercompany.store') }}" method="POST">
                        @csrf

                        <!-- User Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">User <span class="text-red-500">*</span></label>
                            <select name="userid" x-model="form.userid" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->userid }}">
                                    {{ $user->userid }} - {{ $user->name }} 
                                    @if($user->jabatan)
                                        ({{ $user->jabatan->namajabatan }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <div class="text-xs text-gray-500 mt-1">Hanya menampilkan user yang belum memiliki company access</div>
                        </div>

                        <!-- Company Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Companies <span class="text-red-500">*</span></label>
                            
                            <!-- Bulk Selection Controls -->
                            <div class="flex space-x-2 mb-3">
                                <button type="button" 
                                    @click="form.companycodes = availableCompanies.map(company => company.companycode)"
                                    class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 transition-colors">
                                    Select All
                                </button>
                                <button type="button" 
                                    @click="form.companycodes = []"
                                    class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 transition-colors">
                                    Deselect All
                                </button>
                            </div>

                            <!-- Company Checkboxes -->
                            <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3 space-y-2">
                                @foreach($companies as $company)
                                <label class="flex items-center space-x-3 p-2 border rounded hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           name="companycodes[]" 
                                           value="{{ $company->companycode }}"
                                           x-model="form.companycodes"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="flex-1">
                                        <div class="font-medium text-sm">{{ $company->companycode }}</div>
                                        <div class="text-xs text-gray-500">{{ $company->name }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Selected Count -->
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg" x-show="form.companycodes.length > 0">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-900">
                                    <span x-text="form.companycodes.length + ' companies dipilih'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
                            <button type="button" @click="open = false"
                                class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                Batal
                            </button>
                            <button type="submit"
                                :disabled="!form.userid || form.companycodes.length === 0"
                                :class="(!form.userid || form.companycodes.length === 0) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                                class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                Simpan Company Access
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Companies Modal (Simple Read-only) -->
        <div x-show="viewCompaniesModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" x-cloak
            @keydown.window.escape="viewCompaniesModal = false">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Company Access</h3>
                        <p class="text-sm text-gray-600" x-text="`User: ${selectedUserName} (${selectedUser})`"></p>
                    </div>
                    <button @click="viewCompaniesModal = false"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(80vh-140px)]">
                    <template x-if="viewCompanies.length > 0">
                        <div class="space-y-3">
                            <template x-for="company in viewCompanies" :key="company.companycode">
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0h3m2 0h5M9 7h6m-6 4h6m-6 4h6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h6 class="font-medium text-gray-900" x-text="company.companycode"></h6>
                                                <div class="text-xs text-gray-500 mt-1" x-text="'Granted by: ' + (company.grantedby || 'System')"></div>
                                                <div class="text-xs text-gray-500" x-text="'Created: ' + (company.createdat ? new Date(company.createdat).toLocaleDateString('id-ID') : '-')"></div>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="viewCompanies.length === 0">
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0h3m2 0h5M9 7h6m-6 4h6m-6 4h6"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">No Company Access</p>
                            <p class="text-sm text-gray-500">This user has no company access assigned</p>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-end">
                        <button @click="viewCompaniesModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Access Modal -->
        <div x-show="companyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" x-cloak
            @keydown.window.escape="companyModal = false">
            <div class="relative bg-white rounded-lg shadow-xl w-[80vw] h-[80vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex-shrink-0 flex items-center justify-between p-6 border-b border-gray-200 bg-white">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Kelola Company Access</h3>
                        <p class="text-sm text-gray-600" x-text="`User: ${selectedUserName} (${selectedUser})`"></p>
                        <p class="text-xs text-blue-600 mt-1">Total companies available: {{ $companies->count() }}</p>
                    </div>
                    <button @click="companyModal = false"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-hidden">
                    <!-- Loading State -->
                    <div x-show="isLoadingCompanies" class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-slate-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading companies...</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div x-show="!isLoadingCompanies" class="h-full overflow-y-auto p-6">
                        <form action="{{ route('usermanagement.usercompany.assign-companies') }}" method="POST" id="companyForm">
                            @csrf
                            <input type="hidden" name="userid" x-model="selectedUser">

                            <!-- Bulk Selection Controls -->
                            <div class="flex space-x-2 mb-4">
                                <button type="button" 
                                    @click="selectAllCompanies()"
                                    class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 transition-colors">
                                    Select All
                                </button>
                                <button type="button" 
                                    @click="deselectAllCompanies()"
                                    class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 transition-colors">
                                    Deselect All
                                </button>
                            </div>

                            <!-- Companies Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($companies as $company)
                                <label class="flex items-start space-x-3 p-3 border rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           name="companycodes[]" 
                                           value="{{ $company->companycode }}"
                                           x-model="selectedCompanies"
                                           :checked="isCompanySelected('{{ $company->companycode }}')"
                                           class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-sm text-gray-900">{{ $company->companycode }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $company->name }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            <!-- Selected Count -->
                            <div class="mt-6 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-900">
                                        <span x-text="selectedCompanies.length + ' companies dipilih'"></span>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex-shrink-0 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0 p-6 bg-gray-50 border-t">
                    <button type="button" @click="companyModal = false"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors duration-150">
                        Batal
                    </button>
                    <button type="submit" form="companyForm"
                        class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors duration-150">
                        Simpan Companies
                    </button>
                </div>
            </div>
        </div>

    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

</x-layout>