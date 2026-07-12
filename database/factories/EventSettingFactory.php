<?php

namespace Database\Factories;

use App\Models\EventSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventSetting>
 */
class EventSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_date' => '20/09/2026',
            'event_location' => 'Taguaparque - Taguatinga/DF',
            'registration_deadline' => '2026-08-31 23:59:00',
            'max_registrations' => 600,
            'organizer_legal_name' => 'Clube de Desbravadores Ave Branca',
            'organizer_cnpj' => '12.345.678/0001-95',
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->numerify('###########'),
            'contact_whatsapp' => fake()->numerify('###########'),
            'general_information' => fake()->paragraph(),
            'kit_information' => 'A retirada exige documento original com foto e comprovante de pagamento. Não haverá entrega de kits no dia da prova.',
            'baggage_storage_information' => fake()->paragraph(),
            'start_groups_information' => fake()->paragraph(),
            'timing_information' => 'O tempo máximo para as provas de 3 km e 6 km é de 1h30.',
            'special_registrations_information' => fake()->paragraph(),
            'regulation' => '<p>Regulamento – Ave Branca Run 2026</p><p>Data: 20 de setembro de 2026, às 7h30, no Taguaparque.</p>',
        ];
    }
}
