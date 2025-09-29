<x-layout>
    <x-slot:title>{{ $title }}</x-slot>
    <x-slot:navbar>{{ $navbar }}</x-slot:navbar>
    <x-slot:nav>{{ $nav }}</x-slot:nav>
    <x-slot:navnav>{{ $title }}</x-slot:navnav>
    @include('errorfile')
    @if (session('error'))
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @php
        $isEdit = isset($header);
    @endphp


    <form action="{{ $url }}" method="POST">
        @csrf
        @method($method)
        <div class="mx-4 p-6 bg-white rounded-md shadow-md">
            <div class="text-center text-xl pb-2 mb-6 -mt-2 border-b font-medium border-gray-300">Header</div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div>
                    <label class="block text-md">Nomor Sample</label>
                    <input type="text" name="nosample" class="border rounded-md border-gray-300 p-2 w-full"
                        maxlength="4" autocomplete="off" value="{{ old('nosample', $header->nosample ?? '') }}"
                        required>
                </div>

                <div>
                    <label class="block text-md">Kode Plot Sample</label>
                    <select id="idblokplot" name="idblokplot" class="border rounded-md border-gray-300 p-2 w-full"
                        required>
                        <option value="" disabled selected class="text-gray">--Pilih Plot Sample--</option>
                        @foreach ($mapping as $map)
                            <option value="{{ $map->idblokplot }}"
                                {{ old('idblokplot', $header->idblokplot ?? '') == $map->idblokplot ? 'selected' : '' }}
                                class="text-black">
                                {{ $map->idblokplot }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-md">Kode Company</label>
                    <input id="companycode" type="text" name="companycode" maxlength="4"
                        class="border rounded-md border-gray-300 p-2 w-full" autocomplete="off"
                        value="{{ old('companycode', $header->companycode ?? '') }}" required>
                </div>
                <div>
                    <label class="block text-md">Kode Blok</label>
                    <input id="blok" type="text" name="blok" maxlength="2"
                        class="border rounded-md border-gray-300 p-2 w-full" autocomplete="off"
                        value="{{ old('blok', $header->blok ?? '') }}" required>
                </div>
                <div>
                    <label class="block text-md">Kode Plot</label>
                    <input id="plot" type="text" name="plot" maxlength="5"
                        class="border rounded-md border-gray-300 p-2 w-full" autocomplete="off"
                        value="{{ old('plot', $header->plot ?? '') }}" required>
                </div>

                <div>
                    <label class="block text-md">Varietas</label>
                    <input type="text" name="varietas" class="border rounded-md border-gray-300 p-2 w-full"
                        maxlength="10" autocomplete="off" value="{{ old('varietas', $header->varietas ?? '') }}"
                        required>
                </div>

                <div>
                    <label class="block text-md">Tanggal Tanam</label>
                    <input type="date" name="tanggaltanam" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}"
                        value="{{ old('tanggaltanam', $header->tanggaltanam ?? '') }}"
                        class="border rounded-md border-gray-300 p-2 w-full placeholder-gray-400 text-gray-400 focus:text-black valid:text-black"
                        required>
                </div>
                <div>
                    <label class="block text-md">Tanggal Pengamatan</label>
                    <input type="date" name="tanggalpengamatan" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}"
                        value="{{ old('tanggalpengamatan', $header->tanggalpengamatan ?? '') }}"
                        class="border rounded-md border-gray-300 p-2 w-full placeholder-gray-400 text-gray-400 focus:text-black valid:text-black"
                        required>
                </div>
            </div>

        </div>
        <div class="mx-4 p-6 bg-white rounded-md shadow-md mt-4">
            <div class="text-center text-xl pb-2 mb-6 -mt-2 border-b font-medium border-gray-300">List (ni)</div>

            <div class="table-container">
                <table class="table table-bordered" id="listTable">
                    <thead>
                        <tr>
                            <th class="text-xs font-medium">ni</th>
                            <th class="text-xs font-medium">PPT Aktif</th>
                            <th class="text-xs font-medium">PBT Aktif</th>
                            <th class="text-xs font-medium">Skor 0</th>
                            <th class="text-xs font-medium">Skor 1</th>
                            <th class="text-xs font-medium">Skor 2</th>
                            <th class="text-xs font-medium">Skor 3</th>
                            <th class="text-xs font-medium">Skor 4</th>
                            <th class="text-xs font-medium">Telur PPT</th>
                            <th class="text-xs font-medium">Larva PPT 1</th>
                            <th class="text-xs font-medium">Larva PPT 2</th>
                            <th class="text-xs font-medium">Larva PPT 3</th>
                            <th class="text-xs font-medium">Larva PPT 4</th>
                            <th class="text-xs font-medium">Pupa PPT</th>
                            <th class="text-xs font-medium">Ngengat PPT</th>
                            <th class="text-xs font-medium">Kosong PPT</th>
                            <th class="text-xs font-medium">Telur PBT</th>
                            <th class="text-xs font-medium">Larva PBT 1</th>
                            <th class="text-xs font-medium">Larva PBT 2</th>
                            <th class="text-xs font-medium">Larva PBT 3</th>
                            <th class="text-xs font-medium">Larva PBT 4</th>
                            <th class="text-xs font-medium">Pupa PBT</th>
                            <th class="text-xs font-medium">Ngengat PBT</th>
                            <th class="text-xs font-medium">Kosong PBT</th>
                            <th class="text-xs font-medium">DH</th>
                            <th class="text-xs font-medium">DT</th>
                            <th class="text-xs font-medium">KBP</th>
                            <th class="text-xs font-medium">KBB</th>
                            <th class="text-xs font-medium">KP</th>
                            <th class="text-xs font-medium">Cabuk</th>
                            <th class="text-xs font-medium">Belalang</th>
                            <th class="text-xs font-medium">BTG Terserang Ul.Grayak</th>
                            <th class="text-xs font-medium">Jumlah Ul.Grayak</th>
                            <th class="text-xs font-medium">BTG Terserang SMUT</th>
                            <th class="text-xs font-medium">SMUT Stadia 1</th>
                            <th class="text-xs font-medium">SMUT Stadia 2</th>
                            <th class="text-xs font-medium">SMUT Stadia 3</th>
                            <th class="text-xs font-medium sticky right-0 bg-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lists = old('lists', $isEdit ? $header->lists : [[]]);
                        @endphp
                        @foreach ($lists as $index => $list)
                            <tr>
                                <td>
                                    <input type="text" name="lists[{{ $index }}][nourut]" min="0"
                                        value="{{ $list['nourut'] ?? $index + 1 }}"
                                        class="form-control border rounded-md border-gray-300 text-center" readonly>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][ppt_aktif]"
                                        min="0" value="{{ $list['ppt_aktif'] ?? '' }}" maxlength="5"
                                        autocomplete="off" class="form-control border rounded-md border-gray-300"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][pbt_aktif]"
                                        min="0" value="{{ $list['pbt_aktif'] ?? '' }}" maxlength="5"
                                        autocomplete="off" class="form-control border rounded-md border-gray-300"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][skor0]" min="0"
                                        value="{{ $list['skor0'] ?? '' }}" maxlength="5" autocomplete="off"
                                        class="form-control border rounded-md border-gray-300" required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][skor1]" min="0"
                                        value="{{ $list['skor1'] ?? '' }}" maxlength="5" autocomplete="off"
                                        class="form-control border rounded-md border-gray-300" required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][skor2]" min="0"
                                        value="{{ $list['skor2'] ?? '' }}" maxlength="5" autocomplete="off"
                                        class="form-control border rounded-md border-gray-300" required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][skor3]" min="0"
                                        value="{{ $list['skor3'] ?? '' }}" maxlength="5"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][skor4]" min="0"
                                        value="{{ $list['skor4'] ?? '' }}" maxlength="5"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][telur_ppt]"
                                        min="0" value="{{ $list['telur_ppt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_ppt1]"
                                        min="0" value="{{ $list['larva_ppt1'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_ppt2]"
                                        min="0" value="{{ $list['larva_ppt2'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_ppt3]"
                                        min="0" value="{{ $list['larva_ppt3'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_ppt4]"
                                        min="0" value="{{ $list['larva_ppt4'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][pupa_ppt]" min="0"
                                        value="{{ $list['pupa_ppt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][ngengat_ppt]"
                                        min="0" value="{{ $list['ngengat_ppt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][kosong_ppt]"
                                        min="0" value="{{ $list['kosong_ppt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][telur_pbt]"
                                        min="0" value="{{ $list['telur_pbt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_pbt1]"
                                        min="0" value="{{ $list['larva_pbt1'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_pbt2]"
                                        min="0" value="{{ $list['larva_pbt2'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_pbt3]"
                                        min="0" value="{{ $list['larva_pbt3'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][larva_pbt4]"
                                        min="0" value="{{ $list['larva_pbt4'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][pupa_pbt]" min="0"
                                        value="{{ $list['pupa_pbt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][ngengat_pbt]"
                                        min="0" value="{{ $list['ngengat_pbt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][kosong_pbt]"
                                        min="0" value="{{ $list['kosong_pbt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][dh]" min="0"
                                        value="{{ $list['dh'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][dt]" min="0"
                                        value="{{ $list['dt'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][kbp]" min="0"
                                        value="{{ $list['kbp'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][kbb]" min="0"
                                        value="{{ $list['kbb'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][kp]" min="0"
                                        value="{{ $list['kp'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][cabuk]" min="0"
                                        value="{{ $list['cabuk'] ?? '' }}" maxlength="3"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][belalang]" min="0"
                                        value="{{ $list['belalang'] ?? '' }}" maxlength="3"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][serang_grayak]"
                                        min="0" value="{{ $list['serang_grayak'] ?? '' }}" maxlength="3"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][jum_grayak]"
                                        min="0" value="{{ $list['jum_grayak'] ?? '' }}" maxlength="3"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][serang_smut]"
                                        min="0" value="{{ $list['serang_smut'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][smut_stadia1]"
                                        min="0" value="{{ $list['smut_stadia1'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][smut_stadia2]"
                                        min="0" value="{{ $list['smut_stadia2'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td>
                                    <input type="number" name="lists[{{ $index }}][smut_stadia3]"
                                        min="0" value="{{ $list['smut_stadia3'] ?? '' }}" maxlength="4"
                                        class="form-control border rounded-md border-gray-300" autocomplete="off"
                                        required>
                                </td>
                                <td class="sticky right-0 bg-white">
                                    <button type="button" class="flex items-center group remove-row">
                                        <svg class="w-6 h-6 text-red-500 dark:text-white group-hover:hidden"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>

                                        <svg class="w-6 h-6 text-red-500 dark:text-white hidden group-hover:block"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span
                                            class="text-red-500 dark:text-white group-hover:underline font-medium text-sm">Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-center items-center mt-2">
                <button type="button" id="addRow" class="flex items-center group gap-1">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white group-hover:hidden" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white hidden group-hover:block" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-800 dark:text-white group-hover:underline font-bold text-sm">Add</span>
                </button>
            </div>
            <div class="mt-6 flex gap-2">
                <button type="submit"
                    class="flex items-center space-x-2 bg-green-500 text-white px-4 py-2 rounded w-full md:w-auto hover:bg-green-600">
                    <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 11.917 9.724 16.5 19 7.5" />
                    </svg>
                    <span>{{ $buttonSubmit }}</span>
                </button>
                <a href="{{ route('input.hpt.index') }}"
                    class="flex items-center space-x-2 bg-red-500 text-white px-4 py-2 rounded w-full md:w-auto hover:bg-red-600">
                    <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                    <span>Cancel</span>
                </a>
            </div>
        </div>
    </form>

    <div id="scrollToTop">
        <button><svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v13m0-13 4 4m-4-4-4 4" />
            </svg>
        </button>
    </div>

    <script>
        document.getElementById('addRow').addEventListener('click', () => {
            const table = document.getElementById('listTable').querySelector('tbody');
            const rowCount = table.rows.length + 1;
            const newRow = `
            <tr>
                <td>
                    <input type="text" name="lists[${rowCount}][nourut]" min="0" value="${rowCount}"
                        class="form-control border rounded-md border-gray-300 text-center" readonly>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][ppt_aktif]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][pbt_aktif]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][skor0]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][skor1]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][skor2]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][skor3]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][skor4]" min="0" maxlength="5"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][telur_ppt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_ppt1]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_ppt2]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_ppt3]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_ppt4]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][pupa_ppt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][ngengat_ppt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][kosong_ppt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][telur_pbt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_pbt1]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_pbt2]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_pbt3]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][larva_pbt4]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][pupa_pbt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][ngengat_pbt]"
                        min="0" maxlength="4" class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][kosong_pbt]"
                        min="0" maxlength="4" class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][dh]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][dt]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][kbp]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][kbb]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][kp]" min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][cabuk]" min="0" maxlength="3"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][belalang]" min="0" maxlength="3"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][serang_grayak]" min="0" maxlength="3"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][jum_grayak]" min="0" maxlength="3"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][serang_smut]"
                        min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][smut_stadia1]"
                        min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][smut_stadia2]"
                        min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td>
                    <input type="number" name="lists[${rowCount}][smut_stadia3]"
                        min="0" maxlength="4"
                        class="form-control border rounded-md border-gray-300" autocomplete="off" required>
                </td>
                <td class="sticky right-0 bg-white">
                    <button type="button" class="flex items-center group remove-row">
                        <svg class="w-6 h-6 text-red-500 dark:text-white group-hover:hidden"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                        </svg>

                        <svg class="w-6 h-6 text-red-500 dark:text-white hidden group-hover:block"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-red-500 dark:text-white hover:underline font-medium text-sm remove-row">Delete</span>
                    </button>
                </td>
            </tr>
        `;
            table.insertAdjacentHTML('beforeend', newRow);
        });

        document.getElementById('listTable').addEventListener('click', (e) => {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
                resetRowNumbers();
            }
        });

        function resetRowNumbers() {
            const rows = document.querySelectorAll('#listTable tbody tr');
            rows.forEach((row, index) => {
                const noUrutInput = row.querySelector('input[name^="lists"][name$="[nourut]"]');
                noUrutInput.value = index + 1;
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#idblokplot').change(function() {
                var idblokplot = $(this).val();
                if (idblokplot) {
                    $.ajax({
                        url: "{{ route('input.hpt.getFieldByMapping') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            idblokplot: idblokplot
                        },
                        success: function(response) {
                            $('#companycode').val(response.companycode);
                            $('#blok').val(response.blok);
                            $('#plot').val(response.plot);
                        },
                        error: function() {
                            alert('Data tidak ditemukan');
                        }
                    });
                } else {
                    $('#companycode, #blok, #plot').val('');
                }
            });
        });
    </script>
    <script>
        document.querySelectorAll('input[name="nosample"], select[name="idblokplot"]').forEach((element) => {
            element.addEventListener('change', () => {
                const noSample = document.querySelector('input[name="nosample"]').value;
                const kdPlotSample = document.querySelector('select[name="idblokplot"]').value;

                if (noSample && kdPlotSample) {
                    fetch(
                            `{{ route('input.hpt.check-data') }}?nosample=${noSample}&idblokplot=${kdPlotSample}`
                        )
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector('input[name="varietas"]').value = data.varietas;
                                document.querySelector('input[name="tanggaltanam"]').value = data.tanggaltanam;
                            } else {
                                alert("Data tidak ditemukan, silakan isi secara manual.");
                                document.querySelector('input[name="varietas"]').value = '';
                                document.querySelector('input[name="tanggaltanam"]').value = '';
                            }
                        })
                        .catch(err => console.error("Terjadi kesalahan:", err));
                }
            });
        });
    </script>

</x-layout>
