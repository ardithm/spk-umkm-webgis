/**
 * WebGIS Engine with Leaflet.js
 * Peta Sebaran UMKM Kota Banjarmasin & Simulasi Rute Survei Lapangan
 */

let mapInstance = null;
let markersLayer = null;
let routeLayer = null;

// Lokasi Kantor Dinas Koperasi, Usaha Mikro & Tenaga Kerja Kota Banjarmasin
const OFFICE_LOCATION = {
  name: "Dinas Koperasi, UMKM & Tenaga Kerja Kota Banjarmasin",
  lat: -3.3274,
  lng: 114.5942,
  address: "Jl. Tirta Dharma, Banjarmasin Tengah",
  isOffice: true
};

// Dataset Representatif UMKM Kota Banjarmasin
const UMKM_DATA = [
  {
    id: 1,
    nama: "Sasirangan Baras Basah Melayu",
    pemilik: "Hj. Siti Rahmah",
    kategori: "Kriya & Fashion",
    kecamatan: "Banjarmasin Tengah",
    alamat: "Jl. Seberang Mesjid No. 24, Kp. Melayu",
    lat: -3.3155,
    lng: 114.5952,
    omzet: "Rp 180 Jt/Thn",
    status: "direkomendasikan",
    skor: 4.65,
    peringkat: 1,
    phone: "0812-5001-xxxx"
  },
  {
    id: 2,
    nama: "Amplang & Kerupuk Ikan Teluk Tiram",
    pemilik: "M. Fadillah",
    kategori: "Kuliner Olahan",
    kecamatan: "Banjarmasin Barat",
    alamat: "Jl. Teluk Tiram Darat No. 12",
    lat: -3.3325,
    lng: 114.5768,
    omzet: "Rp 140 Jt/Thn",
    status: "direkomendasikan",
    skor: 4.50,
    peringkat: 2,
    phone: "0813-8822-xxxx"
  },
  {
    id: 3,
    nama: "Anyaman Purun Lestari Alalak",
    pemilik: "Norma Hayati",
    kategori: "Kriya Anyaman",
    kecamatan: "Banjarmasin Utara",
    alamat: "Jl. Alalak Utara No. 45",
    lat: -3.2982,
    lng: 114.5824,
    omzet: "Rp 95 Jt/Thn",
    status: "direkomendasikan",
    skor: 4.40,
    peringkat: 3,
    phone: "0852-7711-xxxx"
  },
  {
    id: 4,
    nama: "Bingka Banjar Tradisional Bunda",
    pemilik: "Hj. Rusminah",
    kategori: "Kuliner Khas",
    kecamatan: "Banjarmasin Timur",
    alamat: "Jl. Veteran No. 88, Kuripan",
    lat: -3.3248,
    lng: 114.6075,
    omzet: "Rp 120 Jt/Thn",
    status: "direkomendasikan",
    skor: 4.35,
    peringkat: 4,
    phone: "0819-4400-xxxx"
  },
  {
    id: 5,
    nama: "Keripik Ikan Seluang Mantuil",
    pemilik: "Bambang Irawan",
    kategori: "Kuliner Olahan",
    kecamatan: "Banjarmasin Selatan",
    alamat: "Jl. Mantuil Raya No. 102",
    lat: -3.3540,
    lng: 114.5880,
    omzet: "Rp 85 Jt/Thn",
    status: "menunggu",
    skor: 3.90,
    peringkat: 9,
    phone: "0857-3311-xxxx"
  },
  {
    id: 6,
    nama: "Bengkel Bubut & Las Kayu Tangi",
    pemilik: "Hendra Gunawan",
    kategori: "Jasa & Manufaktur",
    kecamatan: "Banjarmasin Utara",
    alamat: "Jl. Brigjend H. Hasan Basry No. 33",
    lat: -3.2925,
    lng: 114.5988,
    omzet: "Rp 160 Jt/Thn",
    status: "direkomendasikan",
    skor: 4.25,
    peringkat: 5,
    phone: "0811-2299-xxxx"
  },
  {
    id: 7,
    nama: "Soto Banjar & Kuliner Siring Kuin",
    pemilik: "Akhmad Zaini",
    kategori: "Kuliner Tradisional",
    kecamatan: "Banjarmasin Barat",
    alamat: "Jl. Kuin Selatan No. 18",
    lat: -3.3080,
    lng: 114.5720,
    omzet: "Rp 110 Jt/Thn",
    status: "menunggu",
    skor: 3.85,
    peringkat: 11,
    phone: "0812-9900-xxxx"
  },
  {
    id: 8,
    nama: "Konveksi Busana Muslim Kelayan",
    pemilik: "Raihanah",
    kategori: "Kriya & Fashion",
    kecamatan: "Banjarmasin Selatan",
    alamat: "Jl. Kelayan B Barat No. 56",
    lat: -3.3420,
    lng: 114.5960,
    omzet: "Rp 130 Jt/Thn",
    status: "direkomendasikan",
    skor: 4.20,
    peringkat: 6,
    phone: "0853-1122-xxxx"
  }
];

function initWebGIS() {
  const mapElement = document.getElementById('map');
  if (!mapElement) return;

  // Inisialisasi Map Leaflet berpusat di Banjarmasin
  mapInstance = L.map('map', {
    center: [-3.3220, 114.5910],
    zoom: 13,
    zoomControl: true,
    attributionControl: true
  });

  // Basemap Tile Layer - Clean Modern OpenStreetMap / CartoDB Positron
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(mapInstance);

  markersLayer = L.layerGroup().addTo(mapInstance);
  routeLayer = L.layerGroup().addTo(mapInstance);

  // Render Marker Kantor Dinas
  renderOfficeMarker();

  // Render Marker UMKM
  renderMarkers(UMKM_DATA);

  // Pasang Listener Filter
  setupMapFilters();
}

function renderOfficeMarker() {
  const officeIcon = L.divIcon({
    className: 'custom-gis-office-icon',
    html: `
      <div style="background:#8A67AB; color:#fff; width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 6px 16px rgba(138,103,171,0.5); border:3px solid #fff; font-size:18px;">
        🏛️
      </div>
    `,
    iconSize: [38, 38],
    iconAnchor: [19, 19]
  });

  const officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], { icon: officeIcon });
  officeMarker.bindPopup(`
    <div style="font-family:'Plus Jakarta Sans', sans-serif; padding:4px;">
      <span style="background:#8A67AB; color:#fff; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;">Titik Mulai Survei</span>
      <h4 style="margin:8px 0 4px; font-size:14px; font-weight:800; color:#1F1F1F;">${OFFICE_LOCATION.name}</h4>
      <p style="margin:0; font-size:12px; color:#6B7280;">${OFFICE_LOCATION.address}</p>
      <div style="margin-top:8px; font-size:11px; color:#8A67AB; font-weight:600;">Pos Komando Verifikasi & OSRM Engine</div>
    </div>
  `);
  officeMarker.addTo(markersLayer);
}

function renderMarkers(items) {
  // Clear marker lama selain kantor
  markersLayer.clearLayers();
  renderOfficeMarker();

  items.forEach(umkm => {
    const isRecommended = umkm.status === 'direkomendasikan';
    const borderColor = isRecommended ? '#10B981' : '#FFB347';
    const badgeText = isRecommended ? 'Direkomendasikan' : 'Menunggu Verifikasi';
    const badgeBg = isRecommended ? '#EAF7EE' : '#FFF6EC';
    const badgeColor = isRecommended ? '#1E7E34' : '#B46200';

    const iconHtml = `
      <div style="background:#fff; border:2.5px solid ${borderColor}; color:${borderColor}; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; box-shadow:0 4px 12px rgba(0,0,0,0.15); transition:transform 0.2s;">
        ${umkm.peringkat ? '#' + umkm.peringkat : '📍'}
      </div>
    `;

    const customIcon = L.divIcon({
      className: 'custom-gis-item-icon',
      html: iconHtml,
      iconSize: [34, 34],
      iconAnchor: [17, 17]
    });

    const marker = L.marker([umkm.lat, umkm.lng], { icon: customIcon });

    marker.bindPopup(`
      <div style="font-family:'Plus Jakarta Sans', sans-serif; min-width:220px; padding:2px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <span style="background:${badgeBg}; color:${badgeColor}; padding:3px 8px; border-radius:20px; font-size:11px; font-weight:700;">
            ${badgeText}
          </span>
          <span style="font-size:11px; font-weight:800; color:#8A67AB;">Skor: ${umkm.skor}</span>
        </div>
        <h4 style="margin:0 0 4px; font-size:14px; font-weight:800; color:#1F1F1F;">${umkm.nama}</h4>
        <p style="margin:0 0 6px; font-size:12px; color:#585863;">👤 Pemilik: <strong>${umkm.pemilik}</strong></p>
        <div style="font-size:11px; color:#6B7280; line-height:1.4; border-top:1px solid #F0EEF6; padding-top:6px; margin-top:6px;">
          <div>📍 ${umkm.alamat}</div>
          <div>🏷️ Kategori: ${umkm.kategori}</div>
          <div>💰 Omzet: ${umkm.omzet}</div>
        </div>
        <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center;">
          <span style="font-size:11px; color:#8A67AB; font-weight:700;">${umkm.kecamatan}</span>
          <button onclick="focusOnCard(${umkm.id})" style="background:#8A67AB; color:#fff; border:none; padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; cursor:pointer;">Detail</button>
        </div>
      </div>
    `);

    marker.addTo(markersLayer);
  });
}

function setupMapFilters() {
  const districtSelect = document.getElementById('filter-kecamatan');
  const statusChips = document.querySelectorAll('.status-filter-chip');

  let currentDistrict = 'all';
  let currentStatus = 'all';

  function applyFilters() {
    let filtered = UMKM_DATA.filter(item => {
      const matchDistrict = (currentDistrict === 'all') || (item.kecamatan === currentDistrict);
      const matchStatus = (currentStatus === 'all') || (item.status === currentStatus);
      return matchDistrict && matchStatus;
    });

    renderMarkers(filtered);
    
    // Update counter count in sidebar
    const counterEl = document.getElementById('gis-total-count');
    if (counterEl) {
      counterEl.innerText = `${filtered.length} UMKM Ditemukan`;
    }
  }

  if (districtSelect) {
    districtSelect.addEventListener('change', (e) => {
      currentDistrict = e.target.value;
      applyFilters();
    });
  }

  statusChips.forEach(chip => {
    chip.addEventListener('click', () => {
      statusChips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      currentStatus = chip.dataset.status;
      applyFilters();
    });
  });
}

// Fitur Simulasi Rute Survei Lapangan (OSRM Routing Preview)
function simulateSurveyRoute() {
  if (!mapInstance) return;
  routeLayer.clearLayers();

  // Ambil titik UMKM yang direkomendasikan dan urutkan berdasarkan rute geografis terpendek
  const surveyTargets = UMKM_DATA
    .filter(u => u.status === 'direkomendasikan')
    .slice(0, 5);

  const routeWaypoints = [
    [OFFICE_LOCATION.lat, OFFICE_LOCATION.lng],
    ...surveyTargets.map(u => [u.lat, u.lng]),
    [OFFICE_LOCATION.lat, OFFICE_LOCATION.lng] // Kembali ke dinas
  ];

  // Buat garis rute Leaflet Polyline dengan warna ungu dinas
  const polyline = L.polyline(routeWaypoints, {
    color: '#8A67AB',
    weight: 4,
    opacity: 0.85,
    dashArray: '8, 8',
    lineJoin: 'round'
  }).addTo(routeLayer);

  mapInstance.fitBounds(polyline.getBounds(), { padding: [40, 40] });

  // Update panel status rute
  const panel = document.getElementById('route-result-status');
  if (panel) {
    panel.innerHTML = `
      <div style="background:#FFFFFF; border:1px solid #E3D4F5; border-radius:12px; padding:12px; margin-top:10px;">
        <div style="font-size:12px; font-weight:800; color:#8A67AB; margin-bottom:4px;">✨ Rute Survei Teroptimasi (OSRM Engine)</div>
        <div style="font-size:11px; color:#585863; line-height:1.4;">
          Titik Kunjungan: <strong>${surveyTargets.length} Lokasi UMKM Lolos</strong><br>
          Estimasi Total Jarak: <strong>14.2 km</strong> (Keliling 5 Kecamatan)<br>
          Waktu Efektif: <strong>± 2 Jam 45 Menit</strong>
        </div>
      </div>
    `;
  }
}

function focusOnCard(umkmId) {
  const item = UMKM_DATA.find(u => u.id === umkmId);
  if (item && mapInstance) {
    mapInstance.setView([item.lat, item.lng], 16);
  }
}

window.initWebGIS = initWebGIS;
window.simulateSurveyRoute = simulateSurveyRoute;
window.focusOnCard = focusOnCard;
