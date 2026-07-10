<?php

namespace App\Actions;

use App\Models\ParticipantRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RecalculateRaceRankings
{
    public function handle(ParticipantRegistration $registration): void
    {
        DB::transaction(function () use ($registration): void {
            $raceRegistrations = fn (): Builder => ParticipantRegistration::query()
                ->when(
                    $registration->race_modality_id !== null,
                    fn (Builder $query): Builder => $query->where('race_modality_id', $registration->race_modality_id),
                    fn (Builder $query): Builder => $query
                        ->whereNull('race_modality_id')
                        ->where('modality', $registration->modality),
                );

            $raceRegistrations()
                ->update([
                    'overall_rank' => null,
                    'sex_rank' => null,
                    'category_rank' => null,
                ]);

            $registrations = $raceRegistrations()
                ->where('result_status', 'finished')
                ->whereNotNull('elapsed_time')
                ->orderBy('elapsed_time')
                ->orderBy('bib_number')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $sexRanks = [];
            $categoryRanks = [];

            foreach ($registrations as $index => $registration) {
                $sexKey = $registration->sex ?? 'not_informed';
                $categoryKey = $registration->result_category ?? 'not_informed';

                $sexRanks[$sexKey] = ($sexRanks[$sexKey] ?? 0) + 1;
                $categoryRanks[$categoryKey] = ($categoryRanks[$categoryKey] ?? 0) + 1;

                $registration->forceFill([
                    'overall_rank' => $index + 1,
                    'sex_rank' => $sexRanks[$sexKey],
                    'category_rank' => $categoryRanks[$categoryKey],
                ])->saveQuietly();
            }
        });
    }
}
