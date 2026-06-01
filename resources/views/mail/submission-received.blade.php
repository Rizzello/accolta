<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Nuova submission</title>
</head>
<body>
    <h1>Nuova submission: {{ $form->name }}</h1>

    <h2>Dati inviati</h2>

    <table cellpadding="8" cellspacing="0" border="1">
        <tbody>
            @foreach ($fields as $field)
                <tr>
                    <th align="left">{{ $field['label'] }}</th>
                    <td>
                        @if (is_bool($field['value']))
                            {{ $field['value'] ? 'Sì' : 'No' }}
                        @elseif ($field['value'] === null)
                            -
                        @else
                            {{ $field['value'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Metadati</h2>

    <table cellpadding="8" cellspacing="0" border="1">
        <tbody>
            <tr>
                <th align="left">Form</th>
                <td>{{ $form->name }}</td>
            </tr>
            <tr>
                <th align="left">Inviata il</th>
                <td>{{ $submission->submitted_at->toDateTimeString() }}</td>
            </tr>
            <tr>
                <th align="left">IP</th>
                <td>{{ $meta['ip'] ?? '-' }}</td>
            </tr>
            <tr>
                <th align="left">Origin</th>
                <td>{{ $meta['origin'] ?? '-' }}</td>
            </tr>
            <tr>
                <th align="left">Referer</th>
                <td>{{ $meta['referer'] ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
