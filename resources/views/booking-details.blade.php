<div>
    <!-- Be present above all else. - Naval Ravikant -->
</div>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Safari booking details</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 28px 16px; display: grid; place-items: center; background: radial-gradient(ellipse at 10% 10%, #fff7e8, transparent 42%), linear-gradient(135deg, #e9e4d4, #f6f5ed 55%, #e3ebdf); color: #203329; font-family: Arial, sans-serif; }
        .card { width: min(760px, 100%); overflow: hidden; border: 1px solid #e1e8dd; border-radius: 26px; background: #fff; box-shadow: 0 28px 80px #253a2b20; }
        .header { padding: 30px 36px; background: linear-gradient(125deg, #1d5138, #163b2b); color: white; }
        .step { margin: 0 0 9px; color: #efbd7d; font-size: 11px; font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(26px, 5vw, 34px); letter-spacing: -.035em; }
        .header p:last-child { margin: 10px 0 0; color: #d8e6db; font-size: 14px; line-height: 1.5; }
        form { padding: 30px 36px 34px; }
        .fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 16px; }
        .field { min-width: 0; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 7px; color: #34463a; font-size: 12px; font-weight: 700; }
        input, select { width: 100%; min-height: 48px; padding: 0 13px; border: 1px solid #dce3da; border-radius: 11px; background: #fff; color: #26352b; font: inherit; font-size: 13px; }
        input:focus, select:focus { outline: 0; border-color: #6b9874; box-shadow: 0 0 0 4px #80a98925; }
        select:disabled { cursor: not-allowed; background: #f2f5ef; color: #536255; opacity: 1; }
        .hint { margin: 6px 0 0; color: #7d897e; font-size: 11px; line-height: 1.45; }
        .errors { margin: 0 0 18px; padding: 11px 13px; border: 1px solid #f2d2c7; border-radius: 10px; background: #fff5f1; color: #a42e10; font-size: 13px; }
        .button { width: 100%; min-height: 52px; margin-top: 24px; border: 0; border-radius: 12px; background: linear-gradient(100deg, #e97827, #d95c18); box-shadow: 0 9px 20px #df6b2430; color: white; font-size: 14px; font-weight: 700; cursor: pointer; }
        .button:hover { background: linear-gradient(100deg, #d9651c, #c94f13); }
        @media (max-width: 560px) { body { padding: 12px; } .header { padding: 25px 22px; } form { padding: 24px 22px; } .fields { grid-template-columns: 1fr; } .field.full { grid-column: auto; } }
    </style>
</head>
<body>
<main class="card">
    <header class="header">
        <p class="step">Step 1 of 2 · Your details</p>
        <h1>Plan your safari</h1>
        <p>Start with your park and contact details. You’ll choose your visit dates and zones next.</p>
    </header>
    <form method="POST" action="{{ route('safari-booking.details') }}">
        @csrf
        @if ($errors->any()) <p class="errors" role="alert">{{ $errors->first() }}</p> @endif
        <div class="fields">
            <div class="field full">
                @if ($parks->count() === 1)
                    <label for="park-display">National park</label>
                    <select id="park-display" disabled><option selected value="{{ $parks->first()->id }}">{{ $parks->first()->name }}</option></select>
                    <input type="hidden" name="national_park_id" value="{{ $parks->first()->id }}">
                    <p class="hint">This is the only available national park.</p>
                @else
                    <label for="national-park">National park</label>
                    <select id="national-park" name="national_park_id" required>
                        <option value="">Choose a national park</option>
                        @foreach ($parks as $park)
                            <option value="{{ $park->id }}" @selected((string) old('national_park_id', $draft['national_park_id'] ?? '') === (string) $park->id)>{{ $park->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="field">
                <label for="safari-mode">Safari mode</label>
                <select id="safari-mode" name="safari_mode" required>
                    <option value="">Choose safari mode</option>
                    @forelse ($vehicles as $vehicle)
                        <option value="{{ $vehicle }}" @selected(old('safari_mode', $draft['safari_mode'] ?? '') === $vehicle)>{{ $vehicle }}</option>
                    @empty
                        <option value="Gypsy" @selected(old('safari_mode', $draft['safari_mode'] ?? '') === 'Gypsy')>Gypsy</option>
                        <option value="Canter" @selected(old('safari_mode', $draft['safari_mode'] ?? '') === 'Canter')>Canter</option>
                    @endforelse
                </select>
            </div>
            <div class="field">
                <label for="safari-shift">Safari timeslot</label>
                <select id="safari-shift" name="safari_shift" required>
                    <option value="">Choose timeslot</option>
                    <option value="Morning" @selected(old('safari_shift', $draft['safari_shift'] ?? '') === 'Morning')>Morning</option>
                    <option value="Evening" @selected(old('safari_shift', $draft['safari_shift'] ?? '') === 'Evening')>Evening</option>
                    <option value="Any" @selected(old('safari_shift', $draft['safari_shift'] ?? '') === 'Any')>Any</option>
                </select>
            </div>
            <div class="field">
                <label for="customer-name">Your name</label>
                <input id="customer-name" name="name" value="{{ old('name', $draft['name'] ?? '') }}" autocomplete="name" placeholder="Enter your name" required>
            </div>
            <div class="field">
                <label for="customer-phone">Phone number</label>
                <input id="customer-phone" name="phone" type="tel" value="{{ old('phone', $draft['phone'] ?? '') }}" autocomplete="tel" placeholder="Enter your phone number" required>
            </div>
            <div class="field full">
                <label for="customer-email">Email address</label>
                <input id="customer-email" name="email" type="email" value="{{ old('email', $draft['email'] ?? '') }}" autocomplete="email" placeholder="Enter your email address" required>
            </div>
        </div>
        <button class="button" type="submit">Continue to dates and zones</button>
    </form>
</main>
</body>
</html>
