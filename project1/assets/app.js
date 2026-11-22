// @Copyright by 242011022_Sinta Nurpadilah_TIF RP 24 F
const DATA_INFORMASI = [
  {
    id: 1,
    judul: "Peluncuran Platform Digital Nusantara v2.0",
    kategori: "Produk & Teknologi",
    tanggal: "15 Oktober 2026",
    ringkasan: "Peningkatan kecepatan layanan dan keamanan data bagi seluruh mitra bisnis.",
    detail: "Platform Digital Nusantara v2.0 hadir dengan antarmuka lebih cepat, sistem autentikasi berlapis, serta integrasi pelaporan real-time untuk mendukung operasional mitra bisnis di berbagai daerah."
  },
  {
    id: 2,
    judul: "Program Beasiswa & Pelatihan Industri TI",
    kategori: "CSR & Karir",
    tanggal: "02 November 2026",
    ringkasan: "Membuka kesempatan magang dan pelatihan web modern bagi mahasiswa.",
    detail: "Melalui program tanggung jawab sosial perusahaan, kami membuka pendaftaran pelatihan intensif pengembangan web, basis data, dan proyek tim bersama mentor industri bagi mahasiswa."
  },
  {
    id: 3,
    judul: "Kemitraan Strategis Logistik Nasional",
    kategori: "Berita Perusahaan",
    tanggal: "18 November 2026",
    ringkasan: "Memperluas jaringan distribusi ke 12 kota besar di Indonesia.",
    detail: "Kerja sama strategis ini memperkuat rantai pasok dan pengiriman barang antarkota agar lebih tepat waktu, terpantau, dan efisien bagi pelanggan korporasi maupun ritel."
  }
];

function renderDaftarInformasi() {
  const container = document.getElementById("daftar-informasi");
  if (!container) return;

  container.innerHTML = "";

  DATA_INFORMASI.forEach((item) => {
    const col = document.createElement("div");
    col.className = "col-lg-4 col-md-6";

    col.innerHTML = `
      <div class="card h-100 border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex flex-column">
          <div class="mb-2">
            <span class="badge bg-primary fw-semibold px-2 py-1 rounded-2">${item.kategori}</span>
          </div>
          <h6 class="card-title fw-semibold text-dark mb-1">${item.judul}</h6>
          <p class="text-muted small mb-2">Tanggal: ${item.tanggal}</p>
          <p class="card-text text-secondary small mb-3 flex-grow-1">${item.ringkasan}</p>
          <a href="detail.html?id=${item.id}" class="btn btn-outline-primary btn-sm w-100">Lihat Detail</a>
        </div>
      </div>
    `;

    container.appendChild(col);
  });
}

function renderDetailInformasi() {
  const box = document.getElementById("detail-box");
  if (!box) return;

  const params = new URLSearchParams(window.location.search);
  const id = parseInt(params.get("id"), 10);
  const item = DATA_INFORMASI.find((d) => d.id === id);

  if (!item) {
    box.innerHTML = `
      <div class="alert alert-warning mb-3">Informasi tidak ditemukan.</div>
      <a href="index.html" class="btn btn-primary">Kembali ke Beranda</a>
    `;
    return;
  }

  box.innerHTML = `
    <span class="badge bg-primary px-2 py-1 mb-2">${item.kategori}</span>
    <p class="text-muted small mb-3">Tanggal: ${item.tanggal}</p>
    <h2 class="fw-bold text-dark mb-3">${item.judul}</h2>
    <p class="lead text-secondary mb-4">${item.ringkasan}</p>
    <hr>
    <p class="text-dark mb-4" style="line-height: 1.8;">${item.detail}</p>
    <a href="index.html" class="btn btn-outline-primary">Kembali ke Beranda</a>
  `;
}

function handleLogin(event) {
  event.preventDefault();

  const username = document.getElementById("loginUsername").value.trim();
  const password = document.getElementById("loginPassword").value.trim();
  const alertBox = document.getElementById("loginAlert");

  const registered = JSON.parse(localStorage.getItem("registeredUser") || "null");
  const validUsername = registered && registered.username ? registered.username : "sinta24";
  const validPassword = registered && registered.password ? registered.password : "123456";

  if (username === "" || password === "") {
    alertBox.className = "alert alert-warning";
    alertBox.textContent = "Username dan password tidak boleh kosong.";
    alertBox.classList.remove("d-none");
    return;
  }

  if (username === validUsername && password === validPassword) {
    localStorage.setItem("isLoggedIn", "true");
    localStorage.setItem("activeUser", username);
    alertBox.className = "alert alert-success";
    alertBox.textContent = "Login berhasil! Mengalihkan ke Menu Utama...";
    alertBox.classList.remove("d-none");

    setTimeout(() => {
      window.location.href = "dashboard.html";
    }, 700);
  } else {
    alertBox.className = "alert alert-danger";
    alertBox.textContent = "Username atau password salah. (Default: sinta24 / 123456)";
    alertBox.classList.remove("d-none");
  }
}

function handleRegister(event) {
  event.preventDefault();

  const nama = document.getElementById("regNama").value.trim();
  const username = document.getElementById("regUsername").value.trim();
  const password = document.getElementById("regPassword").value.trim();
  const alertBox = document.getElementById("regAlert");

  if (nama === "" || username === "" || password.length < 6) {
    alertBox.className = "alert alert-danger";
    alertBox.textContent = "Isi nama lengkap, username, dan password minimal 6 karakter.";
    alertBox.classList.remove("d-none");
    return;
  }

  localStorage.setItem("registeredUser", JSON.stringify({
    nama,
    username,
    password
  }));

  alertBox.className = "alert alert-success";
  alertBox.textContent = "Registrasi berhasil! Mengalihkan ke halaman Login...";
  alertBox.classList.remove("d-none");

  setTimeout(() => {
    window.location.href = "login.html";
  }, 800);
}

function checkDashboardAuth() {
  if (localStorage.getItem("isLoggedIn") !== "true") {
    window.location.href = "login.html";
    return;
  }

  const nameEl = document.getElementById("dashboard-name");
  if (nameEl) {
    const registered = JSON.parse(localStorage.getItem("registeredUser") || "null");
    if (registered && registered.nama) {
      nameEl.textContent = registered.nama;
    } else {
      nameEl.textContent = "Sinta Nurpadilah";
    }
  }
}

function logoutUser() {
  localStorage.removeItem("isLoggedIn");
  window.location.href = "login.html";
}