<script>
  import axios from "axios";
  import { onMount } from "svelte";
  import { goto } from "$app/navigation";

  let tokenUjian = "";
  let authToken = "";
  let user = {};
  let currentTime = "";
  let showMenu = false;

  // ⏰ Update jam realtime
  const updateClock = () => {
    const now = new Date();
    const date = now.toLocaleDateString("id-ID", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });
    const time = now.toLocaleTimeString("id-ID", { hour12: false });
    currentTime = `${date} — ${time}`;
  };

  // 🚪 Jika belum login, redirect ke login
  onMount(async () => {
    authToken = localStorage.getItem("token");
    if (!authToken) {
      goto("/login");
      return;
    }

    updateClock();
    setInterval(updateClock, 1000);

    // 🧍 Ambil data peserta
    try {
      const res = await axios.get("http://localhost:8000/api/me", {
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      });

      user = {
        name: res.data.name,
        classroom: res.data.classroom?.name || "-",
        department: res.data.classroom?.department?.name || "-",
      };
    } catch (error) {
      console.error("Gagal ambil data user:", error);
      localStorage.removeItem("token");
      goto("/login");
    }
  });

  // 🚪 Logout
  const handleLogout = async () => {
    try {
      await axios.post(
        "http://localhost:8000/api/logout",
        {},
        { headers: { Authorization: `Bearer ${authToken}` } }
      );
    } catch (e) {
      console.warn("Logout API gagal, lanjut hapus token lokal");
    }

    localStorage.removeItem("token");
    goto("/login");
  };

  // 🎫 Validasi token ujian
  const handleSubmit = async () => {
    if (!tokenUjian.trim()) {
      alert("Token ujian tidak boleh kosong!");
      return;
    }

    try {
      const res = await axios.post(
        "http://localhost:8000/api/exams/token",
        { token: tokenUjian },
        { headers: { Authorization: `Bearer ${authToken}` } }
      );
      console.log("Token valid:", res.data);
      alert("Token valid, selamat mengerjakan ujian!");
      // goto('/ujian')
    } catch (error) {
      alert(error.response?.data?.message || "Token salah atau server error.");
    }
  };
</script>

<!-- 🌙 Navbar -->
<nav class="bg-gray-800 text-white shadow-md w-full flex justify-between items-center px-6 py-3">
  <div class="flex items-center gap-3">
    <div class="flex items-center justify-center w-10 h-10 bg-orange-600 rounded-full">
      <span class="font-bold text-lg">G</span>
    </div>
    <h1 class="text-xl font-semibold tracking-wide">Gamada CBT</h1>
  </div>

  <div class="flex items-center gap-6">
    <p class="text-sm font-medium text-gray-200">{currentTime}</p>

    <div class="relative">
      <button
        on:click={() => (showMenu = !showMenu)}
        class="flex items-center gap-2 bg-gray-700 px-3 py-2 rounded-md hover:bg-gray-600 focus:outline-none"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0v.75H4.5v-.75Z" />
        </svg>
        <span>{user.name || "Memuat..."}</span>
      </button>

      {#if showMenu}
      <div class="absolute right-0 mt-2 w-40 bg-gray-700 rounded-md shadow-lg">
        <button
          on:click={handleLogout}
          class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-600"
        >
          Logout
        </button>
      </div>
      {/if}
    </div>
  </div>
</nav>

<!-- 🧾 Konfirmasi Data Peserta -->
<div class="flex flex-col items-center justify-center bg-gray-900 text-white min-h-[calc(100vh-4rem)] px-4">
  <div class="w-full max-w-lg bg-gray-800 rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold mb-6 text-center text-white">Konfirmasi Data Peserta</h2>

    <div class="space-y-3 mb-6 text-sm">
      <div class="flex justify-between border-b border-gray-700 pb-2">
        <span class="text-gray-300">Nama Lengkap</span>
        <span class="font-medium text-white">{user.name || "-"}</span>
      </div>

      <div class="flex justify-between border-b border-gray-700 pb-2">
        <span class="text-gray-300">Jurusan</span>
        <span class="font-medium text-white">{user.department || "-"}</span>
      </div>

      <div class="flex justify-between border-b border-gray-700 pb-2">
        <span class="text-gray-300">Kelas</span>
        <span class="font-medium text-white">{user.classroom || "-"}</span>
      </div>

      <div class="border-b border-gray-700 pb-2">
        <label for="token" class="text-gray-300 block mb-1">Token Ujian</label>
        <input
          id="token"
          type="text"
          bind:value={tokenUjian}
          placeholder="Masukkan token ujian"
          class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 placeholder-gray-400"
        />
      </div>
    </div>

    <button
      on:click={handleSubmit}
      class="w-full text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-full text-sm px-5 py-2.5 text-center transition"
    >
      Mulai Ujian
    </button>
  </div>
</div>

<style>
  /* Supaya konten tetap tengah tanpa scroll */
  html, body {
    overflow: hidden;
  }
</style>
