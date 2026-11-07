<?php

namespace App\Services\Central;

use App\Models\Tenant;
use App\Models\Domain;
use App\Models\Tenant\User;
use App\Models\Tenant\Service;
use App\Models\Tenant\Resource;
use App\Models\Tenant\Label;
use App\Models\Tenant\MessageTemplate;
use App\Models\Tenant\Setting;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Appointment;
use App\Models\Tenant\RecordFieldTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TenantProvisioningService
{
    /**
     * Provision a new tenant and seed starter data.
     */
    public function provision(array $data): Tenant
    {
        // 1. Create tenant row in central DB
        $tenant = Tenant::create([
            'id' => $data['subdomain'], // using subdomain as the unique tenant ID
            'name' => $data['name'],
            'business_type' => $data['business_type'], // salon, clinic, gym, other
            'plan' => $data['plan'] ?? 'starter',
            'locale' => $data['locale'] ?? 'fr',
            'timezone' => $data['timezone'] ?? 'Africa/Casablanca',
            'status' => 'active',
            'trial_ends_at' => now()->addMonth(),
            'receipt_status' => 'none',
        ]);

        // 2. Map domain
        $domainName = $data['subdomain'] . '.' . ($data['parent_domain'] ?? 'rdvpro.ma');
        Domain::create([
            'domain' => $domainName,
            'tenant_id' => $tenant->id,
        ]);

        // If custom domain is provided
        if (!empty($data['custom_domain'])) {
            Domain::create([
                'domain' => $data['custom_domain'],
                'tenant_id' => $tenant->id,
            ]);
        }

        // 3. Seed default data inside the tenant database
        // Stancl Tenancy provides a run() helper to run a callback in the context of the tenant database.
        $tenant->run(function () use ($data) {
            $this->seedTenantDefaultData($data);
        });

        return $tenant;
    }

    /**
     * Seed starter tables inside the tenant database context.
     */
    private function seedTenantDefaultData(array $data): void
    {
        // Create Tenant Admin User
        $admin = User::create([
            'name' => $data['admin_name'] ?? 'Admin ' . $data['name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
        ]);

        // Seed settings
        Setting::create([
            'key' => 'booking_rules',
            'value' => [
                'require_confirmation' => true,
                'allow_self_booking' => true,
                'deposit_on_vip' => false,
            ]
        ]);

        Setting::create([
            'key' => 'business_profile',
            'value' => [
                'name' => $data['name'],
                'business_type' => $data['business_type'],
                'phone' => $data['admin_phone'] ?? '',
                'address' => '',
            ]
        ]);

        // Define standard working hours for resources
        $standardWorkingHours = [
            'Monday' => ['open' => '09:00', 'close' => '20:00', 'active' => true],
            'Tuesday' => ['open' => '09:00', 'close' => '20:00', 'active' => true],
            'Wednesday' => ['open' => '09:00', 'close' => '20:00', 'active' => true],
            'Thursday' => ['open' => '09:00', 'close' => '20:00', 'active' => true],
            'Friday' => ['open' => '09:00', 'close' => '20:00', 'active' => true],
            'Saturday' => ['open' => '09:00', 'close' => '20:00', 'active' => true],
            'Sunday' => ['open' => '09:00', 'close' => '20:00', 'active' => false],
        ];

        // Seed according to business type
        $type = $data['business_type'];

        // Starter Labels
        $labels = [];
        if ($type === 'clinic') {
            $labels = [
                ['name' => 'VIP', 'color' => '#c2683f'],
                ['name' => 'Nouveau', 'color' => '#d4a017'],
                ['name' => 'Suivi', 'color' => '#3d5a3d'],
                ['name' => 'Urgent', 'color' => '#dc2626'],
            ];
        } elseif ($type === 'gym') {
            $labels = [
                ['name' => 'VIP', 'color' => '#c2683f'],
                ['name' => 'Abonné Actif', 'color' => '#3d5a3d'],
                ['name' => 'Essai', 'color' => '#d4a017'],
                ['name' => 'Inactif', 'color' => '#8a8273'],
            ];
        } else { // default to salon/barber
            $labels = [
                ['name' => 'VIP', 'color' => '#c2683f'],
                ['name' => 'Régulier', 'color' => '#3d5a3d'],
                ['name' => 'Nouveau', 'color' => '#d4a017'],
                ['name' => 'À risque', 'color' => '#8a8273'],
            ];
        }

        foreach ($labels as $lbl) {
            Label::create($lbl);
        }

        // Starter Services & Resources
        if ($type === 'clinic') {
            $s1 = Service::create(['name' => 'Consultation Générale', 'duration_minutes' => 20, 'price' => 200.00, 'requires_deposit' => false]);
            $s2 = Service::create(['name' => 'Détartrage', 'duration_minutes' => 30, 'price' => 300.00, 'requires_deposit' => false]);
            $s3 = Service::create(['name' => 'Radiographie Dentaire', 'duration_minutes' => 15, 'price' => 150.00, 'requires_deposit' => false]);
            $s4 = Service::create(['name' => 'Urgence Dentaire', 'duration_minutes' => 45, 'price' => 500.00, 'requires_deposit' => true]);

            $r1 = Resource::create(['name' => 'Dr. Alami', 'role' => 'Dentiste', 'color' => '#3d5a3d', 'working_hours' => $standardWorkingHours]);
            $r2 = Resource::create(['name' => 'Dr. Bennani', 'role' => 'Orthodontiste', 'color' => '#52734f', 'working_hours' => $standardWorkingHours]);

            $r1->services()->attach([$s1->id, $s2->id, $s4->id]);
            $r2->services()->attach([$s1->id, $s3->id, $s4->id]);

        } elseif ($type === 'gym') {
            $s1 = Service::create(['name' => 'Personal Training', 'duration_minutes' => 60, 'price' => 300.00, 'requires_deposit' => true]);
            $s2 = Service::create(['name' => 'Bilan de Forme', 'duration_minutes' => 45, 'price' => 150.00, 'requires_deposit' => false]);
            $s3 = Service::create(['name' => 'Coaching Groupe', 'duration_minutes' => 60, 'price' => 100.00, 'requires_deposit' => false]);
            $s4 = Service::create(['name' => 'Consultation Nutrition', 'duration_minutes' => 30, 'price' => 200.00, 'requires_deposit' => false]);

            $r1 = Resource::create(['name' => 'Coach Simo', 'role' => 'Entraîneur', 'color' => '#3d5a3d', 'working_hours' => $standardWorkingHours]);
            $r2 = Resource::create(['name' => 'Coach Leila', 'role' => 'Prof de Yoga', 'color' => '#52734f', 'working_hours' => $standardWorkingHours]);

            $r1->services()->attach([$s1->id, $s2->id, $s3->id]);
            $r2->services()->attach([$s2->id, $s4->id]);

        } else { // default to salon/barber
            $s1 = Service::create(['name' => 'Coupe de cheveux', 'duration_minutes' => 30, 'price' => 80.00, 'requires_deposit' => false]);
            $s2 = Service::create(['name' => 'Taille de barbe', 'duration_minutes' => 15, 'price' => 40.00, 'requires_deposit' => false]);
            $s3 = Service::create(['name' => 'Coupe + Couleur', 'duration_minutes' => 90, 'price' => 250.00, 'requires_deposit' => true]);
            $s4 = Service::create(['name' => 'Brushing', 'duration_minutes' => 30, 'price' => 150.00, 'requires_deposit' => false]);

            $r1 = Resource::create(['name' => 'Karim', 'role' => 'Coiffeur', 'color' => '#3d5a3d', 'working_hours' => $standardWorkingHours]);
            $r2 = Resource::create(['name' => 'Nadia', 'role' => 'Styliste', 'color' => '#52734f', 'working_hours' => $standardWorkingHours]);

            $r1->services()->attach([$s1->id, $s2->id, $s3->id]);
            $r2->services()->attach([$s1->id, $s3->id, $s4->id]);
        }

        // Message Templates
        $templates = [
            [
                'trigger' => 'booking_confirmed',
                'channel' => 'whatsapp',
                'subject' => null,
                'body' => "Salam {name} 🌟\nVotre rendez-vous chez {business} est confirmé:\n📅 {date} à {time}\n{service} avec {staff}\n\nRépondez par *OUI* pour confirmer ou *NON* pour annuler."
            ],
            [
                'trigger' => 'booking_confirmed',
                'channel' => 'email',
                'subject' => "Confirmation de votre rendez-vous - {business}",
                'body' => "Bonjour {name},\n\nVotre rendez-vous pour la prestation {service} avec {staff} le {date} à {time} a été confirmé par l'établissement {business}.\n\nMerci pour votre confiance !"
            ],
            [
                'trigger' => 'reminder_24h',
                'channel' => 'whatsapp',
                'subject' => null,
                'body' => "Salam {name} 👋\nRappel de votre rendez-vous demain chez {business}:\n📅 {date} à {time}\n{service} avec {staff}\n\nNous avons hâte de vous accueillir !"
            ],
            [
                'trigger' => 'reminder_24h',
                'channel' => 'email',
                'subject' => "Rappel de votre rendez-vous de demain - {business}",
                'body' => "Bonjour {name},\n\nCeci est un rappel amical pour votre rendez-vous de demain chez {business} pour la prestation {service} avec {staff} à {time}.\n\nÀ très bientôt !"
            ],
            [
                'trigger' => 'win_back',
                'channel' => 'whatsapp',
                'subject' => null,
                'body' => "Salam {name}! Vous nous manquez chez {business} ❤️\nCela fait un moment que nous ne vous avons pas vu. Réservez votre prochain rendez-vous aujourd'hui et profitez d'une offre spéciale !\nÀ très vite !"
            ],
            [
                'trigger' => 'win_back',
                'channel' => 'email',
                'subject' => "Vous nous manquez chez {business} !",
                'body' => "Bonjour {name},\n\nCela fait un moment que nous n'avons pas eu le plaisir de vous accueillir chez {business}. Nous serions ravis de vous revoir. Réservez votre prochaine séance en ligne !\n\nÀ bientôt !"
            ],
            [
                'trigger' => 'thank_you_review',
                'channel' => 'whatsapp',
                'subject' => null,
                'body' => "Salam {name} 🙏 Merci pour votre visite chez {business} aujourd'hui ! Nous espérons que vous avez apprécié votre {service}.\nPrenez un instant pour nous laisser votre avis ici: https://g.page/rdvpro/review\nExcellente journée !"
            ],
            [
                'trigger' => 'thank_you_review',
                'channel' => 'email',
                'subject' => "Merci pour votre visite chez {business} !",
                'body' => "Bonjour {name},\n\nMerci d'avoir choisi {business} aujourd'hui. Nous espérons que votre prestation {service} s'est bien passée. Partagez votre avis sur notre page: https://g.page/rdvpro/review\n\nBonne journée !"
            ],
        ];

        foreach ($templates as $tpl) {
            MessageTemplate::create($tpl);
        }

        // Seed some starter Customers
        $c1 = Customer::create([
            'name' => 'Ahmed Alami',
            'phone' => '+212 661 11 22 33',
            'email' => 'ahmed@alami.ma',
            'preferred_channel' => 'whatsapp',
        ]);
        $c2 = Customer::create([
            'name' => 'Yassine Mansouri',
            'phone' => '+212 662 44 55 66',
            'email' => 'yassine@mansouri.ma',
            'preferred_channel' => 'whatsapp',
        ]);
        $c3 = Customer::create([
            'name' => 'Fatima Zahra',
            'phone' => '+212 663 77 88 99',
            'email' => 'fatima@zahra.ma',
            'preferred_channel' => 'email',
        ]);
        
        // Attach VIP label to Fatima Zahra, Régulier to Ahmed
        $vipLabel = Label::where('name', 'VIP')->first();
        $regLabel = Label::where('name', 'Régulier')->first();
        if ($vipLabel) $c3->labels()->attach($vipLabel->id);
        if ($regLabel) $c1->labels()->attach($regLabel->id);

        // Seed some starter Appointments for the Demo Tenant
        // Ahmed Alami: Today 10:00 to 10:30, Coupe de cheveux with Karim (confirmed)
        Appointment::create([
            'customer_id' => $c1->id,
            'service_id' => $s1->id, // Coupe de cheveux
            'resource_id' => $r1->id, // Karim
            'starts_at' => Carbon::today()->setHour(10)->setMinute(0),
            'ends_at' => Carbon::today()->setHour(10)->setMinute(30),
            'status' => 'confirmed',
            'price' => $s1->price,
            'note' => 'Première coupe chez nous',
        ]);

        // Ahmed Alami: Tomorrow 14:00 to 14:15, Taille de barbe with Karim (upcoming confirmed)
        Appointment::create([
            'customer_id' => $c1->id,
            'service_id' => $s2->id, // Taille de barbe
            'resource_id' => $r1->id, // Karim
            'starts_at' => Carbon::tomorrow()->setHour(14)->setMinute(0),
            'ends_at' => Carbon::tomorrow()->setHour(14)->setMinute(15),
            'status' => 'confirmed',
            'price' => $s2->price,
            'note' => 'Barbe sculptée moyenne',
        ]);

        // Ahmed Alami: Yesterday 09:30 to 10:00, Coupe de cheveux with Karim (completed)
        Appointment::create([
            'customer_id' => $c1->id,
            'service_id' => $s1->id, // Coupe de cheveux
            'resource_id' => $r1->id, // Karim
            'starts_at' => Carbon::yesterday()->setHour(9)->setMinute(30),
            'ends_at' => Carbon::yesterday()->setHour(10)->setMinute(0),
            'status' => 'completed',
            'price' => $s1->price,
            'note' => 'Très satisfait, Coupe classique ciseaux',
        ]);

        // Ahmed Alami: 7 days ago 16:00 to 17:30, Coupe + Couleur with Nadia (completed)
        Appointment::create([
            'customer_id' => $c1->id,
            'service_id' => $s3->id, // Coupe + Couleur
            'resource_id' => $r2->id, // Nadia
            'starts_at' => Carbon::today()->subDays(7)->setHour(16)->setMinute(0),
            'ends_at' => Carbon::today()->subDays(7)->setHour(17)->setMinute(30),
            'status' => 'completed',
            'price' => $s3->price,
            'note' => 'Balayage blond miel léger',
        ]);

        // Ahmed Alami: 10 days ago 11:00 to 11:30, Coupe de cheveux with Karim (no-show)
        Appointment::create([
            'customer_id' => $c1->id,
            'service_id' => $s1->id, // Coupe de cheveux
            'resource_id' => $r1->id, // Karim
            'starts_at' => Carbon::today()->subDays(10)->setHour(11)->setMinute(0),
            'ends_at' => Carbon::today()->subDays(10)->setHour(11)->setMinute(30),
            'status' => 'no_show',
            'price' => $s1->price,
            'note' => 'Absent sans prévenir',
        ]);

        // Ahmed Alami: 14 days ago 15:00 to 15:30, Coupe de cheveux with Nadia (cancelled)
        Appointment::create([
            'customer_id' => $c1->id,
            'service_id' => $s1->id, // Coupe de cheveux
            'resource_id' => $r2->id, // Nadia
            'starts_at' => Carbon::today()->subDays(14)->setHour(15)->setMinute(0),
            'ends_at' => Carbon::today()->subDays(14)->setHour(15)->setMinute(30),
            'status' => 'cancelled',
            'price' => $s1->price,
            'note' => 'Annulé le matin par téléphone',
        ]);

        // Yassine Mansouri: Today 11:00 to 11:20, Taille de barbe with Karim
        Appointment::create([
            'customer_id' => $c2->id,
            'service_id' => $s2->id, // Taille de barbe
            'resource_id' => $r1->id, // Karim
            'starts_at' => Carbon::today()->setHour(11)->setMinute(0),
            'ends_at' => Carbon::today()->setHour(11)->setMinute(20),
            'status' => 'confirmed',
            'price' => $s2->price,
            'note' => 'Demande une serviette chaude',
        ]);

        // Fatima Zahra: Today 14:00 to 15:30, Coupe + Couleur with Nadia
        Appointment::create([
            'customer_id' => $c3->id,
            'service_id' => $s3->id, // Coupe + Couleur
            'resource_id' => $r2->id, // Nadia
            'starts_at' => Carbon::today()->setHour(14)->setMinute(0),
            'ends_at' => Carbon::today()->setHour(15)->setMinute(30),
            'status' => 'confirmed',
            'price' => $s3->price,
            'note' => 'Couleur marron chocolat',
        ]);

        // Seed Default Record Field Template based on business type
        $fieldDefinitions = [];
        if ($type === 'clinic') {
            $fieldDefinitions = [
                ['name' => 'diagnosis', 'label' => 'Diagnostic', 'type' => 'long_text', 'required' => true],
                ['name' => 'treatment', 'label' => 'Traitement', 'type' => 'long_text', 'required' => false],
                ['name' => 'prescription', 'label' => 'Ordonnance & Prescriptions', 'type' => 'long_text', 'required' => false],
            ];
        } elseif ($type === 'gym') {
            $fieldDefinitions = [
                ['name' => 'goals', 'label' => 'Objectifs de la session', 'type' => 'long_text', 'required' => true],
                ['name' => 'metrics', 'label' => 'Mensurations & Poids (kg)', 'type' => 'text', 'required' => false],
                ['name' => 'routine', 'label' => 'Exercices prescrits', 'type' => 'long_text', 'required' => false],
            ];
        } elseif ($type === 'salon') {
            $fieldDefinitions = [
                ['name' => 'preferred_style', 'label' => 'Style de coupe préféré', 'type' => 'text', 'required' => true],
                ['name' => 'products_used', 'label' => 'Produits et Shampoing utilisés', 'type' => 'text', 'required' => false],
                ['name' => 'formula', 'label' => 'Formule technique (Couleur/Dosage)', 'type' => 'long_text', 'required' => false],
            ];
        } else {
            $fieldDefinitions = [
                ['name' => 'subject', 'label' => 'Sujet / Motif', 'type' => 'text', 'required' => true],
                ['name' => 'details', 'label' => 'Détails & Compte-rendu', 'type' => 'long_text', 'required' => false],
            ];
        }

        RecordFieldTemplate::create([
            'business_type' => $type,
            'field_definitions' => $fieldDefinitions,
        ]);
    }
}
