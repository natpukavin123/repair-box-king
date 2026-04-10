/**
 * Shared runtime state for the WhatsApp client.
 * All modules import this object; mutations are visible everywhere.
 */
const state = {
    currentQr:   null,    // base64 data URL of the current QR code
    isConnected: false,   // authenticated (but may not be ready yet)
    isReady:     false,   // fully ready to send/receive messages
    deviceInfo:  null,    // client.info object when ready
};

module.exports = state;
