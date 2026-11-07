<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport des Rendez-vous - RDVPro</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a1410;
            margin: 0;
            padding: 20px;
            font-size: 13px;
        }
        .header {
            border-bottom: 2px solid #3d5a3d;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #3d5a3d;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #8a8273;
        }
        .stats {
            margin-bottom: 30px;
            width: 100%;
        }
        .stats td {
            background-color: #f7f3ec;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e4ddd0;
            width: 25%;
        }
        .stats .value {
            font-size: 20px;
            font-weight: bold;
            color: #c2683f;
            margin-top: 5px;
        }
        .stats .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #8a8273;
            letter-spacing: 0.5px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data-table th, table.data-table td {
            padding: 10px;
            border-bottom: 1px solid #e4ddd0;
            text-align: left;
        }
        table.data-table th {
            background-color: #f7f3ec;
            color: #1a1410;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-confirmed { background-color: #e2f0d9; color: #3d5a3d; }
        .status-pending { background-color: #fff3cd; color: #d4a017; }
        .status-completed { background-color: #d1e7dd; color: #198754; }
        .status-no_show { background-color: #f8d7da; color: #842029; }
        .status-cancelled { background-color: #e2e3e5; color: #41464b; }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #8a8273;
            border-top: 1px solid #e4ddd0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ tenant('name') }}</h1>
        <p>Rapport d'activité des réservations · Édité le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table class="stats">
        <tr>
            <td>
                <span class="label">Rendez-vous du jour</span>
                <div class="value">{{ $metrics['todays_bookings'] }}</div>
            </td>
            <td>
                <span class="label">Revenus du jour</span>
                <div class="value">{{ number_format($metrics['todays_revenue'], 2) }} DH</div>
            </td>
            <td>
                <span class="label">Taux d'absence (30j)</span>
                <div class="value">{{ $metrics['no_show_rate'] }}%</div>
            </td>
            <td>
                <span class="label">Total Clients</span>
                <div class="value">{{ $metrics['total_customers'] }}</div>
            </td>
        </tr>
    </table>

    <h3>Liste des Réservations</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Client</th>
                <th>Prestation</th>
                <th>Personnel</th>
                <th>Tarif</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $app)
                <tr>
                    <td>
                        <strong>{{ $app->starts_at->format('d/m/Y') }}</strong><br>
                        <small>{{ $app->starts_at->format('H:i') }} - {{ $app->ends_at->format('H:i') }}</small>
                    </td>
                    <td>
                        {{ $app->customer->name ?? 'Client inconnu' }}<br>
                        <small>{{ $app->customer->phone ?? '' }}</small>
                    </td>
                    <td>{{ $app->service->name ?? '' }}</td>
                    <td>{{ $app->resource->name ?? '' }}</td>
                    <td>{{ number_format($app->price, 2) }} MAD</td>
                    <td>
                        <span class="status-badge 
                            @if($app->status === 'confirmed') status-confirmed
                            @elseif($app->status === 'pending') status-pending
                            @elseif($app->status === 'completed') status-completed
                            @elseif($app->status === 'no_show') status-no_show
                            @else status-cancelled
                            @endif">
                            {{ ucfirst($app->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #8a8273;">Aucun rendez-vous enregistré.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement par l'application RDVPro SaaS.
    </div>
</body>
</html>
