/**
 * Bearer-token auth middleware.
 * Skips check if WA_TOKEN is not configured (dev mode).
 */
const TOKEN = process.env.WA_TOKEN || '';

module.exports = function auth(req, res, next) {
    if (!TOKEN) return next(); // no token set → open access (local dev)

    const header = req.headers['x-wa-token'];
    if (header !== TOKEN) {
        return res.status(401).json({ success: false, message: 'Unauthorized' });
    }
    next();
};
