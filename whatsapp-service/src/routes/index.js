/**
 * All HTTP routes for the WhatsApp gateway.
 *
 * GET  /status  — connection state + QR data URL
 * GET  /groups  — list all WA groups the account is in
 * POST /send    — send a text message  { to, message }
 * POST /logout  — logout and clear session
 */
const router = require('express').Router();
const wa     = require('../client');
const state  = require('../state');

// ─── GET /status ──────────────────────────────────────────────────────────────
router.get('/status', (req, res) => {
    let status = 'disconnected';
    if (state.isReady)       status = 'connected';
    else if (state.currentQr) status = 'qr_pending';

    res.json({
        success: true,
        status,
        qr: state.currentQr,
        device: state.isReady && state.deviceInfo
            ? {
                name:   state.deviceInfo.pushname || '',
                number: state.deviceInfo.wid?.user || '',
            }
            : null,
    });
});

// ─── GET /groups ─────────────────────────────────────────────────────────────
router.get('/groups', async (req, res) => {
    if (!state.isReady || !wa.client) {
        return res.status(503).json({ success: false, message: 'WhatsApp not ready' });
    }
    try {
        const chats  = await wa.client.getChats();
        const groups = chats
            .filter(c => c.isGroup)
            .map(c => ({ id: c.id._serialized, name: c.name }));
        res.json({ success: true, data: groups });
    } catch (err) {
        console.error('[WA] /groups error:', err.message);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ─── POST /send ───────────────────────────────────────────────────────────────
router.post('/send', async (req, res) => {
    if (!state.isReady || !wa.client) {
        return res.status(503).json({ success: false, message: 'WhatsApp not ready' });
    }
    const { to, message } = req.body;
    if (!to || !message) {
        return res.status(400).json({ success: false, message: '"to" and "message" are required' });
    }
    try {
        await wa.client.sendMessage(to, message);
        res.json({ success: true });
    } catch (err) {
        console.error('[WA] /send error:', err.message);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ─── POST /logout ─────────────────────────────────────────────────────────────
router.post('/logout', async (req, res) => {
    try {
        if (wa.client) {
            await wa.client.logout();
        }
        // Send response immediately, then reinit in the background
        res.json({ success: true, message: 'Logged out' });
        // Destroy and recreate client so it shows a fresh QR
        wa.reinit();
    } catch (err) {
        console.error('[WA] /logout error:', err.message);
        // Even if logout threw, still respond and reinit
        res.status(200).json({ success: true, message: 'Logged out (with cleanup)' });
        wa.reinit();
    }
});

module.exports = router;
