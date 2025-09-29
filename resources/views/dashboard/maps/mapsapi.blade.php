<x-layout>
    <x-slot:title>{{ $title }}</x-slot>
    <x-slot:navbar>{{ $navbar }}</x-slot:navbar>
    <x-slot:nav>{{ $nav }}</x-slot:nav>
    <style>
        /* Modern glassmorphism and animations */
        .glass-card {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        .toggle-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .toggle-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .toggle-btn:hover::before {
            left: 100%;
        }

        .toggle-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.6);
        }

        .map-type-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .map-type-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(66, 133, 244, 0.3) 0%, transparent 70%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translate(-50%, -50%);
        }

        .map-type-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .map-type-btn.active {
            background: linear-gradient(135deg, #4285F4, #1976D2);
            color: white;
            border-color: #4285F4;
            box-shadow: 0 4px 20px rgba(66, 133, 244, 0.4);
        }

        .filter-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .filter-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translate(-50%, -50%);
        }

        .filter-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            border-color: #10B981;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
        }

        .upload-zone {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .upload-zone::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(34, 197, 94, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .upload-zone:hover::before {
            opacity: 1;
        }

        .upload-zone:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .header-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .control-panel {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .stats-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .floating-card {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .pulse-dot {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        #map {
            height: 600px;
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #map:hover {
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            #map {
                height: 500px;
                border-radius: 15px;
            }
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .notification.show {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
        <!-- Notification System -->
        <div id="notification" class="notification glass-card text-white p-4 rounded-lg shadow-lg">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></div>
                <span id="notificationText">Filter applied successfully!</span>
            </div>
        </div>

        <!-- Modern Header with Floating Effect -->
        <div class="header-gradient text-white p-6 rounded-2xl shadow-2xl mb-8 mx-4 mt-4 floating-card">
            <div class="text-center relative z-10">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold">🌱 Agricultural GPS Monitoring System</h1>
                </div>
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></div>
                        <span class="text-lg font-semibold">Blok Tersedia:</span>
                    </div>
                    <div id="blocksList" class="text-xl font-bold tracking-wider bg-white/20 px-4 py-2 rounded-full">
                        <div class="flex items-center space-x-2">
                            <div class="loading-spinner w-4 h-4 border-2 border-white/30 border-t-white rounded-full"></div>
                            <span>Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 mx-4">
            <div class="stats-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Plot</p>
                        <p id="totalPlots" class="text-2xl font-bold text-gray-900">-</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="stats-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Blok</p>
                        <p id="totalBlocks" class="text-2xl font-bold text-green-600">-</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="stats-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Plot Bermasalah</p>
                        <p id="plotBermasalah" class="text-2xl font-bold text-orange-600">-</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="stats-card p-6 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Sedang Ditampilkan</p>
                        <p id="filteredCount" class="text-2xl font-bold text-indigo-600">All</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Control Panel -->
        <div class="control-panel mx-4 p-6 rounded-2xl shadow-xl mb-8">
            <div class="flex flex-col lg:flex-row gap-6 items-center">
                <!-- Upload Zone -->
                <div class="flex-1 w-full" hidden>
                    <div class="upload-zone border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center cursor-pointer bg-white/50 hover:bg-white/70 transition-all">
                        <form action="{{ route('dashboard.maps.upload') }}" id="frm-submit" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                            @csrf
                            <div class="flex flex-col items-center space-y-4">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-gray-700">Upload File GPX</p>
                                    <p class="text-sm text-gray-500">Seret & lepas atau klik untuk memilih</p>
                                </div>
                                <input id="gpxFile" type="file" name="gpxFile" accept=".gpx" class="hidden" />
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <!-- Age Filter Dropdown -->
                    <div class="relative">
                        <select id="ageFilter" class="bg-white/80 border-2 border-white/30 rounded-full px-4 py-2 pr-10 text-sm font-medium transition-all focus:border-green-400 focus:ring-2 focus:ring-green-200 focus:outline-none appearance-none cursor-pointer backdrop-blur-sm" onchange="applyFilters()">
                            <option value="all">Semua Umur</option>
                            <option value="6month">&lt; 6 Bulan</option>
                            <option value="6-12month">6-12 Bulan</option>
                            <option value="12month">&gt; 12 Bulan</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter Dropdown -->
                    <div class="relative">
                        <select id="statusFilter" class="bg-white/80 border-2 border-white/30 rounded-full px-4 py-2 pr-10 text-sm font-medium transition-all focus:border-blue-400 focus:ring-2 focus:ring-blue-200 focus:outline-none appearance-none cursor-pointer backdrop-blur-sm" onchange="applyFilters()">
                            <option value="all">Semua Status</option>
                            <option value="PC">PC - Tanam Selesai</option>
                            <option value="RC1">RC1 - Tanam Ulang 1</option>
                            <option value="RC2">RC2 - Tanam Ulang 2</option>
                            <option value="RC3">RC3 - Tanam Ulang 3</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Plot Condition Filter Dropdown -->
                    <div class="relative">
                        <select id="plotConditionFilter" class="bg-white/80 border-2 border-white/30 rounded-full px-4 py-2 pr-10 text-sm font-medium transition-all focus:border-orange-400 focus:ring-2 focus:ring-orange-200 focus:outline-none appearance-none cursor-pointer backdrop-blur-sm" onchange="applyFilters()">
                            <option value="all">Semua Kondisi Plot</option>
                            <option value="normal">Plot Normal</option>
                            <option value="bermasalah">Plot Bermasalah</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Plot Status Filter Dropdown -->
                    <div class="relative">
                        <select id="plotStatusFilter" class="bg-white/80 border-2 border-white/30 rounded-full px-4 py-2 pr-10 text-sm font-medium transition-all focus:border-purple-400 focus:ring-2 focus:ring-purple-200 focus:outline-none appearance-none cursor-pointer backdrop-blur-sm" onchange="applyFilters()">
                            <option value="all">Semua Status Plot</option>
                            <option value="ktg">KTG - Kebun Giling</option>
                            <option value="kbd">KBD - Kebun Replanting</option>
                            <option value="rpl">RPL - Replanting</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Map Type Controls -->
                    <div class="flex gap-2">
                        <button class="map-type-btn bg-white/80 border-2 border-white/30 px-4 py-2 rounded-full cursor-pointer text-sm font-medium transition-all active" onclick="setMapType('roadmap')">
                            Peta Jalan
                        </button>
                        <button class="map-type-btn bg-white/80 border-2 border-white/30 px-4 py-2 rounded-full cursor-pointer text-sm font-medium transition-all" onclick="setMapType('satellite')">
                            Satelit
                        </button>
                        <button class="map-type-btn bg-white/80 border-2 border-white/30 px-4 py-2 rounded-full cursor-pointer text-sm font-medium transition-all" onclick="setMapType('hybrid')">
                            Hybrid
                        </button>
                    </div>

                    <!-- Toggle Button -->
                    <button class="toggle-btn text-white border-0 px-6 py-3 rounded-full cursor-pointer text-sm font-medium transition-all" onclick="togglePolyline()">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span id="toggleText">Sembunyikan Polygon</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Modern Map Container -->
        <div class="mx-4 mb-8">
            <div id="map" class="relative overflow-hidden">
                <div class="flex flex-col justify-center items-center h-full bg-gradient-to-br from-gray-100 to-gray-200 text-gray-600 text-xl text-center">
                    <div class="mb-6">
                        <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold text-gray-700 mb-2">🗺️ Google Maps Integration Required</div>
                    </div>
                    <div class="max-w-lg glass-card p-6 rounded-2xl text-sm text-gray-600">
                        <p class="font-semibold mb-4 text-gray-800">Setup Required:</p>
                        <ul class="text-left space-y-2">
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-500">•</span>
                                <span>Get API key from Google Cloud Console</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-500">•</span>
                                <span>Enable Google Maps JavaScript API</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <span class="text-blue-500">•</span>
                                <span>Deploy to server or hosting</span>
                            </li>
                        </ul>
                        <p class="mt-4 text-indigo-600 font-medium">Alternative: Use Leaflet for immediate functionality</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php //dd($list->first()); @endphp
    <script>
        // Enhanced notifications
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');
            
            notificationText.textContent = message;
            notification.className = `notification glass-card p-4 rounded-lg shadow-lg ${type === 'success' ? 'text-green-100' : 'text-red-100'}`;
            
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Enhanced file handling
        document.addEventListener("DOMContentLoaded", () => {
            const fileInput = document.getElementById("gpxFile");
            const dropArea = document.querySelector(".upload-zone");

            const handleFile = (file) => {
                if (file) {
                    showNotification(`File selected: ${file.name}`, 'success');
                    dropArea.classList.add('bg-green-50', 'border-green-300');
                    setTimeout(() => {
                        dropArea.classList.remove('bg-green-50', 'border-green-300');
                    }, 2000);
                }
            };

            fileInput.addEventListener("change", (e) => handleFile(e.target.files[0]));

            dropArea.addEventListener("dragover", (e) => {
                e.preventDefault();
                dropArea.classList.add("border-blue-400", "bg-blue-50");
            });

            dropArea.addEventListener("dragleave", () => {
                dropArea.classList.remove("border-blue-400", "bg-blue-50");
            });

            dropArea.addEventListener("drop", (e) => {
                e.preventDefault();
                dropArea.classList.remove("border-blue-400", "bg-blue-50");

                const file = e.dataTransfer.files[0];
                if (file) {
                    fileInput.files = e.dataTransfer.files;
                    handleFile(file);
                }
            });

            dropArea.addEventListener("click", () => {
                fileInput.click();
            });

            updateStats();

                        // Initialize location permission on page load
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        showNotification('📍 Location access granted!');
                    },
                    (error) => {
                        showNotification('❌ Location access denied', 'error');
                    }
                );
            }
        });

        $('#gpxFile').change(function(){
          $('#frm-submit').submit();
        })

        function validateForm() {
            const file = document.getElementById("gpxFile").files[0];
            if (!file) {
                showNotification("Please select a file first", 'error');
                return false;
            }

            const fileExtension = file.name.split(".").pop().toLowerCase();
            if (file.type !== "application/gpx+xml" && fileExtension !== "gpx") {
                showNotification("Please upload a valid GPX file", 'error');
                return false;
            }

            if (file.size > 20 * 1024 * 1024) {
                showNotification("File size exceeds 20MB limit", 'error');
                return false;
            }

            showNotification("Uploading file...", 'success');
            return true;
        }

        const headerData = [
          @foreach( $header as $item )
            { 
                code: '{{ $item->plot }}', 
                lat: {{ $item->centerlatitude }}, 
                lng: {{ $item->centerlongitude }}
            },
          @endforeach
        ];

        const listData = [
          @foreach($list as $item)
            { 
                code: '{{ $item->plot }}', 
                lat: {{ $item->latitude }}, 
                lng: {{ $item->longitude }},
                batchno: '{{ $item->batchno ?? '' }}',
                batchdate: '{{ $item->batchdate ?? '' }}',
                tanggalulangtahun: '{{ $item->tanggalulangtahun ?? '' }}',
                age: {{ $item->tanggalulangtahun ? (date('Y') - date('Y', strtotime($item->tanggalulangtahun))) * 12 + (date('m') - date('m', strtotime($item->tanggalulangtahun))) : 0 }},
                kodestatus: '{{ $item->kodestatus ?? '' }}',  
                status: '{{ $item->status ?? '' }}',
                batcharea: {{ $item->batcharea ?? 0 }},
                luasarea: {{ $item->luasarea ?? 0 }}
            },
          @endforeach
        ];

        let map;
        let markers = [];
        let polygons = [];
        let polylineVisible = true;
        let originalHeaderData = headerData;
        let originalListData = listData;
        let currentFilterType = 'all';
        let userLocationMarker = null;
        let locationWatchId = null;

        // New combined filter function
        function applyFilters() {
            const ageFilter = document.getElementById('ageFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const plotStatusFilter = document.getElementById('plotStatusFilter').value;
            const plotConditionFilter = document.getElementById('plotConditionFilter').value;
            
            // Debug: Log current filter values and sample data
            console.log('Filters:', { ageFilter, statusFilter, plotStatusFilter, plotConditionFilter });
            console.log('Sample data:', originalListData.slice(0, 3));
            console.log('Unique plot statuses:', [...new Set(originalListData.map(item => item.status))]);
            
            // Clear existing
            markers.forEach(marker => marker.setMap(null));
            polygons.forEach(polygon => polygon.setMap(null));
            markers = [];
            polygons = [];
            
            // Start with all data
            let filteredListData = originalListData;
            
            // Apply age filter
            if (ageFilter !== 'all') {
                filteredListData = filteredListData.filter(item => {
                    switch (ageFilter) {
                        case '6month': return item.age < 6;
                        case '6-12month': return item.age >= 6 && item.age <= 12;
                        case '12month': return item.age > 12;
                        default: return true;
                    }
                });
            }
            
            // Apply status filter
            if (statusFilter !== 'all') {
                filteredListData = filteredListData.filter(item => {
                    return item.kodestatus === statusFilter;
                });
            }

            // Apply plot status filter
            if (plotStatusFilter !== 'all') {
                filteredListData = filteredListData.filter(item => {
                    return item.status && item.status.toLowerCase() === plotStatusFilter.toLowerCase();
                });
            }

            // Apply plot condition filter
            if (plotConditionFilter !== 'all') {
                filteredListData = filteredListData.filter(item => {
                    const batchArea = parseFloat(item.batcharea) || 0;
                    const luasArea = parseFloat(item.luasarea) || 0;
                    
                    if (plotConditionFilter === 'normal') {
                        return batchArea === luasArea;
                    } else if (plotConditionFilter === 'bermasalah') {
                        return batchArea < luasArea;
                    }
                    return true;
                });
            }
            
            console.log('Filtered data count:', filteredListData.length);
            
            const filteredCodes = [...new Set(filteredListData.map(item => item.code))];
            const filteredHeaderData = originalHeaderData.filter(item => filteredCodes.includes(item.code));
            
            // Update filtered count
            document.getElementById('filteredCount').textContent = filteredHeaderData.length;
            
            
            // Show notification
            const ageNames = {
                'all': 'semua umur',
                '6month': '< 6 bulan',
                '6-12month': '6-12 bulan',
                '12month': '> 12 bulan'
            };
            const statusNames = {
                'all': 'semua status',
                'PC': 'status PC',
                'RC1': 'status RC1',
                'RC2': 'status RC2',
                'RC3': 'status RC3'
            };
            const plotStatusNames = {
                'all': 'semua status plot',
                'ktg': 'status KTG',
                'kbd': 'status KBD',
                'rpl': 'status RPL'
            };
            const plotConditionNames = {
                'all': 'semua kondisi',
                'normal': 'plot normal',
                'bermasalah': 'plot bermasalah'
            };
            
            showNotification(`Menampilkan ${ageNames[ageFilter]}, ${statusNames[statusFilter]}, ${plotStatusNames[plotStatusFilter]}, ${plotConditionNames[plotConditionFilter]} (${filteredHeaderData.length} plot)`);
            
            createMapContent(filteredHeaderData, filteredListData);
            updateBlocksListFiltered(filteredHeaderData);
        }

        // Enhanced stats calculation
        function updateStats() {
            const totalPlots = headerData.length;
            const totalBlocks = [...new Set(headerData.map(item => item.code.charAt(0)))].length;
            
            // Calculate plot bermasalah (batcharea < luasarea)
            const plotBermasalahCodes = listData.filter(item => {
                const batchArea = parseFloat(item.batcharea) || 0;
                const luasArea = parseFloat(item.luasarea) || 0;
                return batchArea < luasArea;
            }).map(item => item.code);
            const uniquePlotBermasalah = [...new Set(plotBermasalahCodes)].length;

            document.getElementById('totalPlots').textContent = totalPlots;
            document.getElementById('totalBlocks').textContent = totalBlocks;
            document.getElementById('plotBermasalah').textContent = uniquePlotBermasalah;
        }
        

        // Enhanced filter function (backward compatibility)
        function filterByMonth(filterType) {
            document.getElementById('ageFilter').value = filterType;
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('plotStatusFilter').value = 'all';
            document.getElementById('plotConditionFilter').value = 'all';
            applyFilters();
        }

        function createMapContent(headerDataToUse, listDataToUse) {
            const blockColorMap = createBlockColorMap();

            headerDataToUse.forEach((point, index) => {
                const blockLetter = point.code.charAt(0);
                const blockColor = blockColorMap[blockLetter] || '#EA4335';
                const plotDetails = originalListData.find(item => item.code === point.code) || {};
                const kodeStatus = plotDetails.kodestatus || 'N/A';
                const marker = new google.maps.Marker({
                    position: { lat: point.lat, lng: point.lng },
                    map: map,
                    title: `Block ${blockLetter} - ${point.code}`,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 12,
                        fillColor: blockColor,
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 3
                    },
                    label: {
                        text: kodeStatus,
                        color: 'white',
                        fontSize: '10px',
                        fontWeight: 'bold'
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
    content: `
        <div style="font-family: Arial, sans-serif; max-width: 300px; padding: 4px;">
            <div style="font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center;">
                <span style="margin-right: 8px;">🌱</span>
                Block ${blockLetter} - ${point.code}
            </div>
            <div style="font-size: 14px; line-height: 1.6; color: #374151;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                    <div><strong>Block:</strong> ${blockLetter}</div>
                    <div><strong>Plot:</strong> ${point.code}</div>
                </div>
                <div style="background: #f3f4f6; padding: 8px; border-radius: 8px; margin-bottom: 8px;">
                    <div hidden><strong>Batch No:</strong> ${plotDetails.batchno || 'N/A'}</div>
                    <div hidden><strong>Batch Date:</strong> ${plotDetails.batchdate || 'N/A'}</div>
                    <div><strong>Age:</strong> <span style="color: #059669; font-weight: 600;">${plotDetails.age || 'N/A'} months</span></div>
                </div>
                <div><strong>Plot Status:</strong> <span style="color: ${getPlotStatusColor(plotDetails.status)}; font-weight: 600;">${plotDetails.status || 'N/A'}</span></div>
                <div><strong>Kode Status:</strong> <span style="color: #059669; font-weight: 600;">${plotDetails.kodestatus || 'N/A'}</span></div>
                <div style="margin-top: 8px; font-size: 12px; color: #6b7280;">
                    <div>Lat: ${point.lat.toFixed(6)}</div>
                    <div>Lng: ${point.lng.toFixed(6)}</div>
                </div>
                <div style="margin-top: 12px; text-align: center;">
                    <button onclick="openInGoogleMaps(${point.lat}, ${point.lng}, '${point.code}')" 
                            style="background: linear-gradient(135deg, #4285F4, #1976D2); 
                                   color: white; 
                                   border: none; 
                                   padding: 8px 16px; 
                                   border-radius: 20px; 
                                   font-size: 12px; 
                                   font-weight: 600; 
                                   cursor: pointer; 
                                   box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3);
                                   transition: all 0.2s ease;">
                        📍 Open in Google Maps
                    </button>
                </div>
            </div>
        </div>
    `
});

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });

            headerDataToUse.forEach((point, index) => {
                const blockLetter = point.code.charAt(0);
                const blockColor = blockColorMap[blockLetter] || '#FF0000';
                const filtered = listDataToUse.filter(item => item.code === point.code);

                if (filtered.length < 3) return;

                const polygonCoordinates = filtered.map(item => ({
                    lat: parseFloat(item.lat),
                    lng: parseFloat(item.lng)
                }));

                const polygon = new google.maps.Polygon({
                    paths: polygonCoordinates,
                    strokeColor: blockColor,
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: blockColor,
                    fillOpacity: 0.35,
                    map: map
                });

                const plotDetails = originalListData.find(item => item.code === point.code) || {};
                
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="max-width: 350px; padding: 4px;">
                            <div style="font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 12px; display: flex; align-items: center;">
                                <span style="margin-right: 8px;">🌿</span>
                                Block ${blockLetter} - Plot ${point.code}
                            </div>
                            <div style="font-size: 14px; line-height: 1.6; color: #374151;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                                    <div><strong>Block:</strong> ${blockLetter}</div>
                                    <div><strong>Plot:</strong> ${point.code}</div>
                                    <div><strong>Points:</strong> ${filtered.length}</div>
                                    <div><strong>Age:</strong> <span style="color: #059669; font-weight: 600;">${plotDetails.age || 'N/A'} months</span></div>
                                </div>
                                <div style="background: #f3f4f6; padding: 8px; border-radius: 8px; margin-bottom: 8px;">
                                    <div><strong>Plot Status:</strong> <span style="color: ${getPlotStatusColor(plotDetails.status)}; font-weight: 600;">${plotDetails.status || 'N/A'}</span></div>
                                    <div><strong>Kode Status:</strong> <span style="color: #059669; font-weight: 600;">${plotDetails.kodestatus || 'N/A'}</span></div>
                                    <div><strong>Batch No:</strong> ${plotDetails.batchno || 'N/A'}</div>
                                    <div><strong>Batch Date:</strong> ${plotDetails.batchdate || 'N/A'}</div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px;">
                                    <div><strong>Area:</strong> ${plotDetails.luasarea || 'N/A'} Ha</div>
                                    <div><strong>Varietas:</strong> ${plotDetails.kodevarietas || 'N/A'}</div>
                                    <div><strong>Plant Distance:</strong> ${plotDetails.plot_jaraktanam || 'N/A'} m</div>
                                    <div><strong>Active:</strong> ${plotDetails.isactive || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    `
                });

                polygon.addListener('click', (e) => {
                    infoWindow.setPosition(e.latLng);
                    infoWindow.open(map);
                });

                polygons.push(polygon);
            });
        }

        function getPlotStatusColor(status) {
            switch (status) {
                case 'ktg': return '#f59e0b'; // amber
                case 'kbd': return '#059669'; // green
                case 'rpl': return '#dc2626'; // red
                default: return '#6b7280'; // gray
            }
        }

        function updateBlocksListFiltered(data) {
            const blocks = [...new Set(data.map(item => item.code.charAt(0)))].sort();
            const blocksListElement = document.getElementById('blocksList');
            
            if (blocksListElement) {
                const blockColorMap = createBlockColorMap();
                const blockElements = blocks.map(block => {
                    const blockColor = blockColorMap[block] || '#10B981';
                    const count = data.filter(item => item.code.charAt(0) === block).length;
                    return `<span style="color: ${blockColor}; font-weight: bold; margin: 0 4px; background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 12px; display: inline-block;">${block}(${count})</span>`;
                }).join(' ');
                
                blocksListElement.innerHTML = blockElements || '<span style="color: #ef4444;">No blocks found</span>';
            }
        }

        function getUniqueBlocks() {
            const blocks = [...new Set(listData.map(item => item.code.charAt(0)))];
            return blocks.sort();
        }

        function createBlockColorMap() {
            const blocks = getUniqueBlocks();
            const colorMap = {};
            
            blocks.forEach((block, index) => {
                const hue = (index * 360 / blocks.length) % 360;
                const saturation = 70 + (index % 3) * 10;
                const lightness = 45 + (index % 2) * 10;
                
                const color = hslToHex(hue, saturation, lightness);
                colorMap[block] = color;
            });
            
            return colorMap;
        }

        function hslToHex(h, s, l) {
            l /= 100;
            const a = s * Math.min(l, 1 - l) / 100;
            const f = n => {
                const k = (n + h / 30) % 12;
                const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
                return Math.round(255 * color).toString(16).padStart(2, '0');
            };
            return `#${f(0)}${f(8)}${f(4)}`;
        }

        function filterByCodes(codes) {
            return originalListData.filter(item => item.code === codes);
        }

        function updateBlocksList() {
            const blocks = getUniqueBlocks();
            const blocksListElement = document.getElementById('blocksList');
            
            if (blocksListElement) {
                const blockColorMap = createBlockColorMap();
                const blockElements = blocks.map(block => {
                    const blockColor = blockColorMap[block] || '#10B981';
                    return `<span style="color: ${blockColor}; font-weight: bold; margin: 0 4px; background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 12px; display: inline-block;">${block}</span>`;
                }).join(' ');
                
                blocksListElement.innerHTML = blockElements;
            }
        }

        function initMap() {
            try {
                const centerLat = (headerData[0].lat + listData[0].lat) / 2;
                const centerLng = (headerData[0].lng + listData[0].lng) / 2;

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 8,
                    center: { lat: centerLat, lng: centerLng },
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapId: "DEMO_MAP_ID",
                    styles: [
                        {
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{ visibility: "on" }]
                        }
                    ]
                });

                createMapContent(headerData, listData);
                updateBlocksList();

                const bounds = new google.maps.LatLngBounds();
                [...headerData, ...listData].forEach(point => {
                    bounds.extend(new google.maps.LatLng(point.lat, point.lng));
                });
                map.fitBounds(bounds);
                
                map.fitBounds(bounds, { maxZoom: 6 });

                showNotification('Peta berhasil dimuat!');

                // Initialize filters
                document.getElementById('filteredCount').textContent = headerData.length;
                initializeWiFiLocationTracking();
                
            } catch (error) {
                console.error('Error initializing Google Maps:', error);
                showNotification('Error memuat Google Maps', 'error');
            }
        }

        function togglePolyline() {
            const toggleText = document.getElementById('toggleText');
            const button = event.target.closest('button');

            if (polylineVisible) {
                polygons.forEach(polygon => polygon.setMap(null));
                toggleText.textContent = 'Tampilkan Polygon';
                polylineVisible = false;
                showNotification('Polygon disembunyikan');
            } else {
                polygons.forEach(polygon => polygon.setMap(map));
                toggleText.textContent = 'Sembunyikan Polygon';
                polylineVisible = true;
                showNotification('Polygon ditampilkan');
            }
        }

        function setMapType(type) {
            if (!map) return;

            document.querySelectorAll('.map-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            switch(type) {
                case 'roadmap':
                    map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                    break;
                case 'satellite':
                    map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
                    break;
                case 'hybrid':
                    map.setMapTypeId(google.maps.MapTypeId.HYBRID);
                    break;
            }

            showNotification(`Jenis peta diubah ke ${type}`);
        }

        

        function initializeWiFiLocationTracking() {
            if (!navigator.geolocation) {
                showNotification('❌ Geolocation not supported by this browser', 'error');
                return;
            }

            showNotification('📶 Detecting location via Wi-Fi networks...');
            
            // Configure for Wi-Fi location (not IP fallback)
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Check if accuracy is reasonable (Wi-Fi typically gives 10-200m)
                    const accuracy = position.coords.accuracy;
                    
                    if (accuracy <= 1000) { // If accuracy is better than 1km (likely Wi-Fi)
                        showNotification(`📶 Wi-Fi location found! Accuracy: ${accuracy.toFixed(0)}m`);
                        updateUserLocation(position);
                        startWiFiTracking();
                    } else {
                        // Too inaccurate, probably IP-based
                        showNotification('❌ Wi-Fi location too inaccurate. Please move to area with more Wi-Fi networks.', 'error');
                    }
                },
                (error) => {
                    let errorMsg = '';
                    switch(error.code) {
                        case 1: 
                            errorMsg = 'Location access denied. Please allow location access for Wi-Fi positioning.';
                            break;
                        case 2: 
                            errorMsg = 'Wi-Fi location unavailable. Make sure Wi-Fi is enabled and you\'re in an area with Wi-Fi networks.';
                            break;
                        case 3: 
                            errorMsg = 'Wi-Fi location timeout. Please try again.';
                            break;
                    }
                    showNotification(`❌ ${errorMsg}`, 'error');
                },
                {
                    enableHighAccuracy: false,  // Don't use GPS, prefer Wi-Fi
                    timeout: 20000,             // Give more time for Wi-Fi scan
                    maximumAge: 60000           // Cache Wi-Fi location for 1 minute
                }
            );
        }

        function startWiFiTracking() {
            locationWatchId = navigator.geolocation.watchPosition(
                (position) => {
                    // Only update if accuracy is good (Wi-Fi level)
                    if (position.coords.accuracy <= 1000) {
                        updateUserLocation(position);
                        showNotification(`📶 Wi-Fi location updated: ${position.coords.latitude.toFixed(4)}, ${position.coords.longitude.toFixed(4)} (±${position.coords.accuracy.toFixed(0)}m)`);
                    }
                },
                (error) => {
                    console.log('Wi-Fi tracking error:', error);
                },
                {
                    enableHighAccuracy: false,
                    timeout: 30000,
                    maximumAge: 120000  // Use cached Wi-Fi location for 2 minutes
                }
            );
        }



        function updateUserLocation(position) {
            console.log('updateUserLocation called with:', position);
            
            if (!map) {
                console.log('Map not available');
                return;
            }

            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            console.log('Creating location marker at:', lat, lng);

            // Remove old marker if exists
            if (userLocationMarker) {
                try {
                    if (userLocationMarker.map) {
                        userLocationMarker.map = null;
                    } else if (userLocationMarker.setMap) {
                        userLocationMarker.setMap(null);
                    }
                } catch (e) {
                    console.log('Error removing old marker:', e);
                }
            }

            // Try to use AdvancedMarkerElement first, fallback to regular Marker
            try {
                // Create modern AdvancedMarkerElement for 2025
                const markerElement = document.createElement('div');
                markerElement.innerHTML = `
                    <div style="
                        width: 20px; 
                        height: 20px; 
                        background-color: #22c55e; 
                        border: 4px solid white; 
                        border-radius: 50%; 
                        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
                        position: relative;
                    "></div>
                `;

                userLocationMarker = new google.maps.marker.AdvancedMarkerElement({
                    map: map,
                    position: { lat: lat, lng: lng },
                    content: markerElement,
                    title: 'Your Location'
                });

                console.log('AdvancedMarkerElement created successfully');
            } catch (e) {
                console.log('AdvancedMarkerElement failed, using regular Marker:', e);
                
                // Fallback to regular Marker
                userLocationMarker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    title: 'Your Location',
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 15,
                        fillColor: '#22c55e',
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 4
                    },
                    zIndex: 1000
                });
            }

            // Create info window
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding: 4px;">
                        <div style="font-size: 16px; font-weight: 700; color: #1f2937; margin-bottom: 8px;">
                            📍 Your Location
                        </div>
                        <div style="font-size: 14px; color: #374151;">
                            <div>Lat: ${lat.toFixed(6)}</div>
                            <div>Lng: ${lng.toFixed(6)}</div>
                            <div>Accuracy: ${position.coords.accuracy.toFixed(0)}m</div>
                        </div>
                    </div>
                `
            });

            // Add click listener
            userLocationMarker.addListener('click', () => {
                infoWindow.open(map, userLocationMarker);
            });

            console.log('Location marker created successfully');
            showNotification(`📍 Location: ${lat.toFixed(4)}, ${lng.toFixed(4)}`);
        }




        // Function to open Google Maps with coordinates
        function openInGoogleMaps(lat, lng, plotCode) {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            // Create the label for the location
            const label = encodeURIComponent(`Agricultural Plot ${plotCode}`);
            
            if (isMobile) {
                // For mobile devices, use the Google Maps app URL scheme
                const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}&query_place_id=${label}`;
                
                // Try to open Google Maps app, fallback to browser
                const appUrl = `comgooglemaps://?q=${lat},${lng}&label=${label}`;
                
                // Create a temporary link to test if the app opens
                const tempLink = document.createElement('a');
                tempLink.href = appUrl;
                tempLink.style.display = 'none';
                document.body.appendChild(tempLink);
                
                // Try to open the app
                tempLink.click();
                
                // Fallback to browser after a short delay
                setTimeout(() => {
                    window.open(mapsUrl, '_blank');
                    document.body.removeChild(tempLink);
                }, 1000);
                
                showNotification(`📍 Opening ${plotCode} in Google Maps...`);
            } else {
                // For desktop, open in browser
                const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}&query_place_id=${label}`;
                window.open(mapsUrl, '_blank');
                showNotification(`📍 Opening ${plotCode} in Google Maps...`);
            }
        }

        




    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCc2vFD26wD5ox_5EwLJhR6U1jcfKibxBQ&libraries=marker&callback=initMap"></script>
</x-layout>