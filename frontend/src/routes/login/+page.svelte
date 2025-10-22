<script>
  import { onMount } from 'svelte';
  import axios from 'axios';
  import Swal from 'sweetalert2';
  import { goto } from '$app/navigation';
  
  let username = '';
  let password = '';
  let showPassword = false; 
  
  // Buat template toast biar bisa dipanggil kapan aja
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    background: '#1e293b', // warna background toast
    color: '#f1f5f9',       // warna teks
    customClass: {
      timerProgressBar: 'custom-progress-bar'
    },
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });
  
  
  onMount(() => {
    const token = localStorage.getItem('token');
    if (token) {
      goto('/token'); // langsung lempar ke halaman token
    }
  });
  
  
  const handleLogin = async (event) => {
    event.preventDefault();
    
    try {
      const response = await axios.post('http://localhost:8000/api/login', {
        login: username,
        password
      });
      
      // Simpan token dari backend
      localStorage.setItem('token', response.data.token);
      
      // Tampilkan toast sukses
      Toast.fire({
        icon: 'success',
        title: 'Login berhasil!',
      });
      
      // Redirect setelah 2 detik
      setTimeout(() => goto('/token'), 2000);
      
    } catch (error) {
      const message =
      error.response?.data?.message ||
      'Tidak dapat terhubung ke server.';
      
      Toast.fire({
        icon: 'error',
        title: message,
      });
    }
  };
</script>

<!-- UI -->
<div class="flex items-center justify-center min-h-screen bg-gray-900 px-4 sm:px-6 lg:px-8">
  <div class="w-full max-w-md bg-gray-800 rounded-lg shadow-lg p-6 sm:p-8">
    <div class="flex flex-col items-center mb-6">
      <div class="flex items-center justify-center w-16 h-16 bg-orange-600 rounded-full mb-4">
        <span class="text-2xl font-bold text-white">GMD</span>
      </div>
      <h1 class="text-2xl font-bold text-white">Gamada CBT</h1>
    </div>
    
    <form on:submit|preventDefault={handleLogin} class="space-y-5">
      <div>
        <label for="username" class="block mb-2 text-sm font-medium text-gray-200"
        >Nama Pengguna</label
        >
        <input
        type="text"
        id="username"
        bind:value={username}
        required
        placeholder="Masukkan nama pengguna"
        class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400"
        />
      </div>
      
      <div>
        <label for="password" class="block mb-2 text-sm font-medium text-gray-200">Kata Sandi</label>
        <div class="relative">
          <input
          type={showPassword ? "text" : "password"} 
          id="password"
          bind:value={password}
          required
          placeholder=".........." 
  class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 placeholder-gray-400 placeholder:text-2xl tracking-widest"
          />
          
          <!-- 👁️ icon toggle password -->
          <button
          type="button"
          class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-white"
          on:click={() => (showPassword = !showPassword)}
          >
          {#if showPassword}
          <!-- Icon mata terbuka -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.345 4.5 12 4.5c4.655 0 8.577 3.01 9.964 7.183.07.21.07.428 0 .639C20.577 16.49 16.655 19.5 12 19.5c-4.655 0-8.577-3.01-9.964-7.178z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {:else}
          <!-- Icon mata tertutup -->
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c1.615 0 3.163-.328 4.584-.918M6.228 6.228l11.544 11.544M9.88 9.88a3 3 0 104.24 4.24" />
          </svg>
          {/if}
        </button>
      </div>
    </div>
    
    <button
    type="submit"
    class="w-full text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
    >
    Masuk
  </button>
</form>
</div>
</div>
