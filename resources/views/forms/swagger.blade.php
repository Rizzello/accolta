<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->name }} - Swagger UI</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
</head>
<body>
    <main>
        <header>
            <h1>{{ $form->name }}</h1>
            <p>
                Endpoint submit:
                <code>{{ $submitUrl }}</code>
            </p>
            <p>
                OpenAPI JSON:
                <a href="{{ $openApiUrl }}">{{ $openApiUrl }}</a>
            </p>
        </header>

        <div
            id="swagger-ui"
            data-openapi-url="{{ $openApiUrl }}"
        ></div>
    </main>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="{{ asset('js/swagger-form.js') }}"></script>
</body>
</html>
