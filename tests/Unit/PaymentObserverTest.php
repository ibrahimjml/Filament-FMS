<?php

use App\Enums\IncomeStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Income;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class)
 ->beforeEach(function () {
        Payment::observe(\App\Observers\PaymentObserver::class);
    });
function makeIncome(array $overrides = []) : Income
{
    $category = Category::create(['category_name' => 'Test Category']);
    $subcategory = Subcategory::create(['sub_name' => 'Test Sub', 'category_id' => $category->category_id]);

    return Income::create(array_merge([
        'subcategory_id' => $subcategory->subcategory_id,
        'amount' => 100,
        'final_amount' => 100,
        'status' => IncomeStatus::PENDING,
        'payment_type' => 'onetime',
    ], $overrides));
}

it('updates income status on payment creation', function () {
    $income = makeIncome();

    expect($income->status)->toBe(IncomeStatus::PENDING);

    Payment::create([
        'income_id' => $income->income_id,
        'payment_amount' => 100,
        'status' => PaymentStatus::PAID->value,
    ]);

    $income->refresh();

    expect($income->status)->toBe(IncomeStatus::COMPLETED);
});

it('updates income status on payment update', function () {
    $income = makeIncome();

    $payment = Payment::create([
        'income_id' => $income->income_id,
        'payment_amount' => 50,
        'status' => PaymentStatus::UNPAID->value,
    ]);

    $payment->update(['status' => PaymentStatus::PAID->value]);

    $income->refresh();

    expect($income->status)->toBe(IncomeStatus::PARTIAL);
});

it('updates income status on payment delete and restore', function () {
    $income = makeIncome();

    $payment = Payment::create([
        'income_id' => $income->income_id,
        'payment_amount' => 100,
        'status' => PaymentStatus::PAID->value,
    ]);

    $income->refresh();
    expect($income->status)->toBe(IncomeStatus::COMPLETED);

    $payment->delete();
    $income->refresh();
    expect($income->status)->toBe(IncomeStatus::PENDING);

    $payment->restore();
    $income->refresh();
    expect($income->status)->toBe(IncomeStatus::COMPLETED);
});

