/**
 * Entry point — WhatsApp Gateway Service
 * ----------------------------------------
 * Starts Express, mounts middleware and routes,
 * then initialises the WhatsApp client.
 *
 * Structure:
 *   server.js              ← you are here (entry point)
 *   src/
 *     state.js             ← shared runtime state
 *     client.js            ← whatsapp-web.js setup & events
 *     middleware/auth.js   ← token auth middleware
 *     routes/index.js      ← HTTP routes (status/groups/send/logout)
 */
require('dotenv').config();

const express = require('express');
const auth    = require('./src/middleware/auth');
const routes  = require('./src/routes/index');
const wa      = require('./src/client');

const app  = express();
const PORT = process.env.PORT || 3001;

// ── Prevent crash on unhandled rejections (puppeteer context destroyed, etc.) ─
process.on('unhandledRejection', (err) => {
    console.error('[WA] Unhandled rejection:', err?.message || err);
});

// ── Global middleware ─────────────────────────────────────────────────────────
app.use(express.json());
app.use(auth);

// ── Routes ────────────────────────────────────────────────────────────────────
app.use('/', routes);

// ── Start ─────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
    console.log(`[WA] Service listening on http://localhost:${PORT}`);
});

console.log('[WA] Initialising WhatsApp client…');
wa.init();
