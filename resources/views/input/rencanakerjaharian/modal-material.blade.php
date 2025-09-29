{{-- resources\views\input\rencanakerjaharian\modal-material.blade.php --}}
<div
  x-show="open"
  x-cloak
  class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 p-4"
  style="display: none;"
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-200"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
>
  <div
    @click.away="open = false"
    class="bg-white rounded-lg shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col"
  >
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">Pilih Grup Herbisida</h2>
        <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 rounded-full p-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <p class="text-sm text-gray-600 mt-1" x-text="`Aktivitas: ${currentActivityCode}`"></p>
    </div>

    {{-- Content berdasarkan grup --}}
    <div class="flex-1 overflow-y-auto p-6">
      <template x-for="group in availableGroups" :key="group.herbisidagroupid">
        <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
          {{-- Group Header dengan Radio Button - Clickable untuk expand/collapse --}}
          <div class="bg-gray-50 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center flex-1">
                <input 
                  type="radio" 
                  :value="group.herbisidagroupid"
                  :name="`group_${rowIndex}`"
                  @change="selectGroup(group)"
                  :checked="selectedGroup && selectedGroup.herbisidagroupid === group.herbisidagroupid"
                  class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                >
                <label class="ml-3 font-semibold text-gray-800 cursor-pointer flex-1" 
                       :for="`group_${rowIndex}_${group.herbisidagroupid}`"
                       x-text="group.herbisidagroupname">
                </label>
              </div>
              
              {{-- Toggle button untuk show/hide detail --}}
              <button 
                type="button"
                @click="group.showDetails = !group.showDetails"
                class="ml-4 p-2 text-gray-500 hover:text-gray-700 transition-colors"
              >
                <svg 
                  class="w-5 h-5 transform transition-transform duration-200"
                  :class="group.showDetails ? 'rotate-180' : ''"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
            </div>
          </div>
          
          {{-- Items Detail - Collapsible --}}
          <div x-show="group.showDetails" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
            class="border-t border-gray-100">
            <div class="p-4">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Material yang tersedia:</h4>
              
              {{-- Group Description - Once per group --}}
              <div class="bg-blue-50 rounded-lg p-3 mb-4 border border-blue-200">
                <div class="text-sm text-blue-800">
                  <strong>Keterangan Grup:</strong>
                  <span x-text="group.items[0]?.description || 'Tidak ada keterangan'"></span>
                </div>
              </div>
              
              <div class="space-y-3">
                <template x-for="item in group.items" :key="item.itemcode">
                  <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <div class="font-medium text-gray-900 mb-1" x-text="item.itemname"></div>
                        <div class="text-sm text-gray-600 grid grid-cols-2 gap-4">
                          <div>Kode: <span class="font-mono" x-text="item.itemcode"></span></div>
                          <div>Dosis: <span x-text="`${item.dosageperha} ${item.measure}/ha`"></span></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
      <span class="text-sm text-gray-600" x-text="selectedGroup ? `${selectedGroup.herbisidagroupname} dipilih` : 'Belum ada grup dipilih'"></span>
      <div class="flex space-x-3">
        <button @click="clearSelection()" type="button" class="px-4 py-2 text-sm text-red-600 hover:text-red-800">
          Clear
        </button>
        <button @click="confirmSelection()" type="button" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
          Selesai
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Re-define materialPicker dengan confirmSelection dan helper-nya
function materialPicker(rowIndex) {
  return {
    open: false,
    rowIndex: rowIndex,
    currentActivityCode: '',
    selectedGroup: null,

    get hasMaterial() {
      return this.currentActivityCode && this.availableGroups.length > 0;
    },

    get availableGroups() {
      if (!this.currentActivityCode || !window.herbisidaData) return [];
      const groups = {};
      window.herbisidaData.forEach(item => {
        if (item.activitycode === this.currentActivityCode) {
          if (!groups[item.herbisidagroupid]) {
            groups[item.herbisidagroupid] = {
              herbisidagroupid: item.herbisidagroupid,
              herbisidagroupname: item.herbisidagroupname,
              showDetails: false,
              items: []
            };
          }
          groups[item.herbisidagroupid].items.push(item);
        }
      });
      return Object.values(groups);
    },

    // Cari group by ID (untuk inisialisasi kalau perlu)
    getGroupById(groupId, activityCode) {
      if (!groupId || !activityCode) return null;
      const item = window.herbisidaData.find(i =>
        i.herbisidagroupid == groupId && i.activitycode === activityCode
      );
      return item
        ? { herbisidagroupid: item.herbisidagroupid, herbisidagroupname: item.herbisidagroupname }
        : null;
    },

    setSelectedGroup(groupId, activityCode) {
      const g = this.getGroupById(groupId, activityCode);
      if (g) this.selectedGroup = g;
    },

    checkMaterial() {
      if (this.hasMaterial) this.open = true;
    },

    selectGroup(group) {
      this.selectedGroup = {
        herbisidagroupid: group.herbisidagroupid,
        herbisidagroupname: group.herbisidagroupname
      };
    },

    clearSelection() {
      this.selectedGroup = null;
      this.updateHiddenInputs();
    },

    // **Tambah ini** agar tombol “Selesai” berfungsi
    confirmSelection() {
      this.updateHiddenInputs();
      this.open = false;
    },

    // Update value hidden inputs
    updateHiddenInputs() {
      this.ensureHiddenInputsExist();
      document.querySelector(`input[name="rows[${this.rowIndex}][material_group_id]"]`).value =
        this.selectedGroup ? this.selectedGroup.herbisidagroupid : '';
      document.querySelector(`input[name="rows[${this.rowIndex}][material_group_name]"]`).value =
        this.selectedGroup ? this.selectedGroup.herbisidagroupname : '';
      document.querySelector(`input[name="rows[${this.rowIndex}][usingmaterial]"]`).value =
        this.selectedGroup ? '1' : '0';
    },

    // Pastikan hidden inputs ada di DOM
    ensureHiddenInputsExist() {
      const cell = document.querySelector(
        `tr:nth-child(${this.rowIndex + 1}) td:nth-child(10)`
      );
      if (!cell) return;
      ['material_group_id','material_group_name','usingmaterial'].forEach(name => {
        if (!cell.querySelector(`input[name="rows[${this.rowIndex}][${name}]"]`)) {
          const inp = document.createElement('input');
          inp.type = 'hidden';
          inp.name = `rows[${this.rowIndex}][${name}]`;
          inp.value = name === 'usingmaterial' ? '0' : '';
          cell.appendChild(inp);
        }
      });
    },

    init() {
      // sama seperti sebelumnya: observasi activity input dan reset selection
      const activityInput = document.querySelector(
        `input[name="rows[${this.rowIndex}][nama]"]`
      );
      if (activityInput) {
        const observer = new MutationObserver(() => {
          this.currentActivityCode = activityInput.value || '';
          this.selectedGroup = null;
          this.updateHiddenInputs();
        });
        observer.observe(activityInput, { attributes: true, attributeFilter: ['value'] });
        activityInput.addEventListener('input', () => {
          this.currentActivityCode = activityInput.value || '';
          this.selectedGroup = null;
          this.updateHiddenInputs();
        });
        this.currentActivityCode = activityInput.value || '';
        this.updateHiddenInputs();
      }
    }
  }
}
</script>
@endpush
