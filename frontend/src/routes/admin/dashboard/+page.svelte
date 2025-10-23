<script>
  import { onMount } from "svelte";
  import axios from "axios";
  import { goto } from "$app/navigation";
  import Icon from "$lib/components/Icon.svelte";
  import Swal from 'sweetalert2';
  import { fade } from "svelte/transition";
  
  let authToken = "";
  let user = {};
  let showMenu = false;
  let sidebarCollapsed = false;
  let leaving = false;
  
  onMount(async () => {
    authToken = localStorage.getItem("admin_token");
    if (!authToken) {
      goto("/admin/login");
      return;
    }
    
    try {
      const res = await axios.get("http://localhost:8000/api/me", {
        headers: { Authorization: `Bearer ${authToken}` },
      });
      user = res.data;
      
      if (user.role !== "admin" && user.role !== "teacher") {
        Toast.fire({
          icon: "error",
          title: "Akses Ditolak",
          text: "Halaman ini hanya untuk Admin atau Guru",
          timer: 2500,
          timerProgressBar: true,
        });
        localStorage.removeItem("admin_token");
        setTimeout(() => goto("/login"), 2500);
      }
      
    } catch (e) {
      localStorage.removeItem("admin_token");
      goto("/admin/login");
    }
  });
  
  const handleLogout = async () => {
    try {
      await axios.post("http://localhost:8000/api/logout", {}, {
        headers: { Authorization: `Bearer ${authToken}` },
      });
    } catch (e) {
      
    }
    localStorage.removeItem("admin_token");
    Toast.fire({
      icon: "success",
      title: "Logout berhasil",
      text: "Sampai jumpa kembali!",
      timer: 2000,              
      timerProgressBar: true,
    });
    leaving = true;
    setTimeout(() => {
      goto("/admin/login");
    }, 450);
  };
  
  const Toast = Swal.mixin({
    toast: true,
    position: "bottom-end", // 🔸 kanan bawah biar elegan
    showConfirmButton: false,
    background: "#1e293b",
    color: "#f1f5f9",
    timerProgressBar: true,
    customClass: {
      popup: "shadow-lg rounded-lg",
      timerProgressBar: "custom-progress-bar",
    },
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  });
</script>

<div out:fade={{ duration: 400 }} in:fade={{ duration: 400 }}>
  <!-- LAYOUT -->
  <div class="flex h-screen bg-gray-900 text-white">
    <!-- Sidebar -->
    <aside
    class={`${
      sidebarCollapsed ? "w-20" : "w-64"
      } bg-gray-800 border-r border-gray-700 flex flex-col transition-all duration-300`}
      >
      <!-- Logo + Toggle -->
      <div class="flex items-center justify-between px-4 py-4 border-b border-gray-700">
        <div class="flex items-center gap-2">
          <div class="flex items-center justify-center w-8 h-8 bg-orange-600 rounded-full">
            <span class="font-bold text-lg">G</span>
          </div>
          {#if !sidebarCollapsed}
          <h1 class="font-semibold text-lg whitespace-nowrap">Gamada CBT</h1>
          {/if}
        </div>
        
        <button
        on:click={() => (sidebarCollapsed = !sidebarCollapsed)}
        class="text-gray-400 hover:text-white focus:outline-none"
        aria-label="Toggle sidebar"
        >
        <Icon name="menu" size={20} />
      </button>
    </div>
    
    <!-- Menu Items -->
    <nav class="flex-1 mt-4 space-y-1 px-2">
      <a
      href="/admin/dashboard"
      class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 transition"
      >
      <Icon name="home" size={20} />
      
      {#if !sidebarCollapsed}
      <span>Dashboard</span>
      {/if}
    </a>
  </nav>
  
  <!-- Logout -->
  <div class="border-t border-gray-700 p-2 mt-auto">
    <button
    on:click={handleLogout}
    class="flex items-center gap-3 w-full px-3 py-2 text-left rounded-lg hover:bg-gray-700 transition"
    >
    <Icon name="logout" size={20} />
    
    {#if !sidebarCollapsed}
    <span>Logout</span>
    {/if}
  </button>
</div>
</aside>

<!-- Content -->
<div class="flex-1 flex flex-col">
  <!-- Navbar -->
  <nav class="bg-gray-800 border-b border-gray-700 shadow-sm px-6 py-3 flex justify-between items-center">
    <h2 class="text-lg font-semibold text-white">Dashboard Admin</h2>
    
    <div class="relative">
      <button
      on:click={() => (showMenu = !showMenu)}
      class="flex items-center gap-2 bg-gray-700 px-3 py-2 rounded-md hover:bg-gray-600 focus:outline-none"
      >
      
      <Icon name="user" size={20} />
      <span class="text-gray-200 font-medium">{user.name || "Admin"}</span>
    </button>
    
    {#if showMenu}
    <div class="absolute right-0 mt-2 w-40 bg-gray-700 border border-gray-600 rounded-md shadow-lg z-10">
      <button
      on:click={handleLogout}
      class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-gray-600"
      >
      Logout
    </button>
  </div>
  {/if}
</div>
</nav>

<!-- Dashboard Content -->
<main class="flex-1 p-6 bg-gray-900 overflow-y-auto">
  <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
    <h1 class="text-2xl font-bold mb-2 text-white">Dashboard Admin</h1>
    <p class="text-gray-400">
      Selamat datang di panel admin <span class="text-orange-500 font-semibold">Gamada CBT</span>.
    </p>
  </div>
</main>
</div>
</div>
</div>


<style>
  /* scrollbar minor styling */
  ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }
  ::-webkit-scrollbar-thumb {
    background-color: #374151; /* gray-700 */
    border-radius: 10px;
  }
  
  /* warna progress swal2 (jika pake) */
  .custom-progress-bar {
    background-color: #ea580c !important; /* orange-600 */
  }
</style>
