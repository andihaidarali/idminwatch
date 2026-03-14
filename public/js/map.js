/**
 * Indonesia Mining Watch - MapLibre GL JS
 * ========================================
 * Initializes the map, loads GeoJSON layers from Laravel API,
 * and handles user interactions (click popups, layer toggles, search).
 *
 */

// ============================================================
// BASEMAP STYLES
// ============================================================
const BASEMAPS = {
    dark: {
        version: 8,
        sources: {
            "dark-tiles": {
                type: "raster",
                tiles: [
                    "https://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}",
                ],
                tileSize: 256,
                attribution: "&copy; Esri, HERE, Garmin, FAO, NOAA, USGS",
            },
        },
        layers: [
            {
                id: "dark-layer",
                type: "raster",
                source: "dark-tiles",
                minzoom: 0,
                maxzoom: 19,
            },
        ],
    },
    satellite: {
        version: 8,
        sources: {
            "satellite-tiles": {
                type: "raster",
                tiles: [
                    "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
                ],
                tileSize: 256,
                attribution: "&copy; Esri",
            },
        },
        layers: [
            {
                id: "satellite-layer",
                type: "raster",
                source: "satellite-tiles",
                minzoom: 0,
                maxzoom: 19,
            },
        ],
    },
    streets: {
        version: 8,
        sources: {
            "osm-tiles": {
                type: "raster",
                tiles: ["https://tile.openstreetmap.org/{z}/{x}/{y}.png"],
                tileSize: 256,
                attribution: "&copy; OpenStreetMap",
            },
        },
        layers: [
            {
                id: "osm-layer",
                type: "raster",
                source: "osm-tiles",
                minzoom: 0,
                maxzoom: 19,
            },
        ],
    },
};

const savedDashboardTheme =
    localStorage.getItem("dashboardTheme") === "light" ? "light" : "dark";
const DEFAULT_MAP_CENTER = [117.23534116173357, -1.4089331737602748];
const DEFAULT_MAP_ZOOM = 4.485;

// ============================================================
// MAP INITIALIZATION
// ============================================================
const map = new maplibregl.Map({
    container: "map",
    style: BASEMAPS[savedDashboardTheme === "light" ? "streets" : "dark"],
    center: DEFAULT_MAP_CENTER,
    zoom: DEFAULT_MAP_ZOOM,
    attributionControl: false,
});

map.on("error", (event) => {
    const error = event?.error;
    const message = error?.message || "";

    if (error?.name === "AbortError" || message.includes("signal is aborted")) {
        return;
    }

    console.error("MapLibre error:", error || event);
});

// Add controls
map.addControl(new maplibregl.NavigationControl(), "bottom-right");
map.addControl(new maplibregl.ScaleControl({ maxWidth: 200 }), "bottom-right");

const FOREST_CLASS_STYLES = {
    "Hutan Lindung": {
        code: "HL",
        fill: "#3c5b00",
        outline: "#2E7D32",
        labelEn: "Protection Forest",
    },
    "Hutan Produksi Tetap": {
        code: "HP",
        fill: "#FFFF00",
        outline: "#C7B800",
        labelEn: "Permanent Production Forest",
    },
    "Hutan Produksi Terbatas": {
        code: "HPT",
        fill: "#70A800",
        outline: "#5A8700",
        labelEn: "Limited Production Forest",
    },
    "Hutan Produksi Yang Dapat Dikonversi": {
        code: "HPK",
        fill: "#FFAA00",
        outline: "#D97706",
        labelEn: "Convertible Production Forest",
    },
    "Kawasan Konservasi": {
        code: "KSA/KPA",
        fill: "#9C10B5",
        outline: "#7E22CE",
        labelEn: "Conservation Area",
    },
    "Kawasan Konservasi Laut": {
        code: "KKL",
        fill: "#00C5FF",
        outline: "#0284C7",
        labelEn: "Marine Conservation Area",
    },
};

const DEFAULT_FOREST_STYLE = {
    code: "LAIN",
    fill: "#64748B",
    outline: "#475569",
};

const FOREST_FILL_COLOR = [
    "match",
    ["get", "deskripsi"],
    ...Object.entries(FOREST_CLASS_STYLES).flatMap(([name, style]) => [
        name,
        style.fill,
    ]),
    DEFAULT_FOREST_STYLE.fill,
];

const FOREST_OUTLINE_COLOR = [
    "match",
    ["get", "deskripsi"],
    ...Object.entries(FOREST_CLASS_STYLES).flatMap(([name, style]) => [
        name,
        style.outline,
    ]),
    DEFAULT_FOREST_STYLE.outline,
];

let galleryImages = [];
let activeGalleryIndex = 0;
let activeInfoGalleryIndex = 0;
let infoGalleryTouchStartX = null;
let infoGalleryTouchStartY = null;
let activeTambangState = null;
let currentLocale =
    localStorage.getItem("dashboardLocale") === "en" ? "en" : "id";
const dashboardRoot = document.getElementById("dashboard-root");
const dashboardUrl =
    dashboardRoot?.dataset.dashboardUrl || window.location.origin;
const sharedUrlTemplate =
    dashboardRoot?.dataset.sharedUrlTemplate ||
    `${window.location.origin}/mining-area/__PUBLIC_UID__`;
const initialSharedTambangUid = (
    dashboardRoot?.dataset.sharedTambangUid || ""
).trim();
const defaultDashboardTitle =
    dashboardRoot?.dataset.defaultTitle || "Indonesia Mining Watch - Dashboard";
const defaultDashboardDescription =
    dashboardRoot?.dataset.defaultDescription ||
    "WebGIS pemantauan wilayah pertambangan - Indonesia Mining Watch.";
let pendingSharedTambangUid =
    initialSharedTambangUid ||
    new URLSearchParams(window.location.search).get("mine") ||
    "";
let tambangListItemsByPublicUid = new Map();
let sidebarTambangItems = [];
let filteredSidebarTambangItems = [];
let visibleTambangCount = 10;
let dashboardFilters = {
    provinsi: "",
    jenis_tambang: "",
};
let dashboardFilterOptionsCache = {
    provinsi: [],
    jenis_tambang: [],
};
let currentTheme = savedDashboardTheme;
let currentBasemap = currentTheme === "light" ? "streets" : "dark";
let lastSpatialSourceUrls = {
    hutan: null,
    tambang: null,
    overlap: null,
};
let sidebarSearchTimer = null;

function persistDashboardTheme(theme) {
    currentTheme = theme === "light" ? "light" : "dark";
    localStorage.setItem("dashboardTheme", currentTheme);
}

function updateThemeControls() {
    const currentLabel = document.getElementById("dashboard-theme-current");
    const darkCheck = document.getElementById("theme-check-dark");
    const lightCheck = document.getElementById("theme-check-light");
    const darkOption = document.getElementById("theme-option-dark");
    const lightOption = document.getElementById("theme-option-light");
    const currentDarkIcon = document.getElementById("theme-current-dark-icon");
    const currentLightIcon = document.getElementById(
        "theme-current-light-icon",
    );

    if (currentLabel) {
        currentLabel.textContent =
            currentTheme === "light" ? t("theme_light") : t("theme_dark");
    }

    if (darkCheck) {
        darkCheck.classList.toggle("hidden", currentTheme !== "dark");
    }

    if (lightCheck) {
        lightCheck.classList.toggle("hidden", currentTheme !== "light");
    }

    if (darkOption) {
        darkOption.classList.toggle("is-active", currentTheme === "dark");
    }

    if (lightOption) {
        lightOption.classList.toggle("is-active", currentTheme === "light");
    }

    if (currentDarkIcon) {
        currentDarkIcon.classList.toggle("hidden", currentTheme !== "dark");
    }

    if (currentLightIcon) {
        currentLightIcon.classList.toggle("hidden", currentTheme !== "light");
    }
}

function applyDashboardTheme() {
    if (!dashboardRoot) {
        return;
    }

    dashboardRoot.classList.toggle("theme-dark", currentTheme === "dark");
    dashboardRoot.classList.toggle("theme-light", currentTheme === "light");
    dashboardRoot.dataset.theme = currentTheme;
    document.documentElement.classList.toggle("dark", currentTheme === "dark");
    updateThemeControls();

    if (
        activeTambangState?.data &&
        document
            .getElementById("info-overlap-section")
            ?.classList.contains("hidden") === false
    ) {
        renderOverlapChart(activeTambangState.data.overlaps || []);
    }
}

function updateBasemapButtons(activeBasemapId) {
    document.querySelectorAll(".basemap-btn").forEach((button) => {
        const isActive = button.id === `btn-basemap-${activeBasemapId}`;
        button.classList.toggle("active", isActive);
        button.classList.toggle("text-white", isActive);
        button.classList.toggle("bg-emerald-600/30", isActive);
        button.classList.toggle("text-gray-400", !isActive);
    });
}

function setBasemap(basemapId, { reloadLayers = true } = {}) {
    const style = BASEMAPS[basemapId];

    if (!style) {
        return;
    }

    currentBasemap = basemapId;
    updateBasemapButtons(basemapId);
    map.setStyle(style);

    if (reloadLayers) {
        map.once("styledata", () => {
            setTimeout(() => loadAllLayers(), 300);
        });
    }
}

updateBasemapButtons(currentBasemap);

function persistDashboardLocale(locale) {
    currentLocale = locale === "en" ? "en" : "id";
    localStorage.setItem("dashboardLocale", currentLocale);
    document.cookie = `preferred_locale=${currentLocale}; path=/; max-age=31536000; SameSite=Lax`;
}

function syncAboutLinkLanguage() {
    const aboutLink = document.getElementById("dashboard-about-link");

    if (!aboutLink) {
        return;
    }

    const aboutUrl = new URL(aboutLink.href, window.location.origin);
    aboutUrl.searchParams.set("lang", currentLocale);
    aboutLink.href = aboutUrl.toString();
}

const TRANSLATIONS = {
    id: {
        dashboard_subtitle: "Monitoring Wilayah Pertambangan",
        language_label: "Bahasa",
        dashboard_filter_label: "Filter Dashboard",
        search_placeholder: "Cari wilayah tambang...",
        filter_provinsi: "Provinsi",
        filter_jenis_tambang: "Jenis Tambang / Komoditas",
        filter_all_provinces: "Semua Provinsi",
        filter_all_commodities: "Semua Komoditas",
        stat_total_tambang: "Jumlah Izin",
        stat_tambang_overlap: "Izin Overlap",
        stat_luas_tambang: "Total Luas Izin",
        stat_luas_overlap: "Total Luas Overlap",
        tambang_list_title: "Daftar Wilayah Tambang",
        about_link: "About",
        admin_link: "Admin Panel",
        legend_title: "Legenda",
        legend_hutan: "Kawasan Hutan",
        legend_tambang: "Wilayah Tambang",
        legend_overlap: "Area Overlap",
        forest_classification: "Klasifikasi Kawasan Hutan",
        forest_class_hl: "Hutan Lindung",
        forest_class_hp: "Hutan Produksi Tetap",
        forest_class_hpt: "Hutan Produksi Terbatas",
        forest_class_hpk: "Hutan Produksi Konversi",
        forest_class_conservation: "Kawasan Konservasi",
        forest_class_marine: "Konservasi Laut",
        info_sk: "Nomor SK",
        info_status: "Status",
        info_luas_sk: "Luas SK",
        info_luas_overlap: "Luas Overlap",
        info_license_section: "Informasi Izin",
        info_tanggal_berlaku: "Tanggal Berlaku",
        info_tanggal_berakhir: "Tanggal Berakhir",
        info_kegiatan: "Kegiatan",
        info_jenis_izin: "Jenis Izin",
        info_nama_provinsi: "Nama Provinsi",
        info_nama_kabupaten: "Nama Kabupaten",
        info_lokasi: "Lokasi",
        info_company_section: "Profil Perusahaan",
        info_company_name: "Perusahaan",
        info_short_profile: "Profil Singkat",
        info_share_section: "Bagikan",
        info_open_page: "Buka Link Khusus",
        info_gallery_section: "Dokumentasi",
        info_overlap_section: "Rincian Overlap Kawasan Hutan",
        info_overlap_composition: "Komposisi Overlap",
        info_overlap_description:
            "Proporsi total overlap berdasarkan klasifikasi kawasan hutan",
        info_total_overlap: "Total Overlap",
        info_impact_section: "Dampak",
        impact_environmental: "Lingkungan",
        impact_social: "Sosial",
        impact_economic: "Ekonomi",
        gallery_title: "Dokumntasi Foto",
        gallery_counter: "Dokumentasi {current} dari {total}",
        gallery_photo: "Dokumentasi {index}",
        chart_empty: "Tidak ada data",
        chart_empty_description: "Belum ada overlap untuk divisualisasikan.",
        chart_classes: "Kelas",
        theme_dark: "Dark",
        theme_light: "Light",
        basemap_dark: "🌙 Gelap",
        basemap_satellite: "🛰️ Satelit",
        basemap_streets: "🗺️ Jalan",
        status_active: "Aktif",
        status_inactive: "Nonaktif",
        no_permit: "Tanpa SK",
        list_loading: "Memuat data...",
        list_empty_title: "Belum ada data wilayah tambang.",
        list_empty_subtitle: "Upload data via QGIS ke PostGIS.",
        list_failed: "Gagal memuat data.",
        list_load_more: "Muat lebih banyak",
    },
    en: {
        dashboard_subtitle: "Mining Area Monitoring",
        language_label: "Language",
        dashboard_filter_label: "Dashboard Filters",
        search_placeholder: "Search mining areas...",
        filter_provinsi: "Province",
        filter_jenis_tambang: "Commodity",
        filter_all_provinces: "All Provinces",
        filter_all_commodities: "All Commodities",
        stat_total_tambang: "Total Permits",
        stat_tambang_overlap: "Permits with Overlap",
        stat_luas_tambang: "Total Permit Area",
        stat_luas_overlap: "Total Overlap Area",
        tambang_list_title: "Mining Area List",
        about_link: "About",
        admin_link: "Admin Panel",
        legend_title: "Legend",
        legend_hutan: "Forest Area",
        legend_tambang: "Mining Area",
        legend_overlap: "Overlap Area",
        forest_classification: "Forest Area Classification",
        forest_class_hl: "Protection Forest",
        forest_class_hp: "Permanent Production Forest",
        forest_class_hpt: "Limited Production Forest",
        forest_class_hpk: "Convertible Production Forest",
        forest_class_conservation: "Conservation Area",
        forest_class_marine: "Marine Conservation Area",
        info_sk: "Permit Number",
        info_status: "Status",
        info_luas_sk: "Permit Area",
        info_luas_overlap: "Overlap Area",
        info_license_section: "Permit Information",
        info_tanggal_berlaku: "Effective Date",
        info_tanggal_berakhir: "Expiry Date",
        info_kegiatan: "Activity",
        info_jenis_izin: "Permit Type",
        info_nama_provinsi: "Province",
        info_nama_kabupaten: "Regency",
        info_lokasi: "Location",
        info_company_section: "Company Profile",
        info_company_name: "Company",
        info_short_profile: "Short Profile",
        info_share_section: "Share",
        info_open_page: "Open Dedicated Link",
        info_gallery_section: "Documentation",
        info_overlap_section: "Overlap Breakdown by Forest Area",
        info_overlap_composition: "Overlap Composition",
        info_overlap_description:
            "Share of total overlap by forest area classification",
        info_total_overlap: "Total Overlap",
        info_impact_section: "Impacts",
        impact_environmental: "Environmental",
        impact_social: "Social",
        impact_economic: "Economic",
        gallery_title: "Documentation Gallery",
        gallery_counter: "Documentation {current} of {total}",
        gallery_photo: "Documentation {index}",
        chart_empty: "No data",
        chart_empty_description: "There is no overlap data to visualize yet.",
        chart_classes: "Classes",
        theme_dark: "Dark",
        theme_light: "Light",
        basemap_dark: "🌙 Dark",
        basemap_satellite: "🛰️ Satellite",
        basemap_streets: "🗺️ Streets",
        status_active: "Active",
        status_inactive: "Inactive",
        no_permit: "No Permit",
        list_loading: "Loading data...",
        list_empty_title: "No mining area data available yet.",
        list_empty_subtitle: "Upload the data from QGIS into PostGIS.",
        list_failed: "Failed to load data.",
        list_load_more: "Load more",
    },
};

function t(key, replacements = {}) {
    const dictionary = TRANSLATIONS[currentLocale] || TRANSLATIONS.id;
    let value = dictionary[key] || TRANSLATIONS.id[key] || key;

    Object.entries(replacements).forEach(([token, replacement]) => {
        value = value.replace(`{${token}}`, replacement);
    });

    return value;
}

function setTranslatedText(elementId, key) {
    const element = document.getElementById(elementId);

    if (element) {
        element.textContent = t(key);
    }
}

function updateLanguageButtons() {
    document.querySelectorAll(".dashboard-lang-btn").forEach((button) => {
        const isActive = button.id === `lang-switch-${currentLocale}`;
        button.classList.toggle("bg-emerald-600/30", isActive);
        button.classList.toggle("text-white", isActive);
        button.classList.toggle("text-gray-400", !isActive);
    });
}

function applyDashboardTranslations() {
    document.documentElement.lang = currentLocale;
    document.getElementById("search-input").placeholder =
        t("search_placeholder");

    setTranslatedText("dashboard-subtitle", "dashboard_subtitle");
    setTranslatedText("dashboard-language-label", "language_label");
    setTranslatedText("dashboard-filter-label", "dashboard_filter_label");
    setTranslatedText("label-filter-provinsi", "filter_provinsi");
    setTranslatedText("label-filter-jenis-tambang", "filter_jenis_tambang");
    setTranslatedText("stat-label-total-tambang", "stat_total_tambang");
    setTranslatedText("stat-label-tambang-overlap", "stat_tambang_overlap");
    setTranslatedText("stat-label-luas-tambang", "stat_luas_tambang");
    setTranslatedText("stat-label-luas-overlap", "stat_luas_overlap");
    setTranslatedText("label-tambang-list-title", "tambang_list_title");
    setTranslatedText("label-about-link", "about_link");
    setTranslatedText("label-admin-link", "admin_link");
    setTranslatedText("label-legend-title", "legend_title");
    setTranslatedText("label-legend-hutan", "legend_hutan");
    setTranslatedText("label-legend-tambang", "legend_tambang");
    setTranslatedText("label-legend-overlap", "legend_overlap");
    setTranslatedText("label-forest-classification", "forest_classification");
    setTranslatedText("label-forest-class-hl", "forest_class_hl");
    setTranslatedText("label-forest-class-hp", "forest_class_hp");
    setTranslatedText("label-forest-class-hpt", "forest_class_hpt");
    setTranslatedText("label-forest-class-hpk", "forest_class_hpk");
    setTranslatedText(
        "label-forest-class-conservation",
        "forest_class_conservation",
    );
    setTranslatedText("label-forest-class-marine", "forest_class_marine");
    setTranslatedText("label-info-sk", "info_sk");
    setTranslatedText("label-info-status", "info_status");
    setTranslatedText("label-info-luas-sk", "info_luas_sk");
    setTranslatedText("label-info-luas-overlap", "info_luas_overlap");
    setTranslatedText("label-info-license-section", "info_license_section");
    setTranslatedText("label-info-tanggal-berlaku", "info_tanggal_berlaku");
    setTranslatedText("label-info-tanggal-berakhir", "info_tanggal_berakhir");
    setTranslatedText("label-info-kegiatan", "info_kegiatan");
    setTranslatedText("label-info-jenis-izin", "info_jenis_izin");
    setTranslatedText("label-info-nama-provinsi", "info_nama_provinsi");
    setTranslatedText("label-info-nama-kabupaten", "info_nama_kabupaten");
    setTranslatedText("label-info-lokasi", "info_lokasi");
    setTranslatedText("label-info-company-section", "info_company_section");
    setTranslatedText("label-info-company-name", "info_company_name");
    setTranslatedText("label-info-short-profile", "info_short_profile");
    setTranslatedText("label-info-share-section", "info_share_section");
    setTranslatedText("label-info-open-page", "info_open_page");
    setTranslatedText("label-info-gallery-section", "info_gallery_section");
    setTranslatedText("label-info-overlap-section", "info_overlap_section");
    setTranslatedText(
        "label-info-overlap-composition",
        "info_overlap_composition",
    );
    setTranslatedText(
        "label-info-overlap-description",
        "info_overlap_description",
    );
    setTranslatedText("label-info-total-overlap", "info_total_overlap");
    setTranslatedText("label-info-impact-section", "info_impact_section");
    setTranslatedText("label-impact-environmental", "impact_environmental");
    setTranslatedText("label-impact-social", "impact_social");
    setTranslatedText("label-impact-economic", "impact_economic");
    setTranslatedText("label-gallery-title", "gallery_title");
    setTranslatedText("label-theme-dark", "theme_dark");
    setTranslatedText("label-theme-light", "theme_light");
    setTranslatedText("label-basemap-dark", "basemap_dark");
    setTranslatedText("label-basemap-satellite", "basemap_satellite");
    setTranslatedText("label-basemap-streets", "basemap_streets");

    updateLanguageButtons();
    renderDashboardFilterOptions();
    updateFilterPlaceholders();
    syncAboutLinkLanguage();
    updateThemeControls();
    renderSidebarTambangList();

    if (activeTambangState) {
        renderTambangDetail(activeTambangState.props, activeTambangState.data);
    } else if (galleryImages.length) {
        updateGalleryModal();
    }
}

applyDashboardTheme();
applyDashboardTranslations();

function buildSharedTambangUrl(publicUid) {
    return sharedUrlTemplate.replace(
        "__PUBLIC_UID__",
        encodeURIComponent(publicUid),
    );
}

function updateFilterPlaceholders() {
    const provinsiSelect = document.getElementById("filter-provinsi");
    const jenisTambangSelect = document.getElementById("filter-jenis-tambang");

    if (provinsiSelect?.options.length) {
        provinsiSelect.options[0].textContent = t("filter_all_provinces");
    }

    if (jenisTambangSelect?.options.length) {
        jenisTambangSelect.options[0].textContent = t("filter_all_commodities");
    }
}

function getLocalizedCommodityLabel(item) {
    if (!item) {
        return "-";
    }

    if (currentLocale === "en" && item.jenis_tambang_en) {
        return item.jenis_tambang_en;
    }

    return item.jenis_tambang || item.jenis_tambang_en || "-";
}

function getLocalizedActivityLabel(item) {
    if (!item) {
        return "-";
    }

    if (currentLocale === "en" && item.kegiatan_en) {
        return item.kegiatan_en;
    }

    return item.kegiatan || item.kegiatan_en || "-";
}

function renderDashboardFilterOptions() {
    const provinsiSelect = document.getElementById("filter-provinsi");
    const jenisTambangSelect = document.getElementById("filter-jenis-tambang");

    if (!provinsiSelect || !jenisTambangSelect) {
        return;
    }

    const selectedProvinsi = dashboardFilters.provinsi;
    const selectedJenisTambang = dashboardFilters.jenis_tambang;

    provinsiSelect.innerHTML = [
        `<option value="">${t("filter_all_provinces")}</option>`,
        ...(dashboardFilterOptionsCache.provinsi || []).map(
            (item) => `<option value="${item}">${item}</option>`,
        ),
    ].join("");

    jenisTambangSelect.innerHTML = [
        `<option value="">${t("filter_all_commodities")}</option>`,
        ...(dashboardFilterOptionsCache.jenis_tambang || []).map((item) => {
            if (typeof item === "string") {
                return `<option value="${item}">${item}</option>`;
            }

            const label =
                currentLocale === "en"
                    ? item.label_en || item.label || item.value
                    : item.label || item.value;

            return `<option value="${item.value}">${label}</option>`;
        }),
    ].join("");

    provinsiSelect.value = selectedProvinsi;
    jenisTambangSelect.value = selectedJenisTambang;
}

function updateDashboardMetadata({ title, description, canonicalUrl } = {}) {
    document.title = title || defaultDashboardTitle;

    const resolvedDescription = description || defaultDashboardDescription;
    const metaDescriptionEl = document.querySelector(
        'meta[name="description"]',
    );
    const canonicalEl = document.querySelector('link[rel="canonical"]');
    const ogTitleEl = document.querySelector('meta[property="og:title"]');
    const ogDescriptionEl = document.querySelector(
        'meta[property="og:description"]',
    );
    const ogUrlEl = document.querySelector('meta[property="og:url"]');
    const twitterTitleEl = document.querySelector('meta[name="twitter:title"]');
    const twitterDescriptionEl = document.querySelector(
        'meta[name="twitter:description"]',
    );

    if (metaDescriptionEl) {
        metaDescriptionEl.setAttribute("content", resolvedDescription);
    }

    if (canonicalEl) {
        canonicalEl.setAttribute("href", canonicalUrl || dashboardUrl);
    }

    if (ogTitleEl) {
        ogTitleEl.setAttribute("content", title || defaultDashboardTitle);
    }

    if (ogDescriptionEl) {
        ogDescriptionEl.setAttribute("content", resolvedDescription);
    }

    if (ogUrlEl) {
        ogUrlEl.setAttribute("content", canonicalUrl || dashboardUrl);
    }

    if (twitterTitleEl) {
        twitterTitleEl.setAttribute("content", title || defaultDashboardTitle);
    }

    if (twitterDescriptionEl) {
        twitterDescriptionEl.setAttribute("content", resolvedDescription);
    }
}

function updateDashboardUrl(publicUid = null) {
    const targetUrl = publicUid
        ? buildSharedTambangUrl(publicUid)
        : dashboardUrl;
    window.history.replaceState({ publicUid }, "", targetUrl);
}

function markActiveTambangItem(publicUid = null) {
    document.querySelectorAll(".tambang-item").forEach((item) => {
        const isActive = publicUid && item.dataset.publicUid === publicUid;
        item.classList.toggle("border-emerald-500/50", isActive);
        item.classList.toggle("bg-emerald-500/10", isActive);
        item.classList.toggle(
            "shadow-[0_0_0_1px_rgba(16,185,129,0.18)]",
            isActive,
        );
        item.classList.toggle("border-transparent", !isActive);
    });
}

function getCurrentBboxValue() {
    const bounds = map.getBounds();
    return [
        bounds.getWest(),
        bounds.getSouth(),
        bounds.getEast(),
        bounds.getNorth(),
    ]
        .map((value) => value.toFixed(6))
        .join(",");
}

function buildApiUrl(basePath, { includeBbox = false } = {}) {
    const params = new URLSearchParams();

    if (includeBbox) {
        params.set("bbox", getCurrentBboxValue());
    }

    if (dashboardFilters.provinsi) {
        params.set("provinsi", dashboardFilters.provinsi);
    }

    if (dashboardFilters.jenis_tambang) {
        params.set("jenis_tambang", dashboardFilters.jenis_tambang);
    }

    const queryString = params.toString();
    return queryString ? `${basePath}?${queryString}` : basePath;
}

function getCurrentDashboardFilters() {
    return {
        provinsi: document.getElementById("filter-provinsi")?.value || "",
        jenis_tambang:
            document.getElementById("filter-jenis-tambang")?.value || "",
    };
}

function buildMapLayerFilter() {
    const clauses = ["all"];

    if (dashboardFilters.provinsi) {
        clauses.push([
            "==",
            ["coalesce", ["get", "nama_provinsi"], ""],
            dashboardFilters.provinsi,
        ]);
    }

    if (dashboardFilters.jenis_tambang) {
        clauses.push([
            "==",
            ["coalesce", ["get", "jenis_tambang"], ""],
            dashboardFilters.jenis_tambang,
        ]);
    }

    return clauses.length > 1 ? clauses : null;
}

function applyMapLayerFilters() {
    const layerFilter = buildMapLayerFilter();

    [
        "tambang-fill",
        "tambang-outline",
        "overlap-fill",
        "overlap-outline",
    ].forEach((layerId) => {
        if (map.getLayer(layerId)) {
            map.setFilter(layerId, layerFilter);
        }
    });
}

function flyToDefaultMapView() {
    const currentCenter = map.getCenter();
    const currentZoom = map.getZoom();
    const isAlreadyAtDefaultView =
        Math.abs(currentCenter.lng - DEFAULT_MAP_CENTER[0]) < 0.0001 &&
        Math.abs(currentCenter.lat - DEFAULT_MAP_CENTER[1]) < 0.0001 &&
        Math.abs(currentZoom - DEFAULT_MAP_ZOOM) < 0.01;

    if (isAlreadyAtDefaultView) {
        return false;
    }

    map.flyTo({
        center: DEFAULT_MAP_CENTER,
        zoom: DEFAULT_MAP_ZOOM,
        duration: 1500,
    });

    return true;
}

function resetDashboardMapFocus() {
    if (activeTambangState) {
        closeInfoPanel({ resetView: false });
    }

    return flyToDefaultMapView();
}

function clearSidebarSearchInput() {
    const searchInput = document.getElementById("search-input");

    if (searchInput) {
        searchInput.value = "";
    }
}

function getSidebarTambangSearchQuery() {
    return (document.getElementById("search-input")?.value || "")
        .trim()
        .toLowerCase();
}

function ensureVisibleTambangBatchForPublicUid(publicUid, features) {
    if (!publicUid) {
        return;
    }

    const itemIndex = features.findIndex(
        (item) => item.public_uid === publicUid,
    );

    if (itemIndex === -1) {
        return;
    }

    const requiredVisibleCount = Math.ceil((itemIndex + 1) / 10) * 10;
    visibleTambangCount = Math.max(visibleTambangCount, requiredVisibleCount);
}

function renderSidebarTambangList() {
    const listEl = document.getElementById("tambang-list");
    const loadMoreWrapEl = document.getElementById(
        "tambang-list-load-more-wrap",
    );
    const loadMoreButtonEl = document.getElementById("tambang-list-load-more");

    if (!listEl) {
        return;
    }

    const query = getSidebarTambangSearchQuery();
    filteredSidebarTambangItems = sidebarTambangItems.filter((item) => {
        if (!query) {
            return true;
        }

        const searchableText = [
            item.nama,
            item.nomor_sk,
            item.jenis_tambang,
            item.jenis_tambang_en,
            item.kegiatan,
            item.kegiatan_en,
            item.nama_provinsi,
        ]
            .filter(Boolean)
            .join(" ")
            .toLowerCase();

        return searchableText.includes(query);
    });

    ensureVisibleTambangBatchForPublicUid(
        pendingSharedTambangUid ||
            activeTambangState?.props?.public_uid ||
            null,
        filteredSidebarTambangItems,
    );

    if (filteredSidebarTambangItems.length === 0) {
        listEl.innerHTML = `
            <div class="text-sm text-gray-500 text-center py-8">
                <p>${t("list_empty_title")}</p>
                <p class="text-xs mt-1 text-gray-600">${t("list_empty_subtitle")}</p>
            </div>`;

        loadMoreWrapEl?.classList.add("hidden");
        return;
    }

    const itemsToRender = filteredSidebarTambangItems.slice(
        0,
        visibleTambangCount,
    );

    listEl.innerHTML = itemsToRender
        .map((item) => {
            const hasOverlap = Number(item.luas_overlap || 0) > 0;
            const lng = Number(item.lng);
            const lat = Number(item.lat);

            return `
                <button class="tambang-item w-full text-left p-3 bg-gray-800/30 hover:bg-gray-800/60 rounded-xl transition-all group border border-transparent hover:border-gray-700/50"
                        data-id="${item.id}" data-public-uid="${item.public_uid || ""}" data-lng="${lng}" data-lat="${lat}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-200 group-hover:text-white truncate">${item.nama || "-"}</p>
                            <p class="text-xs text-gray-500 mt-0.5">${item.nomor_sk || t("no_permit")}</p>
                        </div>
                        ${
                            hasOverlap
                                ? `
                            <span class="shrink-0 ml-2 px-2 py-0.5 bg-red-500/10 text-red-400 text-xs font-medium rounded-md border border-red-500/20">
                                ${formatAreaValue(item.luas_overlap)} Ha
                            </span>
                        `
                                : ""
                        }
                    </div>
                    <p class="mt-2 text-xs text-gray-500 truncate">${getLocalizedCommodityLabel(item)}${getLocalizedActivityLabel(item) !== "-" ? ` • ${getLocalizedActivityLabel(item)}` : ""}</p>
                </button>
            `;
        })
        .join("");

    listEl.querySelectorAll(".tambang-item").forEach((item, index) => {
        const tambangItem = itemsToRender[index];

        item.addEventListener("click", async () => {
            const lng = parseFloat(item.dataset.lng);
            const lat = parseFloat(item.dataset.lat);

            map.flyTo({ center: [lng, lat], zoom: 11, duration: 1500 });
            await showTambangDetail(tambangItem);
        });
    });

    markActiveTambangItem(activeTambangState?.props?.public_uid || null);

    if (loadMoreWrapEl && loadMoreButtonEl) {
        const hasMoreItems =
            filteredSidebarTambangItems.length > itemsToRender.length;
        loadMoreWrapEl.classList.toggle("hidden", !hasMoreItems);

        if (hasMoreItems) {
            loadMoreButtonEl.textContent = t("list_load_more");
        }
    }
}

function applyTambangSearchFilter() {
    visibleTambangCount = 10;
    renderSidebarTambangList();
}

async function loadDashboardFilterOptions() {
    try {
        const res = await fetch("/api/filter-options");
        const data = await res.json();
        dashboardFilterOptionsCache = {
            provinsi: data.provinsi || [],
            jenis_tambang: data.jenis_tambang || [],
        };
        renderDashboardFilterOptions();
    } catch (error) {
        console.error("Error loading dashboard filter options:", error);
    }
}

function applyDashboardDataFilters() {
    dashboardFilters = getCurrentDashboardFilters();
    applyMapLayerFilters();

    if (!dashboardFilters.provinsi && !dashboardFilters.jenis_tambang) {
        clearSidebarSearchInput();
    }

    const didFlyToDefaultView = resetDashboardMapFocus();

    if (!didFlyToDefaultView) {
        scheduleSpatialSourceRefresh(0);
    }

    loadStatistics();
    loadTambangList();
}

// ============================================================
// LOAD DATA LAYERS
// ============================================================
map.on("load", () => {
    applyDashboardTranslations();
    loadDashboardFilterOptions();
    loadAllLayers();
    loadStatistics();
    loadTambangList();
});

async function loadAllLayers() {
    try {
        lastSpatialSourceUrls = {
            hutan: buildApiUrl("/api/geojson/hutan", { includeBbox: true }),
            tambang: buildApiUrl("/api/geojson/tambang", { includeBbox: true }),
            overlap: buildApiUrl("/api/geojson/overlap", { includeBbox: true }),
        };

        // --- 1. Kawasan Hutan (Green) ---
        map.addSource("hutan", {
            type: "geojson",
            data: lastSpatialSourceUrls.hutan,
        });
        map.addLayer({
            id: "hutan-fill",
            type: "fill",
            source: "hutan",
            paint: {
                "fill-color": FOREST_FILL_COLOR,
                "fill-opacity": 0.35,
            },
        });
        map.addLayer({
            id: "hutan-outline",
            type: "line",
            source: "hutan",
            paint: {
                "line-color": FOREST_OUTLINE_COLOR,
                "line-width": 1.5,
                "line-opacity": 0.9,
            },
        });

        // --- 2. Wilayah Tambang (Orange, transparent) ---
        map.addSource("tambang", {
            type: "geojson",
            data: lastSpatialSourceUrls.tambang,
        });
        map.addLayer({
            id: "tambang-fill",
            type: "fill",
            source: "tambang",
            paint: {
                "fill-color": "#f97316",
                "fill-opacity": 0.35,
            },
        });
        map.addLayer({
            id: "tambang-outline",
            type: "line",
            source: "tambang",
            paint: {
                "line-color": "#ea580c",
                "line-width": 2,
                "line-opacity": 0.8,
            },
        });

        // --- 3. Overlap Areas (RED highlight — key visual) ---
        map.addSource("overlap", {
            type: "geojson",
            data: lastSpatialSourceUrls.overlap,
        });
        map.addLayer({
            id: "overlap-fill",
            type: "fill",
            source: "overlap",
            paint: {
                "fill-color": "#ef4444",
                "fill-opacity": 0.55,
                "fill-outline-color": "#dc2626",
            },
        });
        map.addLayer({
            id: "overlap-outline",
            type: "line",
            source: "overlap",
            paint: {
                "line-color": "#dc2626",
                "line-width": 2.5,
                "line-dasharray": [3, 2],
            },
        });

        applyMapLayerFilters();

        // Hover effects for tambang
        map.on("mouseenter", "tambang-fill", () => {
            map.getCanvas().style.cursor = "pointer";
        });
        map.on("mouseleave", "tambang-fill", () => {
            map.getCanvas().style.cursor = "";
        });
    } catch (err) {
        console.error("Error loading map layers:", err);
    }
}

let refreshSourceTimer = null;
let pendingSpatialRefreshToken = 0;

function refreshSpatialSources() {
    const sourceUrls = {
        hutan: buildApiUrl("/api/geojson/hutan", { includeBbox: true }),
        tambang: buildApiUrl("/api/geojson/tambang", { includeBbox: true }),
        overlap: buildApiUrl("/api/geojson/overlap", { includeBbox: true }),
    };

    Object.entries(sourceUrls).forEach(([sourceId, url]) => {
        const source = map.getSource(sourceId);
        if (source && lastSpatialSourceUrls[sourceId] !== url) {
            lastSpatialSourceUrls[sourceId] = url;
            source.setData(url);
        }
    });
}

function scheduleSpatialSourceRefresh(delay = 250) {
    clearTimeout(refreshSourceTimer);
    const refreshToken = ++pendingSpatialRefreshToken;

    refreshSourceTimer = setTimeout(() => {
        const runRefresh = () => {
            if (refreshToken !== pendingSpatialRefreshToken) {
                return;
            }

            refreshSpatialSources();
        };

        if (map.isMoving() || !map.loaded()) {
            map.once("idle", runRefresh);
            return;
        }

        runRefresh();
    }, delay);
}

map.on("moveend", () => {
    scheduleSpatialSourceRefresh(250);
});

function getForestClassStyle(deskripsi) {
    return FOREST_CLASS_STYLES[deskripsi] || DEFAULT_FOREST_STYLE;
}

function getNumberLocale() {
    return currentLocale === "en" ? "en-US" : "id-ID";
}

function formatNumber(value, options = {}) {
    const number = Number(value);

    if (!Number.isFinite(number)) {
        return "-";
    }

    return new Intl.NumberFormat(getNumberLocale(), options).format(number);
}

function formatAreaValue(value) {
    return formatNumber(value, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatDisplayDate(value) {
    if (!value) {
        return "-";
    }

    const normalized = typeof value === "string" ? value : String(value);
    const parsedDate = new Date(`${normalized}T00:00:00`);

    if (Number.isNaN(parsedDate.getTime())) {
        return normalized;
    }

    return new Intl.DateTimeFormat(getNumberLocale(), {
        day: "2-digit",
        month: "short",
        year: "numeric",
    }).format(parsedDate);
}

function hexToRgba(hex, alpha) {
    const normalized = hex.replace("#", "");
    const bigint = parseInt(normalized, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;

    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function formatHectares(value) {
    return `${formatAreaValue(value)} Ha`;
}

function decodeHtmlEntities(value) {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = value;
    return textarea.value;
}

function normalizeRichTextHtml(value) {
    if (typeof value !== "string") {
        return "";
    }

    const decoded = decodeHtmlEntities(value).trim();
    return decoded;
}

function hasRichTextContent(value) {
    const normalized = normalizeRichTextHtml(value);

    if (!normalized) {
        return false;
    }

    const textOnly = normalized
        .replace(/<br\s*\/?>/gi, " ")
        .replace(/<[^>]*>/g, " ")
        .replace(/&nbsp;/gi, " ")
        .trim();

    return textOnly.length > 0;
}

function setRichTextContent(elementId, value) {
    const element = document.getElementById(elementId);

    if (!element) {
        return;
    }

    if (!hasRichTextContent(value)) {
        element.innerHTML = "-";
        return;
    }

    element.innerHTML = normalizeRichTextHtml(value);
}

function aggregateOverlapData(overlaps) {
    const grouped = new Map();

    overlaps.forEach((overlap) => {
        const key = overlap.kawasan_nama || "Lainnya";
        const style = getForestClassStyle(key);
        const luas = Number(overlap.luas_ha || 0);

        if (!grouped.has(key)) {
            const displayName =
                currentLocale === "en" && style.labelEn ? style.labelEn : key;
            grouped.set(key, {
                name: displayName,
                code: style.code,
                color: style.fill,
                total: 0,
            });
        }

        grouped.get(key).total += luas;
    });

    return Array.from(grouped.values()).sort((a, b) => b.total - a.total);
}

function polarToCartesian(cx, cy, r, angleInDegrees) {
    const angleInRadians = ((angleInDegrees - 90) * Math.PI) / 180.0;

    return {
        x: cx + r * Math.cos(angleInRadians),
        y: cy + r * Math.sin(angleInRadians),
    };
}

function describeArc(cx, cy, r, startAngle, endAngle) {
    const start = polarToCartesian(cx, cy, r, endAngle);
    const end = polarToCartesian(cx, cy, r, startAngle);
    const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";

    return [
        "M",
        start.x,
        start.y,
        "A",
        r,
        r,
        0,
        largeArcFlag,
        0,
        end.x,
        end.y,
    ].join(" ");
}

function renderOverlapChart(overlaps) {
    const chartEl = document.getElementById("info-overlap-chart");
    const legendEl = document.getElementById("info-overlap-chart-legend");
    const totalEl = document.getElementById("info-overlap-total");
    const isLightTheme = currentTheme === "light";
    const chartTrackColor = isLightTheme
        ? "rgba(148, 163, 184, 0.34)"
        : "rgba(71, 85, 105, 0.25)";
    const chartCenterFill = isLightTheme
        ? "rgba(255, 255, 255, 0.96)"
        : "rgba(15, 23, 42, 0.95)";
    const chartCenterTextColor = isLightTheme ? "#0f172a" : "#ffffff";
    const chartCenterSubtextColor = isLightTheme ? "#64748b" : "#94a3b8";
    const emptyShellClass = isLightTheme ? "overlap-chart-empty-shell" : "";
    const emptyTextClass = isLightTheme ? "overlap-chart-empty-text" : "";

    const aggregated = aggregateOverlapData(overlaps);
    const total = aggregated.reduce((sum, item) => sum + item.total, 0);
    totalEl.textContent = formatHectares(total);

    if (!aggregated.length || total <= 0) {
        chartEl.innerHTML = `
            <div class="text-center">
                <div class="overlap-chart-empty-shell w-36 h-36 rounded-full border border-gray-700/40 bg-gray-900/50 flex items-center justify-center mx-auto ${emptyShellClass}">
                    <span class="overlap-chart-empty-text text-sm text-gray-500 ${emptyTextClass}">${t("chart_empty")}</span>
                </div>
            </div>
        `;
        legendEl.innerHTML = `<p class="overlap-chart-empty-text text-sm text-gray-500 ${emptyTextClass}">${t("chart_empty_description")}</p>`;
        return;
    }

    let currentAngle = 0;
    const radius = 68;
    const center = 80;

    const slices = aggregated.map((item) => {
        const percentage = (item.total / total) * 100;
        const angle = (item.total / total) * 360;
        const startAngle = currentAngle;
        const endAngle = currentAngle + angle;
        currentAngle = endAngle;

        return {
            ...item,
            percentage,
            path: describeArc(center, center, radius, startAngle, endAngle),
        };
    });

    chartEl.innerHTML = `
        <svg viewBox="0 0 160 160" class="w-44 h-44 drop-shadow-lg">
            <circle cx="${center}" cy="${center}" r="${radius}" fill="none" stroke="${chartTrackColor}" stroke-width="20"></circle>
            ${slices
                .map(
                    (slice) => `
                <path d="${slice.path}" fill="none" stroke="${slice.color}" stroke-width="20" stroke-linecap="butt"></path>
            `,
                )
                .join("")}
            <circle cx="${center}" cy="${center}" r="42" fill="${chartCenterFill}"></circle>
            <text x="${center}" y="${center - 4}" text-anchor="middle" fill="${chartCenterTextColor}" style="font-size:18px;font-weight:700;">
                ${aggregated.length}
            </text>
            <text x="${center}" y="${center + 16}" text-anchor="middle" fill="${chartCenterSubtextColor}" style="font-size:10px;letter-spacing:0.12em;text-transform:uppercase;">
                ${t("chart_classes")}
            </text>
        </svg>
    `;

    legendEl.innerHTML = slices
        .map(
            (slice) => `
        <div class="overlap-chart-legend-item flex items-center justify-between gap-3 rounded-xl border border-gray-700/30 bg-gray-900/35 px-3 py-2">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-3 h-3 rounded-full shrink-0" style="background:${slice.color};"></span>
                <div class="min-w-0">
                    <p class="overlap-chart-legend-name text-sm text-gray-200 truncate">${slice.name}</p>
                    <p class="text-[11px] uppercase tracking-[0.18em]" style="color:${slice.color};">${slice.code}</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="overlap-chart-legend-value text-sm font-semibold text-white">${slice.percentage.toFixed(1)}%</p>
                <p class="overlap-chart-legend-subtext text-xs text-gray-500">${formatHectares(slice.total)}</p>
            </div>
        </div>
    `,
        )
        .join("");
}

function renderDocumentationGallery(images) {
    const sectionEl = document.getElementById("info-gallery-section");
    const gridEl = document.getElementById("info-gallery-grid");
    const counterEl = document.getElementById("info-gallery-counter");
    const dotsEl = document.getElementById("info-gallery-dots");
    const prevButton = document.getElementById("info-gallery-prev");
    const nextButton = document.getElementById("info-gallery-next");

    if (!Array.isArray(images) || images.length === 0) {
        sectionEl.classList.add("hidden");
        gridEl.innerHTML = "";
        counterEl.textContent = "-";
        dotsEl.innerHTML = "";
        prevButton.disabled = true;
        nextButton.disabled = true;
        galleryImages = [];
        activeInfoGalleryIndex = 0;
        return;
    }

    galleryImages = images;
    activeInfoGalleryIndex = 0;
    sectionEl.classList.remove("hidden");
    gridEl.innerHTML = images
        .map(
            (url, index) => `
        <div class="gallery-slide">
            <button type="button"
                class="gallery-trigger group relative aspect-4/3 w-full overflow-hidden rounded-2xl border border-gray-700/50 bg-gray-800/40"
                data-gallery-index="${index}">
                <img src="${url}" alt="${t("gallery_photo", { index: index + 1 })}"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105 group-hover:opacity-90">
                <div class="absolute inset-0 bg-linear-to-t from-gray-950/70 via-transparent to-transparent"></div>
                <div class="absolute bottom-2 left-2 rounded-md bg-gray-950/75 px-2 py-1 text-[11px] font-medium text-white">
                    ${t("gallery_photo", { index: index + 1 })}
                </div>
            </button>
        </div>
    `,
        )
        .join("");

    gridEl.querySelectorAll(".gallery-trigger").forEach((button) => {
        button.addEventListener("click", () => {
            openGalleryModal(Number(button.dataset.galleryIndex || 0));
        });
    });

    dotsEl.innerHTML = images
        .map(
            (_, index) => `
        <button
            type="button"
            class="gallery-slider-dot ${index === activeInfoGalleryIndex ? "is-active" : ""}"
            data-gallery-dot-index="${index}"
            aria-label="${t("gallery_photo", { index: index + 1 })}">
        </button>
    `,
        )
        .join("");

    dotsEl.querySelectorAll("[data-gallery-dot-index]").forEach((button) => {
        button.addEventListener("click", () => {
            activeInfoGalleryIndex = Number(
                button.dataset.galleryDotIndex || 0,
            );
            updateInfoGallerySlider();
        });
    });

    updateInfoGallerySlider();
}

function updateInfoGallerySlider() {
    const trackEl = document.getElementById("info-gallery-grid");
    const counterEl = document.getElementById("info-gallery-counter");
    const dotsEl = document.getElementById("info-gallery-dots");
    const prevButton = document.getElementById("info-gallery-prev");
    const nextButton = document.getElementById("info-gallery-next");

    if (!trackEl || !counterEl || !dotsEl || !prevButton || !nextButton) {
        return;
    }

    if (!galleryImages.length) {
        trackEl.style.transform = "translateX(0)";
        counterEl.textContent = "-";
        dotsEl.innerHTML = "";
        prevButton.disabled = true;
        nextButton.disabled = true;
        return;
    }

    activeInfoGalleryIndex =
        (activeInfoGalleryIndex + galleryImages.length) % galleryImages.length;

    trackEl.style.transform = `translateX(-${activeInfoGalleryIndex * 100}%)`;
    counterEl.textContent = t("gallery_counter", {
        current: activeInfoGalleryIndex + 1,
        total: galleryImages.length,
    });
    dotsEl.querySelectorAll("[data-gallery-dot-index]").forEach((button) => {
        button.classList.toggle(
            "is-active",
            Number(button.dataset.galleryDotIndex || 0) ===
                activeInfoGalleryIndex,
        );
    });
    prevButton.disabled = galleryImages.length <= 1;
    nextButton.disabled = galleryImages.length <= 1;
}

function stepInfoGallery(direction) {
    if (!galleryImages.length) {
        return;
    }

    activeInfoGalleryIndex =
        (activeInfoGalleryIndex + direction + galleryImages.length) %
        galleryImages.length;
    updateInfoGallerySlider();
}

function handleInfoGalleryTouchStart(event) {
    const touch = event.touches?.[0];

    if (!touch || galleryImages.length <= 1) {
        return;
    }

    infoGalleryTouchStartX = touch.clientX;
    infoGalleryTouchStartY = touch.clientY;
}

function handleInfoGalleryTouchEnd(event) {
    const touch = event.changedTouches?.[0];

    if (
        !touch ||
        infoGalleryTouchStartX === null ||
        infoGalleryTouchStartY === null ||
        galleryImages.length <= 1
    ) {
        infoGalleryTouchStartX = null;
        infoGalleryTouchStartY = null;
        return;
    }

    const deltaX = touch.clientX - infoGalleryTouchStartX;
    const deltaY = touch.clientY - infoGalleryTouchStartY;
    const absDeltaX = Math.abs(deltaX);
    const absDeltaY = Math.abs(deltaY);

    infoGalleryTouchStartX = null;
    infoGalleryTouchStartY = null;

    if (absDeltaX < 40 || absDeltaX <= absDeltaY) {
        return;
    }

    stepInfoGallery(deltaX < 0 ? 1 : -1);
}

function updateGalleryModal() {
    if (!galleryImages.length) {
        return;
    }

    const imageEl = document.getElementById("gallery-image");
    const counterEl = document.getElementById("gallery-counter");

    imageEl.src = galleryImages[activeGalleryIndex];
    counterEl.textContent = t("gallery_counter", {
        current: activeGalleryIndex + 1,
        total: galleryImages.length,
    });
}

function openGalleryModal(index = 0) {
    if (!galleryImages.length) {
        return;
    }

    activeGalleryIndex = index;
    updateGalleryModal();
    document.getElementById("gallery-modal").classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
}

function closeGalleryModal() {
    document.getElementById("gallery-modal").classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
}

function stepGallery(direction) {
    if (!galleryImages.length) {
        return;
    }

    activeGalleryIndex =
        (activeGalleryIndex + direction + galleryImages.length) %
        galleryImages.length;
    updateGalleryModal();
}

function setTambangBasicInfo(props) {
    document.getElementById("info-nama").textContent = props.nama || "-";
    document.getElementById("info-jenis").textContent =
        getLocalizedCommodityLabel(props);
    document.getElementById("info-sk").textContent = props.nomor_sk || "-";
    document.getElementById("info-luas-sk").innerHTML =
        `${props.luas_sk_ha !== null && props.luas_sk_ha !== undefined && props.luas_sk_ha !== "" ? formatAreaValue(props.luas_sk_ha) : "-"}<span class="text-xs font-normal text-gray-500 ml-1">Ha</span>`;
    document.getElementById("info-luas-overlap").innerHTML =
        `${formatAreaValue(props.luas_overlap || 0)}<span class="text-xs font-normal text-red-400/60 ml-1">Ha</span>`;
    document.getElementById("info-tanggal-berlaku").textContent =
        formatDisplayDate(props.tanggal_berlaku);
    document.getElementById("info-tanggal-berakhir").textContent =
        formatDisplayDate(props.tanggal_berakhir);
    document.getElementById("info-kegiatan").textContent =
        getLocalizedActivityLabel(props);
    document.getElementById("info-jenis-izin").textContent =
        props.jenis_izin || "-";
    document.getElementById("info-nama-provinsi").textContent =
        props.nama_provinsi || "-";
    document.getElementById("info-nama-kabupaten").textContent =
        props.nama_kabupaten || "-";
    document.getElementById("info-lokasi").textContent = props.lokasi || "-";

    const statusEl = document.getElementById("info-status");
    const status = props.status || "aktif";
    statusEl.textContent =
        status === "aktif"
            ? t("status_active")
            : status === "nonaktif"
              ? t("status_inactive")
              : status.charAt(0).toUpperCase() + status.slice(1);
    statusEl.className =
        status === "aktif"
            ? "text-sm font-medium text-emerald-400"
            : "text-sm font-medium text-amber-400";
}

function summarizeTambangDescription(props, data) {
    const detail = data?.detail;
    const localizedProfile = getLocalizedRichText(detail, "profil_singkat");
    const cleanProfile = normalizeRichTextHtml(localizedProfile)
        .replace(/<br\s*\/?>/gi, " ")
        .replace(/<[^>]*>/g, " ")
        .replace(/&nbsp;/gi, " ")
        .replace(/\s+/g, " ")
        .trim();

    if (cleanProfile) {
        return cleanProfile.length > 160
            ? `${cleanProfile.slice(0, 157)}...`
            : cleanProfile;
    }

    return `${props.nama || "Wilayah tambang"} pada dashboard Indonesia Mining Watch.`;
}

function getLocalizedRichText(detail, fieldName) {
    const primaryField = currentLocale === "en" ? `${fieldName}_en` : fieldName;
    const fallbackField =
        currentLocale === "en" ? fieldName : `${fieldName}_en`;

    if (hasRichTextContent(detail?.[primaryField])) {
        return detail[primaryField];
    }

    return detail?.[fallbackField] || "";
}

function renderTambangDetail(props, data) {
    const mergedProps = { ...props, ...(data?.tambang || {}) };
    activeTambangState = { props: mergedProps, data };
    setTambangBasicInfo(mergedProps);
    const previousVisibleCount = visibleTambangCount;
    ensureVisibleTambangBatchForPublicUid(
        mergedProps.public_uid || null,
        filteredSidebarTambangItems,
    );
    if (visibleTambangCount !== previousVisibleCount) {
        renderSidebarTambangList();
    } else {
        markActiveTambangItem(mergedProps.public_uid || null);
    }
    updateDashboardUrl(mergedProps.public_uid || null);
    updateDashboardMetadata({
        title: mergedProps.nama
            ? `Indonesia Mining Watch - ${mergedProps.nama}`
            : defaultDashboardTitle,
        description: summarizeTambangDescription(mergedProps, data),
        canonicalUrl: mergedProps.public_uid
            ? buildSharedTambangUrl(mergedProps.public_uid)
            : dashboardUrl,
    });
    document.getElementById("info-company-section").classList.add("hidden");
    document.getElementById("info-share-section").classList.add("hidden");
    document.getElementById("info-overlap-section").classList.add("hidden");
    document.getElementById("info-impact-section").classList.add("hidden");
    document
        .querySelectorAll('#info-impact-section [id$="-card"]')
        .forEach((el) => el.classList.add("hidden"));
    setRichTextContent("info-profil-singkat", "");
    setRichTextContent("info-dampak-lingkungan", "");
    setRichTextContent("info-dampak-sosial", "");
    setRichTextContent("info-dampak-ekonomi", "");

    if (data.detail) {
        const localizedProfile = getLocalizedRichText(
            data.detail,
            "profil_singkat",
        );
        const localizedEnvironmental = getLocalizedRichText(
            data.detail,
            "dampak_lingkungan",
        );
        const localizedSocial = getLocalizedRichText(
            data.detail,
            "dampak_sosial",
        );
        const localizedEconomic = getLocalizedRichText(
            data.detail,
            "dampak_ekonomi",
        );

        document
            .getElementById("info-company-section")
            .classList.remove("hidden");
        document.getElementById("info-perusahaan").textContent =
            data.detail.nama_perusahaan || "-";
        setRichTextContent("info-profil-singkat", localizedProfile);
        renderDocumentationGallery(data.detail.dokumentasi_urls || []);

        const hasImpact =
            hasRichTextContent(localizedEnvironmental) ||
            hasRichTextContent(localizedSocial) ||
            hasRichTextContent(localizedEconomic);
        if (hasImpact) {
            document
                .getElementById("info-impact-section")
                .classList.remove("hidden");

            if (hasRichTextContent(localizedEnvironmental)) {
                document
                    .getElementById("dampak-lingkungan-card")
                    .classList.remove("hidden");
                setRichTextContent(
                    "info-dampak-lingkungan",
                    localizedEnvironmental,
                );
            }
            if (hasRichTextContent(localizedSocial)) {
                document
                    .getElementById("dampak-sosial-card")
                    .classList.remove("hidden");
                setRichTextContent("info-dampak-sosial", localizedSocial);
            }
            if (hasRichTextContent(localizedEconomic)) {
                document
                    .getElementById("dampak-ekonomi-card")
                    .classList.remove("hidden");
                setRichTextContent("info-dampak-ekonomi", localizedEconomic);
            }
        }
    } else {
        document.getElementById("info-company-section").classList.add("hidden");
        document.getElementById("info-impact-section").classList.add("hidden");
        setRichTextContent("info-profil-singkat", "");
        renderDocumentationGallery([]);
    }

    if (data.public_url) {
        document
            .getElementById("info-share-section")
            .classList.remove("hidden");
        document.getElementById("share-whatsapp").href =
            data.share_links?.whatsapp || "#";
        document.getElementById("share-email").href =
            data.share_links?.email || "#";
        document.getElementById("share-telegram").href =
            data.share_links?.telegram || "#";
        document.getElementById("share-x").href = data.share_links?.x || "#";
        document.getElementById("share-facebook").href =
            data.share_links?.facebook || "#";
    }

    if (data.overlaps && data.overlaps.length > 0) {
        document
            .getElementById("info-overlap-section")
            .classList.remove("hidden");
        renderOverlapChart(data.overlaps);
    } else {
        document.getElementById("info-overlap-section").classList.add("hidden");
    }
}

async function showTambangDetail(props) {
    const tambangId = props.id;

    setTambangBasicInfo(props);
    openInfoPanel();

    try {
        const res = await fetch(`/api/tambang/${tambangId}/detail`);
        const data = await res.json();
        renderTambangDetail(props, data);
    } catch (err) {
        console.error("Error fetching tambang detail:", err);
    }
}

// ============================================================
// CLICK HANDLER — Show Info Panel
// ============================================================
map.on("click", "tambang-fill", async (e) => {
    if (!e.features || e.features.length === 0) return;

    await showTambangDetail(e.features[0].properties);
});

// ============================================================
// INFO PANEL CONTROLS
// ============================================================
function openInfoPanel() {
    const panel = document.getElementById("info-panel");
    panel.classList.remove("translate-x-full");
    panel.classList.add("translate-x-0");

    // Reset hidden sections
    document.getElementById("info-company-section").classList.add("hidden");
    document.getElementById("info-share-section").classList.add("hidden");
    document.getElementById("info-gallery-section").classList.add("hidden");
    document.getElementById("info-overlap-section").classList.add("hidden");
    document.getElementById("info-impact-section").classList.add("hidden");
    document
        .querySelectorAll('#info-impact-section [id$="-card"]')
        .forEach((el) => el.classList.add("hidden"));
    setRichTextContent("info-profil-singkat", "");
    setRichTextContent("info-dampak-lingkungan", "");
    setRichTextContent("info-dampak-sosial", "");
    setRichTextContent("info-dampak-ekonomi", "");
}

function closeInfoPanel({ resetView = true } = {}) {
    const panel = document.getElementById("info-panel");
    panel.classList.add("translate-x-full");
    panel.classList.remove("translate-x-0");
    activeTambangState = null;
    markActiveTambangItem(null);
    updateDashboardUrl();
    updateDashboardMetadata({
        title: defaultDashboardTitle,
        description: defaultDashboardDescription,
        canonicalUrl: dashboardUrl,
    });

    if (resetView) {
        flyToDefaultMapView();
    }
}

document
    .getElementById("close-info-panel")
    .addEventListener("click", closeInfoPanel);
document
    .getElementById("gallery-close")
    .addEventListener("click", closeGalleryModal);
document
    .getElementById("gallery-prev")
    .addEventListener("click", () => stepGallery(-1));
document
    .getElementById("gallery-next")
    .addEventListener("click", () => stepGallery(1));
document
    .getElementById("info-gallery-prev")
    .addEventListener("click", () => stepInfoGallery(-1));
document
    .getElementById("info-gallery-next")
    .addEventListener("click", () => stepInfoGallery(1));
document
    .getElementById("info-gallery-viewport")
    .addEventListener("touchstart", handleInfoGalleryTouchStart, {
        passive: true,
    });
document
    .getElementById("info-gallery-viewport")
    .addEventListener("touchend", handleInfoGalleryTouchEnd, {
        passive: true,
    });

document.getElementById("gallery-modal").addEventListener("click", (event) => {
    if (event.target.id === "gallery-modal") {
        closeGalleryModal();
    }
});

document.addEventListener("keydown", (event) => {
    const modalEl = document.getElementById("gallery-modal");
    const isOpen = !modalEl.classList.contains("hidden");

    if (!isOpen) {
        return;
    }

    if (event.key === "Escape") {
        closeGalleryModal();
    }

    if (event.key === "ArrowLeft") {
        stepGallery(-1);
    }

    if (event.key === "ArrowRight") {
        stepGallery(1);
    }
});

document.getElementById("lang-switch-id").addEventListener("click", () => {
    persistDashboardLocale("id");
    applyDashboardTranslations();
    loadTambangList();
});

document.getElementById("lang-switch-en").addEventListener("click", () => {
    persistDashboardLocale("en");
    applyDashboardTranslations();
    loadTambangList();
});

const themeMenuButton = document.getElementById("theme-menu-button");
const themeMenu = document.getElementById("theme-menu");
const themeMenuChevron = document.getElementById("theme-menu-chevron");

function isThemeMenuOpen() {
    return themeMenu?.dataset.open === "true";
}

function positionThemeMenu() {
    if (!themeMenu || !themeMenuButton) {
        return;
    }

    const buttonRect = themeMenuButton.getBoundingClientRect();
    const menuWidth = buttonRect.width;
    const viewportPadding = 16;
    const right = Math.max(
        viewportPadding,
        window.innerWidth - buttonRect.right,
    );

    themeMenu.style.width = `${menuWidth}px`;
    themeMenu.style.top = `${buttonRect.bottom + 8}px`;
    themeMenu.style.right = `${right}px`;
    themeMenu.style.left = "auto";
}

function syncThemeMenuVisualState() {
    const open = isThemeMenuOpen();

    if (themeMenuChevron) {
        themeMenuChevron.classList.toggle("rotate-180", open);
    }
}

syncThemeMenuVisualState();

function closeThemeMenu() {
    if (!themeMenu) {
        return;
    }

    themeMenu.dataset.open = "false";
    syncThemeMenuVisualState();
}

function toggleThemeMenu() {
    if (!themeMenu) {
        return;
    }

    positionThemeMenu();
    themeMenu.dataset.open = isThemeMenuOpen() ? "false" : "true";
    syncThemeMenuVisualState();
}

themeMenuButton?.addEventListener("click", (event) => {
    event.stopPropagation();
    toggleThemeMenu();
});

document.getElementById("theme-option-dark")?.addEventListener("click", () => {
    persistDashboardTheme("dark");
    applyDashboardTheme();
    setBasemap("dark");
    closeThemeMenu();
});

document.getElementById("theme-option-light")?.addEventListener("click", () => {
    persistDashboardTheme("light");
    applyDashboardTheme();
    setBasemap("streets");
    closeThemeMenu();
});

document.addEventListener("click", (event) => {
    if (!themeMenu || !isThemeMenuOpen()) {
        return;
    }

    if (
        !themeMenu.contains(event.target) &&
        !themeMenuButton?.contains(event.target)
    ) {
        closeThemeMenu();
    }
});

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && isThemeMenuOpen()) {
        closeThemeMenu();
    }
});

window.addEventListener("resize", () => {
    if (isThemeMenuOpen()) {
        positionThemeMenu();
    }
});

window.addEventListener(
    "scroll",
    () => {
        if (isThemeMenuOpen()) {
            positionThemeMenu();
        }
    },
    true,
);

// ============================================================
// SIDEBAR TOGGLE
// ============================================================
document.getElementById("toggle-sidebar").addEventListener("click", () => {
    const sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("-translate-x-full");
    sidebar.classList.toggle("absolute");
    sidebar.classList.toggle("h-full");
});

// ============================================================
// LAYER TOGGLE (Legend checkboxes)
// ============================================================
const layerMapping = {
    "layer-hutan": ["hutan-fill", "hutan-outline"],
    "layer-tambang": ["tambang-fill", "tambang-outline"],
    "layer-overlap": ["overlap-fill", "overlap-outline"],
};

Object.entries(layerMapping).forEach(([checkboxId, layerIds]) => {
    document.getElementById(checkboxId).addEventListener("change", (e) => {
        const visibility = e.target.checked ? "visible" : "none";
        layerIds.forEach((layerId) => {
            if (map.getLayer(layerId)) {
                map.setLayoutProperty(layerId, "visibility", visibility);
            }
        });
    });
});

// ============================================================
// BASEMAP SWITCHER
// ============================================================
document.querySelectorAll(".basemap-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
        const basemapId = btn.id.replace("btn-basemap-", "");
        setBasemap(basemapId);
    });
});

// ============================================================
// SIDEBAR: Load Statistics
// ============================================================
async function loadStatistics() {
    try {
        const res = await fetch(buildApiUrl("/api/statistik"));
        const data = await res.json();

        document.getElementById("stat-total-tambang").textContent =
            formatNumber(data.total_tambang || 0);
        document.getElementById("stat-tambang-overlap").textContent =
            formatNumber(data.tambang_overlap || 0);
        document.getElementById("stat-luas-tambang").innerHTML =
            `${formatAreaValue(data.total_luas_tambang_ha || 0)}<span class="text-xs font-normal text-gray-500 ml-1">Ha</span>`;
        document.getElementById("stat-luas-overlap").innerHTML =
            `${formatAreaValue(data.total_luas_overlap_ha || 0)}<span class="text-xs font-normal text-gray-500 ml-1">Ha</span>`;
    } catch (err) {
        console.error("Error loading statistics:", err);
    }
}

// ============================================================
// SIDEBAR: Load Tambang List
// ============================================================
async function loadTambangList() {
    try {
        document.getElementById("tambang-list").innerHTML = `
            <div class="text-sm text-gray-500 text-center py-8">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                ${t("list_loading")}
            </div>`;

        const res = await fetch(buildApiUrl("/api/tambang-list"));
        const payload = await res.json();
        const items = Array.isArray(payload?.data) ? payload.data : [];
        tambangListItemsByPublicUid = new Map();
        sidebarTambangItems = [];
        filteredSidebarTambangItems = [];
        visibleTambangCount = 10;

        if (items.length === 0) {
            document.getElementById("tambang-list").innerHTML = `
                <div class="text-sm text-gray-500 text-center py-8">
                    <p>${t("list_empty_title")}</p>
                    <p class="text-xs mt-1 text-gray-600">${t("list_empty_subtitle")}</p>
                </div>`;
            document
                .getElementById("tambang-list-load-more-wrap")
                ?.classList.add("hidden");
            return;
        }

        items.forEach((item) => {
            const publicUid = item.public_uid;

            if (publicUid) {
                tambangListItemsByPublicUid.set(publicUid, item);
            }
        });

        sidebarTambangItems = items;
        renderSidebarTambangList();

        if (
            pendingSharedTambangUid &&
            tambangListItemsByPublicUid.has(pendingSharedTambangUid)
        ) {
            const item = tambangListItemsByPublicUid.get(
                pendingSharedTambangUid,
            );

            map.flyTo({
                center: [Number(item.lng), Number(item.lat)],
                zoom: 12,
                duration: 1500,
            });
            await showTambangDetail(item);
            pendingSharedTambangUid = "";
        } else if (activeTambangState?.props?.public_uid) {
            markActiveTambangItem(activeTambangState.props.public_uid);
        }
    } catch (err) {
        console.error("Error loading tambang list:", err);
        document.getElementById("tambang-list").innerHTML =
            `<div class="text-sm text-red-400 text-center py-4">${t("list_failed")}</div>`;
        document
            .getElementById("tambang-list-load-more-wrap")
            ?.classList.add("hidden");
    }
}

// ============================================================
// SIDEBAR: Search Filter
// ============================================================
document.getElementById("search-input").addEventListener("input", () => {
    clearTimeout(sidebarSearchTimer);
    sidebarSearchTimer = setTimeout(() => {
        resetDashboardMapFocus();
        applyTambangSearchFilter();
    }, 250);
});

document
    .getElementById("tambang-list-load-more")
    ?.addEventListener("click", () => {
        visibleTambangCount += 10;
        renderSidebarTambangList();
    });

document
    .getElementById("filter-provinsi")
    .addEventListener("change", applyDashboardDataFilters);
document
    .getElementById("filter-jenis-tambang")
    .addEventListener("change", applyDashboardDataFilters);

// ============================================================
// HELPERS
// ============================================================

/**
 * Get rough centroid of a GeoJSON feature for flyTo
 */
function getCentroid(feature) {
    try {
        const coords = feature.geometry.coordinates;
        let totalLng = 0,
            totalLat = 0,
            count = 0;

        function flatten(arr) {
            if (typeof arr[0] === "number") {
                totalLng += arr[0];
                totalLat += arr[1];
                count++;
            } else {
                arr.forEach(flatten);
            }
        }

        flatten(coords);
        return [totalLng / count, totalLat / count];
    } catch {
        return [117.0, -1.5];
    }
}
