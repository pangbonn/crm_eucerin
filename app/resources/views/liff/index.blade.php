<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>BT Eucerin CRM</title>
    <link rel="stylesheet" href="{{ asset('css/liff.css') }}">
    <script>
        window.APP_URL = "{{ config('app.url') }}";
        window.LIFF_ID = "{{ config('services.line.liff_id', '') }}";
    </script>
</head>
<body>
    <div id="liff-app"></div>

    {{-- LINE LIFF SDK --}}
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script src="{{ asset('js/liff.js') }}"></script>
</body>
</html>
