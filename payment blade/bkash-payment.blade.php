<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <form action="{{ url('/bkash/create') }}" method="POST">
        @csrf
        <label for="amount">Amount (BDT):</label>
        <input type="number" name="amount" value="10" readonly><br>

        <label for="payerReference">Payer Reference (Phone Number):</label>
        <input type="text" name="payerReference" required><br>

        <button type="submit">Pay with bKash</button>
    </form>
</body>
</html>
