<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview do Email - Pastelaria API</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 40px;
        }

        .preview-section {
            margin-bottom: 30px;
        }

        .preview-section h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .preview-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 10px 10px 0;
            transition: transform 0.2s;
        }

        .preview-button:hover {
            transform: translateY(-2px);
            color: white;
        }

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .info-box h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #6c757d;
            margin: 5px 0;
        }

        .orders-list {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .order-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #e9ecef;
        }

        .order-item:last-child {
            margin-bottom: 0;
        }

        .order-id {
            font-weight: 600;
            color: #667eea;
        }

        .order-customer {
            color: #6c757d;
            font-size: 14px;
        }

        .order-products {
            color: #495057;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🍕 Preview do Email</h1>
            <p>Visualize como o cliente receberá o email de confirmação</p>
        </div>

        <div class="content">

            <div class="preview-section">
                <h2>📧 Visualizar Email</h2>
                <a href="{{ route('email.preview') }}" target="_blank" class="preview-button">
                    👁️ Preview Padrão
                </a>
                @if(isset($orders) && $orders->count() > 0)
                    <a href="{{ route('email.preview') }}?order_id={{ $orders->first()->id }}" target="_blank" class="preview-button">
                        📋 Preview com Pedido #{{ $orders->first()->id }}
                    </a>
                @endif
            </div>

            @if(isset($orders) && $orders->count() > 0)
                <div class="preview-section">
                    <h2>📋 Pedidos Disponíveis</h2>
                    <div class="orders-list">
                        @foreach($orders as $order)
                            <div class="order-item">
                                <div class="order-id">Pedido #{{ $order->id }}</div>
                                <div class="order-customer">{{ $order->customer->nome }} ({{ $order->customer->email }})</div>
                                <div class="order-products">
                                    {{ $order->products->count() }} produto(s) - 
                                    Total: R$ {{ number_format($order->products->sum('preco'), 2, ',', '.') }}
                                </div>
                                <a href="{{ route('email.preview.order', $order->id) }}" target="_blank" class="preview-button" style="margin: 10px 0 0 0; font-size: 12px; padding: 8px 15px;">
                                    Ver Email
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="preview-section">
                <h2>🔗 Links Úteis</h2>
                <a href="/api" target="_blank" class="preview-button">
                    🌐 API Documentation
                </a>
                <a href="/api/products" target="_blank" class="preview-button">
                    🍕 Listar Produtos
                </a>
                <a href="/api/customers" target="_blank" class="preview-button">
                    👥 Listar Clientes
                </a>
                <a href="/api/orders" target="_blank" class="preview-button">
                    📦 Listar Pedidos
                </a>
            </div>
        </div>
    </div>
</body>

</html>