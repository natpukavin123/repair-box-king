/**
 * WhatsApp client setup.
 * Initialises whatsapp-web.js and manages all events.
 * Exports a wrapper so the client can be destroyed and recreated
 * (needed after logout — puppeteer context gets destroyed).
 */
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode  = require('qrcode');
const { execSync } = require('child_process');
const fs      = require('fs');
const path    = require('path');
const state   = require('./state');

const SESSION_DIR = path.resolve(__dirname, '..', 'wa_session');

const PUPPETEER_ARGS = [
    '--no-sandbox',
    '--disable-setuid-sandbox',
    '--disable-dev-shm-usage',
    '--disable-accelerated-2d-canvas',
    '--no-first-run',
    '--no-zygote',
    '--single-process',
    '--disable-gpu',
];

const wa = {
    client: null,
    _reinitialising: false,
};

/**
 * Kill any stale Chrome/Chromium processes that still reference wa_session
 * and remove SingletonLock files so a new browser can start.
 */
function cleanupStaleBrowser() {
    // Kill orphaned Chrome processes tied to our session dir
    try {
        execSync(`pkill -f "chromium.*wa_session" 2>/dev/null || true`, { stdio: 'ignore' });
        execSync(`pkill -f "chrome.*wa_session" 2>/dev/null || true`, { stdio: 'ignore' });
    } catch {}

    // Remove SingletonLock files that prevent Chrome from starting
    try {
        const sessionPath = path.join(SESSION_DIR, 'session');
        const lockFile = path.join(sessionPath, 'SingletonLock');
        if (fs.existsSync(lockFile)) {
            fs.unlinkSync(lockFile);
            console.log('[WA] Removed stale SingletonLock');
        }
    } catch (err) {
        console.warn('[WA] Could not remove SingletonLock:', err.message);
    }
}

function createClient() {
    const c = new Client({
        authStrategy: new LocalAuth({ dataPath: './wa_session' }),
        puppeteer: { headless: true, args: PUPPETEER_ARGS },
    });

    c.on('qr', async (qr) => {
        console.log('[WA] QR code received — scan with your phone');
        state.isConnected = false;
        state.isReady     = false;
        try {
            state.currentQr = await qrcode.toDataURL(qr);
        } catch {
            state.currentQr = qr;
        }
    });

    c.on('authenticated', () => {
        console.log('[WA] Authenticated successfully');
        state.currentQr   = null;
        state.isConnected = true;
    });

    c.on('ready', () => {
        console.log('[WA] Client ready — connected as', c.info?.pushname);
        state.isReady     = true;
        state.isConnected = true;
        state.currentQr   = null;
        state.deviceInfo  = c.info;
    });

    c.on('disconnected', (reason) => {
        console.warn('[WA] Disconnected:', reason);
        state.isConnected = false;
        state.isReady     = false;
        state.deviceInfo  = null;
        state.currentQr   = null;
        // Reinitialise with a fresh client after disconnect
        wa.reinit();
    });

    c.on('auth_failure', (msg) => {
        console.error('[WA] Authentication failure:', msg);
        state.isConnected = false;
        state.isReady     = false;
    });

    return c;
}

/**
 * Destroy the current client and create + initialise a fresh one.
 * Safe to call multiple times — only one reinit runs at a time.
 */
wa.reinit = async function reinit() {
    if (wa._reinitialising) return;
    wa._reinitialising = true;

    console.log('[WA] Reinitialising client…');
    state.isConnected = false;
    state.isReady     = false;
    state.deviceInfo  = null;
    state.currentQr   = null;

    // Destroy old client (ignore errors — it may already be dead)
    if (wa.client) {
        try { await wa.client.destroy(); } catch {}
        wa.client = null;
    }

    // Wait for Chrome to fully exit, then clean up any stale locks
    await new Promise(r => setTimeout(r, 3000));
    cleanupStaleBrowser();
    await new Promise(r => setTimeout(r, 1000));

    wa.client = createClient();
    try {
        await wa.client.initialize();
    } catch (err) {
        console.error('[WA] Reinit initialize error:', err.message);
        // If it still fails, try one more cleanup + retry after longer delay
        wa.client = null;
        await new Promise(r => setTimeout(r, 3000));
        cleanupStaleBrowser();
        await new Promise(r => setTimeout(r, 1000));
        wa.client = createClient();
        try {
            await wa.client.initialize();
        } catch (err2) {
            console.error('[WA] Reinit retry also failed:', err2.message);
        }
    }
    wa._reinitialising = false;
};

/**
 * First-time init — also cleans up stale locks from prior crashes.
 */
wa.init = function init() {
    cleanupStaleBrowser();
    wa.client = createClient();
    wa.client.initialize();
};

module.exports = wa;
