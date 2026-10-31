import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const blade = readFileSync(new URL('../../resources/views/check-in/index.blade.php', import.meta.url), 'utf8');
const script = blade.match(/<script>\s*([\s\S]*?)<\/script>/)[1]
    .replace(/@json\(route\('check-in.store'\)\)/g, "'/check-in'")
    .replace(/@json\(csrf_token\(\)\)/g, "'test-token'")
    .replace(/@if \(session\('success'\)\)[\s\S]*?@endif/, '');

function setup({ pauseFails = false } = {}) {
    const elements = new Map();
    const documentEvents = {};
    const requests = [];
    const alerts = [];
    let autoStart;
    let scanner;
    let currentAlert;
    let loading = false;
    let closing = false;
    let closeCalls = 0;
    const element = (id) => {
        if (!elements.has(id)) elements.set(id, {
            textContent: '', value: 'RR-MANUAL',
            classList: { add() {}, remove() {} },
            events: {},
            addEventListener(type, listener) { this.events[type] = listener; },
            querySelector() { return element('input'); },
            reset() {},
        });
        return elements.get(id);
    };
    const Swal = {
        fire(options) {
            const previous = currentAlert;
            currentAlert = options;
            alerts.push(options);
            loading = false;
            closing = false;
            previous?.didDestroy();
            options.didOpen?.();
        },
        isVisible: () => Boolean(currentAlert) && !closing,
        isLoading: () => loading,
        showLoading() { loading = true; },
        close() { loading = false; closing = true; closeCalls++; },
    };
    class Html5Qrcode {
        constructor() { scanner = this; this.pauses = 0; this.resumes = 0; }
        static async getCameras() { return [{ id: 'rear', label: 'Back' }]; }
        async start(id, config, callback) { this.scan = callback; }
        pause() {
            if (pauseFails) throw new Error('Pause unavailable');
            this.pauses++;
        }
        resume() { this.resumes++; }
        async stop() {}
        clear() {}
    }
    vm.runInNewContext(script, {
        document: {
            getElementById: element,
            addEventListener(type, listener) { documentEvents[type] = listener; },
        },
        window: { Swal }, Html5Qrcode,
        setTimeout(callback) { autoStart = callback; },
        fetch: () => new Promise((resolve, reject) => requests.push({ resolve, reject })),
    });
    documentEvents.DOMContentLoaded();
    return {
        start: () => autoStart(),
        get scanner() { return scanner; },
        get closeCalls() { return closeCalls; },
        requests, alerts, element,
        key(key) { documentEvents.keydown({ key, preventDefault() {} }); },
        destroy() { const alert = currentAlert; currentAlert = null; alert.didDestroy(); },
        manual() {
            const form = element('manual-form');
            return form.events.submit({ preventDefault() {}, currentTarget: form });
        },
    };
}

for (const outcome of ['success', 'error']) {
    test(`QR ${outcome}: block scans through progress, result and closing animation`, async () => {
        const app = setup();
        await app.start();
        const pending = app.scanner.scan('RR-FIRST');
        assert.equal(app.scanner.pauses, 1);
        await app.scanner.scan('RR-DURING-PROGRESS');
        app.key('Enter');
        app.key(' ');
        assert.equal(app.closeCalls, 0);
        assert.equal(app.requests.length, 1);
        assert.equal(app.scanner.resumes, 0);
        if (outcome === 'success') {
            app.requests[0].resolve({ ok: true, json: async () => ({ message: 'OK' }) });
        } else {
            app.requests[0].reject(new Error('Network failed'));
        }
        await pending;
        assert.equal(app.alerts.at(-1).icon, outcome);
        assert.equal(app.scanner.resumes, 0);
        await app.scanner.scan('RR-DURING-RESULT');
        app.key('Enter');
        assert.equal(app.closeCalls, 1);
        await app.scanner.scan('RR-DURING-CLOSE');
        assert.equal(app.requests.length, 1);
        app.destroy();
        assert.equal(app.scanner.resumes, 1);
        const next = app.scanner.scan('RR-NEXT');
        assert.equal(app.requests.length, 2);
        app.requests[1].resolve({ ok: true, json: async () => ({ message: 'OK' }) });
        await next;
    });
}

test('manual check-in pauses camera and keeps it paused until result closes', async () => {
    const app = setup();
    await app.start();
    const pending = app.manual();
    assert.equal(app.scanner.pauses, 1);
    await app.scanner.scan('RR-QR');
    assert.equal(app.requests.length, 1);
    app.requests[0].resolve({ ok: true, json: async () => ({ message: 'OK' }) });
    await pending;
    assert.equal(app.scanner.resumes, 0);
    app.destroy();
    assert.equal(app.scanner.resumes, 1);
});

test('camera starting while a popup is open pauses as soon as it starts', async () => {
    const app = setup();
    app.element('input').value = '';
    await app.manual();
    await app.start();
    assert.equal(app.scanner.pauses, 1);
    await app.scanner.scan('RR-BLOCKED');
    assert.equal(app.requests.length, 0);
    app.destroy();
    assert.equal(app.scanner.resumes, 1);
});

test('popup lock still rejects scan callbacks when camera pause fails', async () => {
    const app = setup({ pauseFails: true });
    await app.start();
    const pending = app.scanner.scan('RR-FIRST');
    app.requests[0].resolve({ ok: false, json: async () => ({ message: 'Invalid QR' }) });
    await pending;
    await app.scanner.scan('RR-BLOCKED');
    assert.equal(app.requests.length, 1);
    app.destroy();
    assert.equal(app.scanner.resumes, 0);
});

test('closing a result after stopping camera does not resume it', async () => {
    const app = setup();
    await app.start();
    const pending = app.scanner.scan('RR-FIRST');
    app.requests[0].resolve({ ok: true, json: async () => ({ message: 'OK' }) });
    await pending;
    await app.element('stop-camera').events.click();
    app.destroy();
    assert.equal(app.scanner.resumes, 0);
});
