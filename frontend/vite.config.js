import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [sveltekit()],

	server: {
    host: '0.0.0.0', 
    port: 5173,
    strictPort: true,

    proxy: {
      '/api': {
        target: 'http://app:9000', 
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '/api'),
      },
    },
  },
});
