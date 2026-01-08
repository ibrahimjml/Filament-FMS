<x-filament-panels::page>
  <div class="max-w-4xl mx-auto bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8">

    {{-- Header --}}
    <div class="flex justify-between items-start border-b border-gray-200 dark:border-gray-700 pb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
          Invoice
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Invoice #{{ $invoice->invoice_number }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Issue Date: {{ $invoice->issue_date->format('Y - m - d') }}
        </p>
      </div>

      <img src="{{ $invoice->setting?->logo ?? asset('logo.jpg') }}" alt="Company Logo" class="h-16 object-contain" />

    </div>

    {{-- Billing To Information --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <div>
        <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">
          Billed From
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client->full_name }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client->client_phone }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client->email }}</p>
      </div>
   {{-- Billing From Information --}}
      <div class="md:text-right">
        <h2 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">
          {{$invoice->setting_snapshot['company_name']}}
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
          {{$invoice->setting_snapshot['company_phone']}}
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400">
          {{$invoice->setting_snapshot['company_address']}}
        </p>
      
        <p class="text-sm text-gray-600 dark:text-gray-400">
          {{$invoice->setting_snapshot['company_email']}}
        </p>
      </div>
    </div>

    {{-- Invoice Items --}}
    <div class="overflow-x-auto mt-8">
      <table class="w-full border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <thead class="bg-gray-100 dark:bg-gray-800">
          <tr>
            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
              Description
            </th>
            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-200">
              Category
            </th>
            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-200">
              Price
            </th>
            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-200">
              Paid
            </th>
            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700 dark:text-gray-200">
              Total
            </th>
          </tr>
        </thead>

        <tbody>
          @foreach ($invoice->payments as $payment)
          
          <tr class="border-t border-gray-200 dark:border-gray-700">
            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
              {!!  $payment->description ?? 'N/A' !!}
            </td>
            <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">
              {{ $invoice->income->subcategory?->category?->name . ' - ' . $invoice->income->subcategory?->name }}
            </td>
            <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">
              {{ $invoice->income->amount }}
            </td>
            <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">
              {{ $payment->payment_amount }}
            </td>
            <td class="px-4 py-2 text-sm text-right text-gray-600 dark:text-gray-400">
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
        <tfoot class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <td colspan="4" class="px-4 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">
              {{$invoice->income->final_amount > 0 ? 'subtotal after discount' : 'subtotal'}}
            </td>
            <td class="px-4 py-2 text-right text-gray-700 dark:text-gray-200">
              $ {{ number_format($finalamount, 2) }}
            </td>
          </tr>
          <tr>
            <td colspan="4" class="px-4 py-2 text-right font-semibold text-gray-700 dark:text-gray-200">
              total paid
            </td>
            <td class="px-4 py-2 text-right text-gray-700 dark:text-gray-200">
              $ {{ number_format($paymentAmount, 2) }}
            </td>
          </tr>
          <tr>
            <td colspan="5" class="px-4 py-2 text-right font-semibold border-t border-t-gray-600 w-fit text-gray-800 dark:text-gray-100">
                $ {{ number_format($paymentAmount,2)}}
            </td>
    
          </tr>
        </tfoot>
      </table>
    </div>

    {{-- Footer --}}
  <div class="mt-8 flex justify-between items-start text-sm text-gray-500 dark:text-gray-400">
  <!-- Left content -->
  <div class="text-left">
    <p>
      <p>{!! nl2br(e($invoice->setting_snapshot['footer'])) !!}</p>
      <span class="font-medium text-gray-700 dark:text-gray-300">
        {{ $invoice->setting_snapshot['company_email'] }}
      </span>
    </p>
  </div>
  <!-- Right status -->
  <p class="border-2 border-red-400 p-4 -rotate-45 whitespace-nowrap">
    {{ $invoice->status->getLabel() }}
  </p>
</div>
  </div>
</x-filament-panels::page>