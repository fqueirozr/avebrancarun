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
        $pathfinders = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                $values = $row->toArray();
                $name = trim((string) ($values[0] ?? ''));
                $cpf = preg_replace('/\D+/', '', (string) ($values[1] ?? ''));

                if ($index === 1 && mb_strtolower($name) === 'nome') {
                    continue;
                }

                if ($name !== '' || $cpf !== '') {
                    if ($name === '' || strlen($cpf) !== 11) {
                        throw ValidationException::withMessages([
                            'file' => "A linha {$index} deve conter nome e CPF com 11 dígitos.",
                        ]);
                    }

                    $pathfinders[] = [
                        'name' => mb_substr($name, 0, 255),
                        'cpf' => $cpf,
                    ];
                }
            }

            break;
        }

        $reader->close();

        if ($pathfinders === []) {
            throw ValidationException::withMessages(['file' => 'A planilha não contém desbravadores para importar.']);
        }

        return DB::transaction(function () use ($pathfinders): int {
            collect($pathfinders)->unique('cpf')->each(
                fn (array $pathfinder) => Pathfinder::query()->updateOrCreate(
                    ['cpf' => $pathfinder['cpf']],
                    ['name' => $pathfinder['name'], 'is_active' => true],
                ),
            );

            return count($pathfinders);
        });
    }
}
