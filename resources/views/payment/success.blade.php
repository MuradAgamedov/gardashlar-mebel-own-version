<!doctype html>
<html lang="az">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ödəniş uğurla tamamlandı</title>
  <style>
    body {
      margin:0;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: #0f172a;
      color: #f1f5f9;
      display:flex;
      justify-content:center;
      align-items:center;
      min-height:100vh;
    }
    .card {
      background:#1e293b;
      border:1px solid #334155;
      border-radius:16px;
      padding:30px;
      max-width:480px;
      width:100%;
      box-shadow:0 10px 30px rgba(0,0,0,.4);
    }
    h1 {
      font-size:20px;
      margin-bottom:20px;
      color:#22c55e;
    }
    .row {
      display:flex;
      justify-content:space-between;
      padding:10px 0;
      border-bottom:1px solid rgba(255,255,255,.05);
    }
    .row:last-child {
      border-bottom:none;
    }
    .label {
      color:#94a3b8;
      font-size:14px;
    }
    .value {
      font-weight:600;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Ödəniş uğurla tamamlandı ✅</h1>

    <div class="row">
      <div class="label">Sifariş №</div>
      <div class="value" id="orderId">12345</div>
    </div>

    <div class="row">
      <div class="label">Status</div>
      <div class="value" id="status">APPROVED</div>
    </div>

    <div class="row">
      <div class="label">Əməliyyat №</div>
      <div class="value" id="transaction">39918</div>
    </div>

    <div class="row">
      <div class="label">Kart</div>
      <div class="value" id="cardMask">416974XXXXXX1778</div>
    </div>

    <div class="row">
      <div class="label">Ətraflı</div>
      <div class="value" id="desc">Approved, no balances available</div>
    </div>

    <div class="row">
      <div class="label">Məbləğ</div>
      <div class="value" id="amount">50.00 AZN</div>
    </div>
  </div>

  <!-- JS ilə `up` parametri decode edib dəyərləri doldura bilərsən -->
  <script>
    const params = new URLSearchParams(location.search);
    const up = params.get('up');
    if(up){
      try {
        const data = JSON.parse(atob(up));
        document.getElementById('orderId').textContent = data.OrderId ?? '—';
        document.getElementById('status').textContent = data.Status ?? '—';
        document.getElementById('transaction').textContent = data.Transaction ?? '—';
        document.getElementById('cardMask').textContent = data.CardMask ?? '—';
        document.getElementById('desc').textContent = data.ExternalStatusDesc ?? '—';
        document.getElementById('amount').textContent = data.Amount ? data.Amount + ' AZN' : '—';
      } catch(e){ console.error("UP parse error", e); }
    }
  </script>
</body>
</html>
