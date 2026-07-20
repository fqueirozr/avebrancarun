<?php

namespace App\Actions;

use App\Models\Pathfinder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenSpout\Reader\Common\Creator\ReaderFactory;

class ImportPathfinders
{
    public function handle(string $path): int
    {
        $reader = ReaderFactory::createFromFileByMimeType($path);
        $reader->open($path);
        $names = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                $name = trim((string) ($row->toArray()[0] ?? ''));

                if ($index === 1 && mb_strtolower($name) === 'nome') {
                    continue;
                }

                if ($name !== '') {
                    $names[] = mb_substr($name, 0, 255);
                }
            }

            break;
        }

        $reader->close();

        if ($names === []) {
            throw ValidationException::withMessages(['file' => 'A planilha não contém nomes para importar.']);
        }

        return DB::transaction(function () use ($names): int {
            collect($names)->unique(fn (string $name): string => mb_strtolower($name))->each(
                fn (string $name) => Pathfinder::query()->firstOrCreate(['name' => $name], ['is_active' => true]),
            );

            return count($names);
        });
    }
}
