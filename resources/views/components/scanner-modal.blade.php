<div id="scannerModal" style="position: fixed; z-index: 1050; display: none; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--card-bg); padding: 1.5rem; border-radius: var(--radius-md); width: 90%; max-width: 500px; box-shadow: var(--shadow-md);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin:0; font-size: 1.1rem; color: var(--text-main);"><i class="fa-solid fa-qrcode"></i> Scan Barcode/QR Kode Unit</h3>
            <button type="button" onclick="closeScanner()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted);"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="reader" style="width: 100%;"></div>
        <p style="font-size: 0.8rem; color: var(--text-muted); text-align: center; margin-top: 1rem;">
            Arahkan kamera ke QR Code atau Barcode pada label aset.
        </p>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner;
    let onScanSuccessCallback = null;

    function openScanner(callback) {
        onScanSuccessCallback = callback;
        document.getElementById('scannerModal').style.display = 'block';
        
        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
            
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function closeScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(error => {
                console.error("Failed to clear html5QrcodeScanner. ", error);
            });
        }
        document.getElementById('scannerModal').style.display = 'none';
    }

    function onScanSuccess(decodedText, decodedResult) {
        closeScanner();
        
        let extractedCode = decodedText;
        let match = decodedText.match(/Kode:\s*([^\n\r]+)/i);
        if (match) {
            extractedCode = match[1].trim();
        } else if (decodedText.includes('\n') || decodedText.includes('\r')) {
            extractedCode = decodedText.split(/[\n\r]+/)[0].trim();
        }
        
        toastr.info('Kode terdeteksi: ' + extractedCode);
        if (typeof onScanSuccessCallback === 'function') {
            onScanSuccessCallback(extractedCode);
        }
    }

    function onScanFailure(error) {
        // ignore continuous failures
    }
</script>
