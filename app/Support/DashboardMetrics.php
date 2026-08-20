<?php

namespace App\Support;

use App\Models\ParticipantRegistration;
use App\Models\ShirtOrder;

class DashboardMetrics
{
    /** @return array{paid: int, under_review: int, pending: int, cancelled: int} */
    public function registrationCounts(): array
    {
        $counts = ParticipantRegistration::query()
            ->toBase()
            ->selectRaw("count(case when payment_status = 'paid' then 1 end) as paid")
            ->selectRaw("count(case when payment_status = 'under_review' then 1 end) as under_review")
            ->selectRaw("count(case when payment_status = 'pending' then 1 end) as pending")
            ->selectRaw("count(case when payment_status = 'cancelled' then 1 end) as cancelled")
            ->first();

        return [
            'paid' => (int) $counts->paid,
            'under_review' => (int) $counts->under_review,
            'pending' => (int) $counts->pending,
            'cancelled' => (int) $counts->cancelled,
        ];
    }

    public function collectedRevenue(): float
    {
        $registrationRevenue = ParticipantRegistration::query()
            ->select(['id', 'kit_id', 'shirt_size'])
            ->with('kit:id,price,has_shirt,size_2xl_surcharge,size_3xl_surcharge')
            ->where('payment_status', 'paid')
            ->lazy()
            ->sum(fn (ParticipantRegistration $registration): float => $registration->kit === null
                ? 0.0
                : (float) $registration->kit->price
                    + ($registration->kit->has_shirt ? $registration->kit->surchargeForSize($registration->shirt_size) : 0.0));

        $shirtOrderRevenue = (float) ShirtOrder::query()
            ->where('payment_status', 'paid')
            ->sum('total_price');

        return round($registrationRevenue + $shirtOrderRevenue, 2);
    }

    /** @return array<string, array{included: int, standalone: int, total: int}> */
    public function shirtCountsBySize(): array
    {
        $counts = collect(ParticipantRegistration::shirtSizeOptions())
            ->map(fn (): array => ['included' => 0, 'standalone' => 0, 'total' => 0]);

        ParticipantRegistration::query()
            ->select(['id', 'kit_id', 'shirt_size'])
            ->with('kit:id,has_shirt')
            ->where('payment_status', '!=', 'cancelled')
            ->whereNotNull('shirt_size')
            ->lazy()
            ->each(function (ParticipantRegistration $registration) use ($counts): void {
                if ($registration->kit?->has_shirt && $counts->has($registration->shirt_size)) {
                    $counts[$registration->shirt_size] = [
                        'included' => $counts[$registration->shirt_size]['included'] + 1,
                        'standalone' => $counts[$registration->shirt_size]['standalone'],
                        'total' => $counts[$registration->shirt_size]['total'] + 1,
                    ];
                }
            });

        ShirtOrder::query()
            ->select(['id', 'size', 'sizes', 'quantity'])
            ->where('payment_status', '!=', 'cancelled')
            ->cursor()
            ->each(function (ShirtOrder $order) use ($counts): void {
                foreach ($order->sizes ?: array_fill(0, $order->quantity, $order->size) as $size) {
                    if (! $counts->has($size)) {
                        continue;
                    }

                    $counts[$size] = [
                        'included' => $counts[$size]['included'],
                        'standalone' => $counts[$size]['standalone'] + 1,
                        'total' => $counts[$size]['total'] + 1,
                    ];
                }
            });

        return $counts->all();
    }
}
