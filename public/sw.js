const CACHE_NAME = 'repairbox-v1';
const STATIC_ASSETS = ['/', '/track'];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (e) => {
  // Only handle GET requests
  if (e.request.method !== 'GET') return;
  // Skip admin, login, api, and non-http requests
  const url = new URL(e.request.url);
  if (url.pathname.startsWith('/admin') ||
      url.pathname.startsWith('/login') ||
      url.pathname.startsWith('/api') ||
      !e.request.url.startsWith('http')) return;

  e.respondWith(
    fetch(e.request)
      .then(response => {
        // Cache successful responses for static assets
        if (response.ok && (url.pathname === '/' || url.pathname === '/track')) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(e.request, clone));
        }
        return response;
      })
      .catch(() => caches.match(e.request))
  );
});
