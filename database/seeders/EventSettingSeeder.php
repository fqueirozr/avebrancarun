<?php

namespace Database\Seeders;

use App\Models\EventSetting;
use Illuminate\Database\Seeder;

class EventSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventSetting::query()->firstOrCreate([], [
            'event_date' => 'A confirmar',
            'event_location' => 'A confirmar',
            'contact_email' => 'contato@corridaavebranca.com.br',
            'contact_phone' => null,
            'contact_whatsapp' => null,
            'general_information' => 'Data e local serão confirmados pela organização.',
            'kit_information' => 'Em definição',
            'baggage_storage_information' => 'Serviço e orientações serão confirmados pela organização antes do evento.',
            'start_groups_information' => 'A organização vai orientar os atletas por categoria, idade e distância no dia da prova.',
            'timing_information' => 'As informações de apuração e resultados serão divulgadas nos canais oficiais do evento.',
            'special_registrations_information' => 'Entre em contato com a organização para necessidades específicas ou orientações adicionais.',
            'course_information' => 'Logo o percurso estará disponível para você se preparar.',
            'course_images' => null,
            'regulation' => 'Em revisão',
        ]);
    }
}
