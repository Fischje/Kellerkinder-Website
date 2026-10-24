const APP_VERSION = '2.31.2';
const CACHE_NAME = 'kellerkinder-static-v' + APP_VERSION;
// Logo und Icons mit ?v=Version, wie sie die Seiten einbinden: so holt ein neuer
// Service-Worker sie frisch (auch an Cloudflare vorbei) statt eine alte Kopie zu behalten.
const STATIC_ASSETS = [
  './manifest.webmanifest',
  './assets/kellerkinder-logo.svg?v=' + APP_VERSION,
  './assets/smartphone-install.svg',
  './assets/app-icon-180.png?v=' + APP_VERSION,
  './assets/app-icon-192.png',
  './assets/app-icon-512.png'
];

self.addEventListener('install', event => {
  // cache: 'reload' umgeht den Browser-Cache beim Vorladen
  event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS.map(asset => new Request(asset, { cache: 'reload' })))));
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin || url.pathname.endsWith('/api.php')) return;

  const isStaticAsset = STATIC_ASSETS.some(asset => url.pathname.endsWith(asset.replace('./', '/').split('?')[0]));
  if (!isStaticAsset) return;

  event.respondWith(
    caches.match(event.request).then(cached => cached || fetch(event.request).then(response => {
      const copy = response.clone();
      caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
      return response;
    }))
  );
});
