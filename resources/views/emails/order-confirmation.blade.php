<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação do Pedido</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .order-number {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .order-date {
            color: #6c757d;
            font-size: 14px;
        }
        
        .customer-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .customer-details h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .customer-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }
        
        .customer-info span {
            font-weight: 600;
            color: #495057;
        }
        
        .products-section {
            margin-bottom: 30px;
        }
        
        .products-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .product-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .product-table th {
            background-color: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        
        .product-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        
        .product-table tr:last-child td {
            border-bottom: none;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .product-price {
            color: #667eea;
            font-weight: 600;
        }
        
        .product-quantity {
            text-align: center;
            background-color: #e9ecef;
            border-radius: 4px;
            padding: 4px 8px;
            font-weight: 600;
        }
        
        .total-section {
            background-color: #667eea;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: right;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: 700;
        }
        
        .action-button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            transition: background-color 0.3s;
        }
        
        .action-button:hover {
            background-color: #5a6fd8;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .thank-you {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-top: 20px;
        }
        
        @media (max-width: 600px) {
            .customer-info {
                grid-template-columns: 1fr;
            }
            
            .product-table {
                font-size: 12px;
            }
            
            .product-table th,
            .product-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🍕 Pastelaria Deliciosa</h1>
            <p>Seu pedido foi confirmado!</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, Sr(a). {{ $order->customer->nome }}!
            </div>

            <div class="order-info">
                <div class="order-number">Pedido #{{ $order->id }}</div>
                <div class="order-date">Data: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div class="customer-details">
                <h3>📋 Dados do Cliente</h3>
                <div class="customer-info">
                    <div><span>Nome:</span> {{ $order->customer->nome }}</div>
                    <div><span>Email:</span> {{ $order->customer->email }}</div>
                    <div><span>Telefone:</span> {{ $order->customer->telefone }}</div>
                    <div><span>CEP:</span> {{ $order->customer->cep }}</div>
                    <div><span>Endereço:</span> {{ $order->customer->endereco }}, {{ $order->customer->bairro }}</div>
                    @if($order->customer->complemento)
                        <div><span>Complemento:</span> {{ $order->customer->complemento }}</div>
                    @endif
                </div>
            </div>

            <div class="products-section">
                <h3>🛒 Produtos do Pedido</h3>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->products as $product)
                            <tr>
                                <td class="product-name">{{ $product->nome }}</td>
                                <td class="product-quantity">1</td>
                                <td class="product-price">R$ {{ number_format($product->preco, 2, ',', '.') }}</td>
                                <td class="product-price">R$ {{ number_format($product->preco, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="total-amount">
                    Total do Pedido: R$ {{ number_format($order->products->sum('preco'), 2, ',', '.') }}
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p style="margin-bottom: 15px; color: #6c757d;">Você pode acompanhar o status do seu pedido aqui:</p>
                <a href="#" class="action-button">Ver Pedido</a>
            </div>
        </div>

        <div class="footer">
            <p>Obrigado por escolher a Pastelaria Deliciosa!</p>
            <p>Seu pedido está sendo preparado com muito carinho.</p>
            <p>Em caso de dúvidas, entre em contato conosco.</p>
            <div class="thank-you">Obrigado.</div>
        </div>
    </div>
</body>
</html> 