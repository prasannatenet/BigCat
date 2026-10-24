<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Safari booking availability</title>
    <style>
        :root { font-family: Arial, sans-serif; color: #1e2d23; background: #f1eee4; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 32px 16px; background: radial-gradient(ellipse at 8% 8%, #fff8e9 0, transparent 36%), linear-gradient(135deg, #e9e6d8, #f5f5ee 52%, #e2eadf); }
        .page { width: min(900px, 100%); margin: 0 auto; overflow: hidden; border: 1px solid #e2e8df; border-radius: 24px; background: white; box-shadow: 0 28px 80px #253a2b20; }
        header { padding: 32px clamp(22px, 5vw, 40px); background: linear-gradient(125deg, #1d5138, #163b2b); color: white; }
        .eyebrow { margin: 0 0 8px; color: #f0bb77; font-size: 11px; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(25px, 4vw, 34px); letter-spacing: -.035em; }
        header p:last-child { margin: 10px 0 0; color: #d8e6db; font-size: 14px; line-height: 1.5; }
        .meter { height: 7px; margin-top: 18px; overflow: hidden; border-radius: 999px; background: #ffffff30; }
        .meter span { display: block; height: 100%; border-radius: inherit; background: #efad62; }
        .content { padding: 28px clamp(22px, 5vw, 40px) 34px; }
        .summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 11px; margin: 0 0 28px; }
        .detail { min-width: 0; padding: 14px 15px; border: 1px solid #e5ebe3; border-radius: 12px; background: #fbfcfa; }
        .detail-label { display: block; margin-bottom: 5px; color: #788478; font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .detail-value { color: #26392b; font-size: 14px; font-weight: 600; overflow-wrap: anywhere; }
        .coupon-detail { grid-column: 1 / -1; padding: 18px 20px; border-color: #b9dfc1; background: linear-gradient(120deg, #f0fbf1, #fbfef9); }
        .coupon-detail.expired { border-color: #f0d49c; background: linear-gradient(120deg, #fff8e9, #fffdf7); }
        .coupon-detail.unavailable { border-color: #ead7c8; background: linear-gradient(120deg, #fff7f0, #fffdfa); }
        .coupon-detail .detail-label { margin-bottom: 7px; color: #42664b; font-size: 11px; }
        .coupon-detail .detail-value { display: block; color: #26392b; font-size: 15px; line-height: 1.65; }
        .coupon-detail small { display: inline-block; margin-top: 8px; padding: 4px 9px; border-radius: 999px; background: #e4f2e5; color: #386045; font-size: 11px; }
        h2 { margin: 0 0 14px; color: #1d3f2d; font-size: 20px; }
        .date-list { display: grid; gap: 12px; }
        .date-card { padding: 16px; border: 1px solid #e5ebe2; border-radius: 15px; background: #f8faf6; }
        .date-card h3 { margin: 0 0 11px; color: #31583d; font-size: 15px; }
        .slot { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 14px; margin-top: 8px; padding: 13px 14px; border: 1px solid #e4e9e2; border-radius: 11px; background: white; }
        .slot-available { border-color: #26743e; background: #26743e; }
        .slot-unavailable { border-color: #b64037; background: #b64037; }
        .slot-available .slot-name, .slot-available .slot-meta, .slot-unavailable .slot-name, .slot-unavailable .slot-meta { color: white; }
        .slot-available .badge, .slot-unavailable .badge { border: 1px solid #ffffff70; background: #ffffff20; color: white; }
        .slot-name { color: #26392b; font-size: 14px; font-weight: 700; }
        .slot-meta { margin-top: 4px; color: #788278; font-size: 12px; }
        .badge { padding: 7px 11px; border-radius: 999px; font-size: 11px; font-weight: 800; white-space: nowrap; }
        .available { background: #e7f4e8; color: #28603a; }
        .unavailable { background: #fff0eb; color: #a4442b; }
        .notice { margin: 0 0 18px; padding: 13px 15px; border: 1px solid #cae6d0; border-radius: 11px; background: #ecfdf5; color: #166534; font-size: 13px; line-height: 1.5; }
        .availability-help { margin: 20px 0 -10px; padding: 13px 15px; border: 1px solid #f2d9aa; border-radius: 11px; background: #fff8e8; color: #77531d; font-size: 13px; line-height: 1.55; }
        .submit { min-height: 48px; margin-top: 22px; padding: 0 20px; border: 0; border-radius: 10px; background: linear-gradient(100deg, #e97827, #d95c18); box-shadow: 0 8px 18px #df6b2430; color: white; font-size: 13px; font-weight: 800; cursor: pointer; }
        .submit:hover { transform: translateY(-1px); }
        .home-link { display: inline-block; margin: 22px 0 0 9px; color: #46634d; font-size: 13px; font-weight: 700; text-decoration: none; }
        .footer { margin: 20px 0 0; color: #778177; font-size: 12px; line-height: 1.5; }
        .popup-backdrop { position: fixed; inset: 0; z-index: 20; display: grid; place-items: center; padding: 20px; background: #10251dcc; }
        .popup-card { width: min(440px, 100%); padding: 32px; border: 1px solid #e1e9df; border-radius: 20px; background: white; text-align: center; box-shadow: 0 24px 80px #0004; }
        .popup-icon { width: 56px; height: 56px; display: grid; place-items: center; margin: 0 auto 16px; border-radius: 50%; background: #e7f4e8; color: #28603a; font-size: 28px; font-weight: 800; }
        .popup-card h2 { margin: 0; color: #1d3f2d; font-size: 24px; }
        .popup-card p { margin: 10px 0 0; color: #69736b; font-size: 14px; line-height: 1.6; }
        .popup-close { min-height: 44px; margin-top: 23px; padding: 0 24px; border: 0; border-radius: 10px; background: #24543c; color: white; font-size: 13px; font-weight: 800; cursor: pointer; }
        @media (max-width: 650px) { body { padding: 14px 10px; } .summary { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 460px) { .summary { grid-template-columns: 1fr; } .slot { grid-template-columns: 1fr; } .home-link { margin-left: 0; } }
    </style>
</head>
<body>
@php
    $allAvailable = $requestedCount > 0 && $availableCount === $requestedCount;
    $availabilityPercent = $requestedCount > 0 ? (int) round(($availableCount / $requestedCount) * 100) : 0;
@endphp
<main class="page">
    <header>
        <p class="eyebrow">Safari booking request</p>
        <h1>{{ $allAvailable ? 'Your selected slots are available' : 'Your slot availability results' }}</h1>
        <p>{{ $availableCount }} of {{ $requestedCount }} selected visits are available.</p>
        <div class="meter" role="img" aria-label="{{ $availabilityPercent }} percent available"><span style="width: {{ $availabilityPercent }}%"></span></div>
    </header>
    <section class="content">
        <div class="summary">
            <div class="detail"><span class="detail-label">Name</span><span class="detail-value">{{ $bookingRequest->name }}</span></div>
            <div class="detail"><span class="detail-label">National park</span><span class="detail-value">{{ $bookingRequest->nationalPark->name }}</span></div>
            <div class="detail"><span class="detail-label">Safari mode</span><span class="detail-value">{{ $bookingRequest->safari_mode }}</span></div>
            <div class="detail"><span class="detail-label">Email</span><span class="detail-value">{{ $bookingRequest->email }}</span></div>
            <div class="detail"><span class="detail-label">Phone</span><span class="detail-value">{{ $bookingRequest->phone }}</span></div>
            @if ($bookingRequest->coupon_name)
                <div class="detail coupon-detail {{ $couponExpired ? 'expired' : ($coupon ? '' : 'unavailable') }}">
                    <span class="detail-label">Coupon offer</span>
                    <span class="detail-value">
                        @if ($couponExpired)
                            {{ $bookingRequest->coupon_name }} has expired. Don't be sad. We'll still give you our best price!
                        @elseif ($coupon)
                            {{ $coupon->name }}:
                            @if ($coupon->type === 'percentage')
                                {{ rtrim(rtrim($coupon->discount_value, '0'), '.') }}% off
                            @elseif ($coupon->type === 'fixed_amount')
                                Enjoy &#8377;{{ number_format((float) $coupon->discount_value, 2) }} off.
                            @else
                                {{ $coupon->custom_text }}
                            @endif
                            {{-- <small>Valid {{ $coupon->starts_at->format('M j, Y') }} - {{ $coupon->ends_at->format('M j, Y') }}</small> --}}
                        @else
                            This coupon is not valid, but we'll still try to give you our best price!
                        @endif
                    </span>
                </div>
            @endif
            <div class="detail"><span class="detail-label">Request status</span><span class="detail-value">{{ ucfirst($bookingRequest->request_status) }}</span></div>
        </div>

        <h2>Availability by date and slot</h2>
        @if (session('status'))
            <p class="notice" role="status">{{ session('status') }}</p>
        @endif
        <div class="date-list">
            @foreach ($slotsByDate as $dateSlots)
                <section class="date-card">
                    <h3>{{ $dateSlots->first()->safari_date->format('l, M j, Y') }}</h3>
                    @foreach ($dateSlots as $slot)
                        <article class="slot {{ $slot->was_available ? 'slot-available' : 'slot-unavailable' }}">
                            <div>
                                <div class="slot-name">{{ $slot->zone->name }}</div>
                                <div class="slot-meta">{{ $slot->safari_shift }} safari &middot; {{ $bookingRequest->safari_mode }}</div>
                            </div>
                            <span class="badge {{ $slot->was_available ? 'available' : 'unavailable' }}">{{ $slot->was_available ? 'Available' : 'Not available' }}</span>
                        </article>
                    @endforeach
                </section>
            @endforeach
        </div>

        @if (! $allAvailable && $bookingRequest->request_status !== 'submitted')
            <p class="availability-help" role="status">Submit your request and our team will contact you within 1 hour to help with your booking.</p>
        @endif
        @if ($bookingRequest->request_status !== 'submitted')
            <form method="POST" action="{{ route('safari-booking.confirmation.submit', ['confirmationToken' => $bookingRequest->confirmation_token]) }}">
                @csrf
                <button class="submit" type="submit">Submit booking request</button>
            </form>
            {{-- <p class="footer">Submitting sends these availability results and your contact details to the admin for review. This page is read-only.</p> --}}
        @else
            <p class="footer">Your booking request has been submitted to the admin for review.</p>
        @endif
        <a class="home-link" href="{{ route('home') }}">Back to safari form</a>
    </section>
</main>
@if (session('status'))
    <div class="popup-backdrop" id="request-popup" role="alertdialog" aria-modal="true" aria-labelledby="popup-title" aria-describedby="popup-message">
        <section class="popup-card">
            <div class="popup-icon" aria-hidden="true">&#10003;</div>
            <h2 id="popup-title">We got your request!</h2>
            <p id="popup-message">Within 1 hour, our team will contact you.</p>
            <button class="popup-close" type="button" id="popup-close">Okay</button>
        </section>
    </div>
    <script>
        const popup = document.getElementById('request-popup');
        document.getElementById('popup-close').addEventListener('click', () => popup.remove());
        popup.addEventListener('click', (event) => {
            if (event.target === popup) popup.remove();
        });
    </script>
@endif
</body>
</html>
