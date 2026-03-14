<div class="md:col-span-2">
    <input type="hidden" name="new_jenis_tambang" id="new_jenis_tambang" value="{{ old('new_jenis_tambang') }}">
    <input type="hidden" name="new_jenis_tambang_en" id="new_jenis_tambang_en" value="{{ old('new_jenis_tambang_en') }}">

    <div class="flex flex-col gap-3 rounded-xl border border-gray-700/40 bg-gray-800/20 p-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-medium text-gray-200">Jenis tambang belum ada?</p>
            <p id="commodity-modal-summary" class="mt-1 text-xs text-gray-500">
                Tambahkan jenis tambang baru melalui popup, lalu sistem akan langsung memilihkannya di input.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="commodity-modal-open"
                class="inline-flex items-center justify-center rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-2.5 text-sm font-medium text-emerald-400 transition hover:bg-emerald-500/20">
                + Tambah Jenis Tambang
            </button>
            <button type="button" id="commodity-modal-edit"
                class="inline-flex items-center justify-center rounded-xl border border-gray-700/60 bg-gray-800/60 px-4 py-2.5 text-sm font-medium text-gray-300 transition hover:border-blue-500/30 hover:bg-blue-500/10 hover:text-blue-300">
                Edit Terpilih
            </button>
        </div>
    </div>
</div>

<div id="commodity-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/70 px-4 py-8 backdrop-blur-sm">
    <div class="w-full max-w-lg rounded-2xl border border-gray-800/70 bg-slate-900 shadow-2xl shadow-black/30">
        <div class="flex items-center justify-between border-b border-gray-800/70 px-6 py-4">
            <div>
                <h3 id="commodity-modal-title" class="text-base font-semibold text-white">Tambah Jenis Tambang Baru</h3>
                <p id="commodity-modal-description" class="mt-1 text-xs text-gray-400">Isi nama utama Bahasa Indonesia dan translasi English.</p>
            </div>
            <button type="button" id="commodity-modal-close" class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-800 hover:text-gray-300" aria-label="Tutup modal jenis tambang">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="space-y-4 px-6 py-5">
            <div>
                <label for="commodity-modal-name" class="mb-2 block text-xs text-gray-500">Jenis Tambang (Bahasa Indonesia)</label>
                <input type="text" id="commodity-modal-name"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"
                    placeholder="Contoh: Pasir Silika">
            </div>
            <div>
                <label for="commodity-modal-name-en" class="mb-2 block text-xs text-gray-500">Commodity Translation (English)</label>
                <input type="text" id="commodity-modal-name-en"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"
                    placeholder="Example: Silica Sand">
            </div>
            <p id="commodity-modal-error" class="hidden text-sm text-red-400"></p>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-gray-800/70 px-6 py-4">
            <button type="button" id="commodity-modal-cancel" class="rounded-xl px-4 py-2.5 text-sm text-gray-400 transition hover:text-white">
                Batal
            </button>
            <button type="button" id="commodity-modal-save"
                class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-medium text-white transition hover:from-emerald-500 hover:to-teal-500">
                Simpan Jenis Ini
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectEl = document.getElementById('jenis_tambang');
            const translationInputEl = document.getElementById('jenis_tambang_en');
            const comboboxInputEl = document.getElementById('jenis_tambang_combobox');
            const optionsPanelEl = document.getElementById('jenis_tambang_options');
            const hiddenNameEl = document.getElementById('new_jenis_tambang');
            const hiddenNameEnEl = document.getElementById('new_jenis_tambang_en');
            const summaryEl = document.getElementById('commodity-modal-summary');
            const openButtonEl = document.getElementById('commodity-modal-open');
            const editButtonEl = document.getElementById('commodity-modal-edit');
            const modalEl = document.getElementById('commodity-modal');
            const closeButtonEl = document.getElementById('commodity-modal-close');
            const cancelButtonEl = document.getElementById('commodity-modal-cancel');
            const saveButtonEl = document.getElementById('commodity-modal-save');
            const modalNameEl = document.getElementById('commodity-modal-name');
            const modalNameEnEl = document.getElementById('commodity-modal-name-en');
            const modalErrorEl = document.getElementById('commodity-modal-error');
            const modalTitleEl = document.getElementById('commodity-modal-title');
            const modalDescriptionEl = document.getElementById('commodity-modal-description');
            const commodityStoreUrl = @json(route('admin.jenis-tambang.store'));
            const csrfToken = @json(csrf_token());
            let modalMode = 'create';

            if (!selectEl || !translationInputEl || !comboboxInputEl || !optionsPanelEl || !hiddenNameEl || !hiddenNameEnEl || !summaryEl || !modalEl) {
                return;
            }

            const baseSummary = 'Tambahkan jenis tambang baru melalui popup, lalu sistem akan langsung memilihkannya di input.';
            const baseOptions = Array.from(selectEl.options)
                .slice(1)
                .map((option) => ({
                    value: option.value,
                    label: option.textContent,
                    translation: option.dataset.translation || '',
                }));

            const updateEditButtonState = () => {
                const hasSelectedCommodity = Boolean(selectEl.value);
                editButtonEl?.toggleAttribute('disabled', !hasSelectedCommodity);
                editButtonEl?.classList.toggle('opacity-50', !hasSelectedCommodity);
                editButtonEl?.classList.toggle('cursor-not-allowed', !hasSelectedCommodity);
            };

            const closeCombobox = () => {
                optionsPanelEl.classList.add('hidden');
            };

            const openCombobox = () => {
                optionsPanelEl.classList.remove('hidden');
            };

            const syncComboboxLabel = () => {
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                comboboxInputEl.value = selectedOption && selectedOption.value
                    ? selectedOption.textContent
                    : '';
            };

            const chooseCommodity = (value) => {
                selectEl.value = value || '';
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                translationInputEl.value = selectedOption?.dataset.translation || '';
                syncComboboxLabel();
                updateEditButtonState();
                closeCombobox();
            };

            const renderCommodityOptions = (query = '', { keepOpen = false } = {}) => {
                const selectedValue = selectEl.value;
                const normalizedQuery = query.trim().toLowerCase();
                const filteredOptions = !normalizedQuery
                    ? baseOptions
                    : baseOptions.filter((option) =>
                        [option.label, option.translation]
                            .filter(Boolean)
                            .join(' ')
                            .toLowerCase()
                            .includes(normalizedQuery)
                    );

                const selectedOptionMeta = baseOptions.find((option) => option.value === selectedValue);
                const optionPool = filteredOptions.some((option) => option.value === selectedValue) || !selectedOptionMeta
                    ? filteredOptions
                    : [selectedOptionMeta, ...filteredOptions];

                selectEl.innerHTML = '<option value="">-- Pilih --</option>';
                optionsPanelEl.innerHTML = '';

                optionPool.forEach((option) => {
                    const optionEl = new Option(option.label, option.value, false, option.value === selectedValue);
                    optionEl.dataset.translation = option.translation || '';
                    selectEl.add(optionEl);

                    const itemButton = document.createElement('button');
                    itemButton.type = 'button';
                    itemButton.className = `flex w-full items-start justify-between gap-3 rounded-lg px-3 py-2.5 text-left text-sm transition ${
                        option.value === selectedValue
                            ? 'bg-emerald-500/15 text-emerald-300'
                            : 'text-gray-300 hover:bg-gray-800/80 hover:text-white'
                    }`;
                    itemButton.innerHTML = `
                        <span class="min-w-0">
                            <span class="block truncate font-medium">${option.label}</span>
                            ${option.translation ? `<span class="mt-0.5 block truncate text-xs text-gray-500">${option.translation}</span>` : ''}
                        </span>
                        ${option.value === selectedValue ? '<span class="mt-0.5 text-xs text-emerald-400">Selected</span>' : ''}
                    `;
                    itemButton.addEventListener('click', () => chooseCommodity(option.value));
                    optionsPanelEl.appendChild(itemButton);
                });

                if (selectedValue) {
                    selectEl.value = selectedValue;
                }

                if (optionsPanelEl.children.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'rounded-lg px-3 py-3 text-sm text-gray-500';
                    emptyState.textContent = 'Tidak ada jenis tambang yang cocok.';
                    optionsPanelEl.appendChild(emptyState);
                }

                if (keepOpen) {
                    openCombobox();
                }
            };

            const syncTranslationFromSelectedOption = () => {
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                if (!selectedOption) {
                    return;
                }

                translationInputEl.value = selectedOption.dataset.translation || '';
                syncComboboxLabel();
            };

            const syncSelectedOption = (name, nameEn, created = false) => {
                let optionEl = Array.from(selectEl.options).find((option) => option.value === name);

                if (!optionEl) {
                    optionEl = new Option(name, name, true, true);
                    selectEl.add(optionEl);
                }

                optionEl.dataset.translation = nameEn || '';
                optionEl.selected = true;
                selectEl.value = name;
                translationInputEl.value = nameEn || '';
                hiddenNameEl.value = '';
                hiddenNameEnEl.value = '';
                summaryEl.textContent = created
                    ? `Jenis tambang "${name}" berhasil ditambahkan dan dipilih.`
                    : `Jenis tambang "${name}" sudah tersedia dan dipilih.`;
                const existingMeta = baseOptions.find((option) => option.value === name);
                if (existingMeta) {
                    existingMeta.label = name;
                    existingMeta.translation = nameEn || '';
                } else {
                    baseOptions.push({
                        value: name,
                        label: name,
                        translation: nameEn || '',
                    });
                    baseOptions.sort((left, right) => left.label.localeCompare(right.label));
                }
                renderCommodityOptions(comboboxInputEl.value || '');
                chooseCommodity(name);
            };

            const ensurePendingOption = () => {
                const pendingName = hiddenNameEl.value.trim();
                const pendingNameEn = hiddenNameEnEl.value.trim();

                if (!pendingName) {
                    summaryEl.textContent = baseSummary;
                    return;
                }

                syncSelectedOption(pendingName, pendingNameEn, false);
            };

            const closeModal = () => {
                modalEl.classList.add('hidden');
                modalEl.classList.remove('flex');
                modalErrorEl.classList.add('hidden');
                modalErrorEl.textContent = '';
            };

            const openModal = (mode = 'create') => {
                modalMode = mode;
                if (mode === 'edit') {
                    const selectedOption = selectEl.options[selectEl.selectedIndex];
                    if (!selectedOption || !selectedOption.value) {
                        summaryEl.textContent = 'Pilih jenis tambang terlebih dahulu sebelum mengedit translasi.';
                        updateEditButtonState();
                        return;
                    }

                    modalTitleEl.textContent = 'Edit Jenis Tambang';
                    modalDescriptionEl.textContent = 'Perbarui nama utama atau translasi English untuk jenis tambang yang dipilih.';
                    saveButtonEl.textContent = 'Simpan Perubahan';
                    modalNameEl.value = selectedOption.value;
                    modalNameEnEl.value = selectedOption.dataset.translation || '';
                } else {
                    modalTitleEl.textContent = 'Tambah Jenis Tambang Baru';
                    modalDescriptionEl.textContent = 'Isi nama utama Bahasa Indonesia dan translasi English.';
                    saveButtonEl.textContent = 'Simpan Jenis Ini';
                    modalNameEl.value = hiddenNameEl.value || '';
                    modalNameEnEl.value = hiddenNameEnEl.value || '';
                }

                modalErrorEl.classList.add('hidden');
                modalErrorEl.textContent = '';
                modalEl.classList.remove('hidden');
                modalEl.classList.add('flex');
                setTimeout(() => modalNameEl.focus(), 10);
            };

            const clearPendingOption = () => {
                hiddenNameEl.value = '';
                hiddenNameEnEl.value = '';
                summaryEl.textContent = baseSummary;
                syncTranslationFromSelectedOption();
            };

            openButtonEl?.addEventListener('click', () => openModal('create'));
            editButtonEl?.addEventListener('click', () => openModal('edit'));
            closeButtonEl?.addEventListener('click', closeModal);
            cancelButtonEl?.addEventListener('click', closeModal);

            modalEl.addEventListener('click', (event) => {
                if (event.target === modalEl) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modalEl.classList.contains('hidden')) {
                    closeModal();
                }
            });

            saveButtonEl?.addEventListener('click', async () => {
                const name = modalNameEl.value.trim().replace(/\s+/g, ' ');
                const nameEn = modalNameEnEl.value.trim().replace(/\s+/g, ' ');

                if (!name) {
                    modalErrorEl.textContent = 'Jenis tambang Bahasa Indonesia wajib diisi.';
                    modalErrorEl.classList.remove('hidden');
                    modalNameEl.focus();
                    return;
                }

                saveButtonEl.disabled = true;
                saveButtonEl.classList.add('opacity-70', 'cursor-wait');

                try {
                    const response = await fetch(commodityStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            nama: name,
                            nama_en: nameEn,
                        }),
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        const firstError = payload?.errors
                            ? Object.values(payload.errors).flat()[0]
                            : payload?.message;
                        throw new Error(firstError || 'Gagal menyimpan jenis tambang.');
                    }

                    hiddenNameEl.value = payload.data.nama;
                    hiddenNameEnEl.value = payload.data.nama_en || '';
                    syncSelectedOption(
                        payload.data.nama,
                        payload.data.nama_en || '',
                        Boolean(payload.data.created),
                    );
                    closeModal();
                } catch (error) {
                    modalErrorEl.textContent = error.message || 'Gagal menyimpan jenis tambang.';
                    modalErrorEl.classList.remove('hidden');
                } finally {
                    saveButtonEl.disabled = false;
                    saveButtonEl.classList.remove('opacity-70', 'cursor-wait');
                }
            });

            selectEl.addEventListener('change', () => {
                syncTranslationFromSelectedOption();
                updateEditButtonState();
            });

            comboboxInputEl.addEventListener('focus', () => {
                renderCommodityOptions(comboboxInputEl.value, { keepOpen: true });
            });

            comboboxInputEl.addEventListener('input', () => {
                renderCommodityOptions(comboboxInputEl.value, { keepOpen: true });
            });

            comboboxInputEl.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeCombobox();
                }
            });

            document.addEventListener('click', (event) => {
                if (
                    event.target !== comboboxInputEl &&
                    !optionsPanelEl.contains(event.target)
                ) {
                    closeCombobox();
                }
            });

            renderCommodityOptions('');
            ensurePendingOption();
            if (!hiddenNameEl.value.trim()) {
                syncTranslationFromSelectedOption();
            }
            updateEditButtonState();
        });
    </script>
@endpush
