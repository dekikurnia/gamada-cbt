<script>
  export let name = ""; 
  export let size = 24; 
  export let color = "currentColor"; 
</script>

{#if name === "menu"}
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    class="w-5 h-5">
    <line x1="4" y1="6" x2="20" y2="6"/>
    <line x1="4" y1="12" x2="20" y2="12"/>
    <line x1="4" y1="18" x2="20" y2="18"/>
  </svg>


{:else if name === "home"}
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>

{:else if name === "logout"}
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>

{:else if name === "user"}
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check-icon lucide-user-check"><path d="m16 11 2 2 4-4"/><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>

{:else if name === "settings"}
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke={color} stroke-width="1.5" class={`w-${size/4} h-${size/4}`}>
    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1 1 0 011.35-.937l.39.145a8.998 8.998 0 014.763 4.763l.145.39a1 1 0 01-.937 1.35l-.35.062a3 3 0 000 5.925l.35.062a1 1 0 01.937 1.35l-.145.39a8.998 8.998 0 01-4.763 4.763l-.39.145a1 1 0 01-1.35-.937l.062-.35a3 3 0 00-5.925 0l.062.35a1 1 0 01-1.35.937l-.39-.145a8.998 8.998 0 01-4.763-4.763l-.145-.39a1 1 0 01.937-1.35l.35-.062a3 3 0 000-5.925l-.35-.062a1 1 0 01-.937-1.35l.145-.39a8.998 8.998 0 014.763-4.763l.39-.145a1 1 0 011.35.937l-.062.35a3 3 0 005.925 0l-.062-.35z" />
  </svg>

{:else}
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke={color} stroke-width="1.5" class={`w-${size/4} h-${size/4}`}>
    <circle cx="12" cy="12" r="10" />
    <text x="12" y="16" text-anchor="middle" font-size="10" fill={color}>?</text>
  </svg>
{/if}
