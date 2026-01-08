<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>

<body>
  <div
    style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 800px; margin: 0 auto;">
    <!-- Header -->
    <table style="width: 100%; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
      <tr>
        <td>
          <h1 style="font-size: 24px; font-weight: bold; color: #4a5568;">Invoice</h1>
          <p style="color: #a0aec0;">Invoice #{{ $invoice->invoice_number }}</p>
          <p style="color: #a0aec0;">Date: {{ $invoice->issue_date->format('Y - m - d') }}</p>
        </td>
        <td style="text-align: right;">
          <img src="{{ $invoice->setting?->logo ?? asset('logo.jpg') }}" alt="Company Logo"
            style="object-fit: contain; width: 60px; height: 50px;" />
        </td>
      </tr>
    </table>

    <!-- Billing Information -->
    <table style="width: 100%; margin-top: 24px;">
      <tr>
        <td>
          <h2 style="font-weight: bold; color: #4a5568;">Billed From</h2>
          <p style="color: #718096;">{{ $invoice->client->full_name }}</p>
          <p style="color: #718096;">{{ $invoice->client->client_phone }}</p>
          <p style="color: #718096;">{{ $invoice->client->email }}</p>
        </td>
        <td style="text-align: right;">
          <h2 style="font-weight: bold; color: #4a5568;">{{$invoice->setting_snapshot['company_name']}}</h2>
          <p style="color: #718096;">{{$invoice->setting_snapshot['company_phone']}}</p>
          <p style="color: #718096;">{{$invoice->setting_snapshot['company_address']}}</p>
          <p style="color: #718096;">{{$invoice->setting_snapshot['company_email']}}</p>
        </td>
      </tr>
    </table>

    <!-- Invoice Items -->
    <table style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
      <thead>
        <tr style="background-color: #f7fafc;">
          <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: left; color: #4a5568;">Description</th>
          <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Category</th>
          <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Price</th>
          <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Payment</th>
          <th style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">Total</th>
        </tr>
      </thead>
      <tbody>
       @foreach ($invoice->payments as $payment)
        <tr>
          <td style="border: 1px solid #e2e8f0; padding: 8px; color: #718096;">
            {!! $payment->description ?? 'N/A'!!}
          </td>
          <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">
            {{ $invoice->income->subcategory?->category?->name . ' - ' . $invoice->income->subcategory?->name }}
          </td>
          <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">$
            {{ $invoice->income->amount }}
          </td>
          <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">$
            {{ $payment->payment_amount }}
          </td>
          <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #718096;">$
            {{ $payment->payment_amount }}
          </td>
        </tr>
       @endforeach
      </tbody>
      @php
          $paymentAmount = $invoice->payments?->sum('payment_amount') ?? 0;
          $discount = $invoice->income->discount_amount ?? 0;
          $finalamount =  $invoice->income->final_amount > 0
                     ? $invoice->income->final_amount 
                     : $invocie->income->amount;
      @endphp
      <tfoot>
        <tr style="background-color: #f7fafc;">
          <td colspan="4"
            style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
            {{$invoice->income->final_amount > 0 ? 'subtotal after discount' : 'subtotal'}}
          </td>
          <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">
            $ {{ number_format($finalamount, 2) }}
          </td>
        </tr>
        <tr style="background-color: #f7fafc;">
          <td colspan="4"
            style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">Paid
          </td>
          <td style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; color: #4a5568;">
            $ {{ number_format($paymentAmount, 2) }}
          </td>
        </tr>
        <tr>
          <td colspan="5"
            style="border: 1px solid #e2e8f0; padding: 8px; text-align: right; font-weight: bold; color: #4a5568;">
            $ {{ number_format($paymentAmount,2)}}
          </td>
        </tr>
      </tfoot>
    </table>

    <!-- Footer -->
    <div style="margin-top: 24px;display: flex;justify-content: space-between;align-items: flex-start;color: #a0aec0;">
      <!-- Left -->
      <div style="text-align: left; width: 70%;">
        <p>
          {!! nl2br(e($invoice->setting_snapshot['footer'])) !!}<br>
          <span style="font-weight: 500; color: #4a5568;">
            {{ $invoice->setting_snapshot['company_email'] }}
          </span>
        </p>
      </div>

      <!-- Right -->
      <div style="width: 30%; text-align: right;margin-block: 20px 0;">
        <div style="display: inline-block;border: 2px solid red;padding: 7px;transform: rotate(-45deg);transform-origin: center;">
          {{ $invoice->status->getLabel() }}
        </div>
      </div>
    </div>
  </div>
</body>

</html>