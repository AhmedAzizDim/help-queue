<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Help Queue')</title>
<style>
body { font-family: system-ui, sans-serif; max-width: 760px; margin: 40px auto;
padding: 0 16px; color: #1f2933; }

header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 12px; margin-bottom: 24px; }

header a { text-decoration: none; color: inherit; font-weight: 700; }

.ticket { border: 1px solid #ddd; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; }

.topic { background: #eef2ff; border-radius: 99px; padding: 2px 10px; font-size: 14px;
margin-left: 8px; }
</style>
</head>
<body>
<header>
<a href="{{ route('tickets.index') }}">Help Queue</a>
</header>
<main>
@yield('content')
</main>
</body>
</html>
