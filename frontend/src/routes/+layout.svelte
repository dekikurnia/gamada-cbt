<script>
	import "../app.css";
	import favicon from '$lib/assets/favicon.svg';

	let { children } = $props();
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" />
</svelte:head>

<!-- 🌐 Wrapper utama dengan font dan background -->
<div class="min-h-screen font-sans bg-gray-900 text-gray-100">
	{@render children?.()}
</div>
