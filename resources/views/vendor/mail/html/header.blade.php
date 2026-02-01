@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ asset('images/logo-1.png', true) }}" class="logo" alt="{{ config('app.name') }} Logo">
            <div style="margin-top: 10px; color: #1e3a8a; font-size: 14px; font-weight: 900; letter-spacing: 0.2em;">
                {{ config('app.name') }}</div>
        </a>
    </td>
</tr>