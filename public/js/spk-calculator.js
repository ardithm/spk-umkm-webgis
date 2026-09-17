/**
 * SPK Profile Matching Calculation Engine
 * Berdasarkan spesifikasi Algoritma Profile Matching pada prd.md
 */

// Target Ideal & Tipe Faktor Kriteria (Dinas Koperasi & UMKM Banjarmasin)
const SPK_CRITERIA = {
  k1: { name: 'Omzet Tahunan', target: 4, type: 'core', weightFactor: 0.60 },
  k2: { name: 'Aset Usaha', target: 3, type: 'secondary', weightFactor: 0.40 },
  k3: { name: 'Jumlah Tenaga Kerja', target: 4, type: 'core', weightFactor: 0.60 },
  k4: { name: 'Jangkauan Pemasaran', target: 4, type: 'secondary', weightFactor: 0.40 },
  k5: { name: 'Status Perizinan (NIB/SKU/P-IRT)', target: 5, type: 'core', weightFactor: 0.60 }
};

// Tabel Konversi Nilai GAP ke Bobot Nilai
function getGapWeight(gap) {
  switch (gap) {
    case 0: return 5.0;     // Tidak ada selisih (Kompetensi sesuai kebutuhan)
    case 1: return 4.5;     // Kelebihan 1 tingkat
    case -1: return 4.0;    // Kekurangan 1 tingkat
    case 2: return 3.5;     // Kelebihan 2 tingkat
    case -2: return 3.0;    // Kekurangan 2 tingkat
    case 3: return 2.5;     // Kelebihan 3 tingkat
    case -3: return 2.0;    // Kekurangan 3 tingkat
    case 4: return 1.5;     // Kelebihan 4 tingkat
    case -4: return 1.0;    // Kekurangan 4 tingkat
    default:
      return gap > 4 ? 1.0 : (gap < -4 ? 1.0 : 3.0);
  }
}

function calculateProfileMatching() {
  const k1Val = parseInt(document.getElementById('calc-k1').value, 10);
  const k2Val = parseInt(document.getElementById('calc-k2').value, 10);
  const k3Val = parseInt(document.getElementById('calc-k3').value, 10);
  const k4Val = parseInt(document.getElementById('calc-k4').value, 10);
  const k5Val = parseInt(document.getElementById('calc-k5').value, 10);

  // 1. Hitung GAP (Nilai Aktual - Target Ideal)
  const gapK1 = k1Val - SPK_CRITERIA.k1.target;
  const gapK2 = k2Val - SPK_CRITERIA.k2.target;
  const gapK3 = k3Val - SPK_CRITERIA.k3.target;
  const gapK4 = k4Val - SPK_CRITERIA.k4.target;
  const gapK5 = k5Val - SPK_CRITERIA.k5.target;

  // 2. Pemetaan Bobot GAP
  const bobotK1 = getGapWeight(gapK1);
  const bobotK2 = getGapWeight(gapK2);
  const bobotK3 = getGapWeight(gapK3);
  const bobotK4 = getGapWeight(gapK4);
  const bobotK5 = getGapWeight(gapK5);

  // 3. Core Factor (NCF): K1, K3, K5
  const ncf = (bobotK1 + bobotK3 + bobotK5) / 3;

  // 4. Secondary Factor (NSF): K2, K4
  const nsf = (bobotK2 + bobotK4) / 2;

  // 5. Total Nilai Akhir (NCF 60% + NSF 40%)
  const totalScore = (ncf * 0.60) + (nsf * 0.40);

  // Update UI Elements
  document.getElementById('res-ncf').innerText = ncf.toFixed(2);
  document.getElementById('res-nsf').innerText = nsf.toFixed(2);
  document.getElementById('res-total').innerText = totalScore.toFixed(2);

  // Status Badge Logic (Passing grade instansi misal: 4.00)
  const statusContainer = document.getElementById('res-status-container');
  if (totalScore >= 4.25) {
    statusContainer.innerHTML = `
      <div class="status-badge-lg pass">
        <span>✨ Sangat Direkomendasikan (Prioritas Kuota)</span>
      </div>
    `;
  } else if (totalScore >= 3.80) {
    statusContainer.innerHTML = `
      <div class="status-badge-lg pass" style="background:#EEF8F1; color:#1E7E34; border-color:#BDE3C8;">
        <span>✅ Memenuhi Syarat Seleksi (Lolos Verifikasi)</span>
      </div>
    `;
  } else {
    statusContainer.innerHTML = `
      <div class="status-badge-lg consider">
        <span>⚠️ Dipertimbangkan / Cadangan Gelombang</span>
      </div>
    `;
  }

  // Update breakdown table tags if available
  const k1Detail = document.getElementById('calc-k1-gap');
  if (k1Detail) {
    k1Detail.innerText = `GAP: ${gapK1 > 0 ? '+' + gapK1 : gapK1} (Bobot: ${bobotK1})`;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const selectInputs = ['calc-k1', 'calc-k2', 'calc-k3', 'calc-k4', 'calc-k5'];
  selectInputs.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('change', calculateProfileMatching);
    }
  });

  // Jalankan kalkulasi awal saat halaman selesai dimuat
  if (document.getElementById('calc-k1')) {
    calculateProfileMatching();
  }
});
