(function() {
    function fnv1a(str) {
        let hash = 0x811c9dc5;
        for (let i = 0; i < str.length; i++) {
            hash ^= str.charCodeAt(i);
            hash += (hash << 1) + (hash << 4) + (hash << 7) + (hash << 8) + (hash << 24);
        }
        return (hash >>> 0).toString(16);
    }

    function getCanvasHash() {
        try {
            const canvas = document.createElement('canvas');
            canvas.width = 200; canvas.height = 50;
            const ctx = canvas.getContext('2d');
            if (!ctx) return 'no-ctx';
            ctx.textBaseline = "top"; ctx.font = "14px 'Arial'";
            ctx.textBaseline = "alphabetic"; ctx.fillStyle = "#f60";
            ctx.fillRect(125, 1, 62, 20); ctx.fillStyle = "#069";
            ctx.fillText("SimplCMS, 🎨!", 2, 15); ctx.fillStyle = "rgba(102, 204, 0, 0.7)";
            ctx.fillText("SimplCMS, 🎨!", 4, 17);
            return canvas.toDataURL();
        } catch (e) { return 'canvas-error'; }
    }

    function getWebGLHash() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (!gl) return 'no-webgl';
            const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
            if (!debugInfo) return 'no-debug-info';
            const vendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
            const renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
            const params = [
                gl.getParameter(gl.MAX_TEXTURE_SIZE),
                gl.getParameter(gl.MAX_VIEWPORT_DIMS)[0],
                gl.getParameter(gl.MAX_VIEWPORT_DIMS)[1],
                gl.getParameter(gl.MAX_VARYING_VECTORS)
            ].join(',');
            return vendor + '~' + renderer + '~' + params;
        } catch (e) { return 'webgl-error'; }
    }

    function getSystemHash() {
        const nav = window.navigator;
        const scr = window.screen;
        return [
            scr.width, scr.height, scr.colorDepth, scr.pixelDepth,
            nav.language, nav.hardwareConcurrency || 0,
            nav.deviceMemory || 0, nav.platform,
            new Date().getTimezoneOffset()
        ].join('|');
    }

    const fp = {
        canvas: fnv1a(getCanvasHash()),
        webgl: fnv1a(getWebGLHash()),
        system: fnv1a(getSystemHash())
    };
    
    const fp_json = encodeURIComponent(JSON.stringify(fp));
    document.cookie = "admin_fp=" + fp_json + "; path=/; max-age=31536000; SameSite=Strict";
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="fingerprint"]').forEach(function(input) {
            input.value = fp_json;
        });
    });
})();