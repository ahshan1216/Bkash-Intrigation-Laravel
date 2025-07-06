<h1>Payment Successful</h1>
<p>Thank you! Your payment has been processed successfully.</p>

<ul>
    @foreach($data as $key => $value)
        <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
    @endforeach
</ul>
