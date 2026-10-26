<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose Safari Slots</title>
    <style>
        :root { font-family: Arial, sans-serif; color: #203328; background: #f1eee4; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 32px 18px; display: grid; place-items: center; background: linear-gradient(135deg, #f8f6ef, #e4eadf); }
        main { width: min(800px, 100%); padding: clamp(24px, 5vw, 48px); border: 1px solid #e4e9e1; border-radius: 24px; background: white; box-shadow: 0 24px 70px #253a2b18; }
        .eyebrow { margin: 0 0 9px; color: #6d866f; font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
        h1 { margin: 0; color: #183c2c; font-size: clamp(26px, 4vw, 34px); letter-spacing: -.03em; }
        .intro { margin: 12px 0 22px; color: #69736b; font-size: 14px; line-height: 1.6; }
        .summary { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 22px; }
        .summary span { padding: 8px 11px; border: 1px solid #e2e9df; border-radius: 999px; background: #f5f8f2; color: #31583d; font-size: 12px; font-weight: 700; }
        .day-list { display: grid; gap: 14px; }
        .day-card { padding: 17px; border: 1px solid #e4eae1; border-radius: 15px; background: #fbfcfa; }
        .day-card h2 { margin: 0 0 14px; color: #263d2e; font-size: 17px; }
        .shift-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .shift-card { padding: 13px; border: 1px solid #e7ece5; border-radius: 12px; background: white; }
        .shift-title { display: flex; align-items: center; gap: 8px; margin-bottom: 9px; color: #31583d; font-size: 13px; font-weight: 800; }
        .shift-dot { width: 8px; height: 8px; border-radius: 50%; background: #e97827; }
        .shift-card.evening .shift-dot { background: #425f88; }
        label { display: block; margin-bottom: 6px; color: #46584a; font-size: 11px; font-weight: 700; }
        select { width: 100%; min-height: 44px; padding: 0 11px; border: 1px solid #dce3da; border-radius: 9px; background: white; color: #26352b; font: inherit; font-size: 13px; }
        select:focus { outline: 3px solid #80a98925; border-color: #6b9874; }
        .shift-note { margin: 7px 0 0; color: #788278; font-size: 11px; line-height: 1.4; }
        .errors { margin: 12px 0; padding: 11px 13px; border: 1px solid #f2d2c7; border-radius: 10px; background: #fff5f1; color: #a42e10; font-size: 13px; }
        .submit { width: 100%; min-height: 51px; margin-top: 20px; border: 0; border-radius: 11px; background: linear-gradient(100deg, #e97827, #d95c18); box-shadow: 0 8px 18px #df6b2430; color: white; font-size: 14px; font-weight: 700; cursor: pointer; }
        .back { display: inline-block; margin-top: 14px; color: #46634d; font-size: 13px; font-weight: 700; text-decoration: none; }
        @media (max-width: 560px) { body { padding: 14px; } main { border-radius: 18px; } .shift-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<main>
    <p class="eyebrow">Step 2 of 2 · Choose your safari slots</p>
    <h1>Choose a zone for each slot</h1>
    <p class="intro">Choose a zone for Morning, Evening, or both on each date. You can select the same zone for both shifts. Assign every zone you selected to a date and slot.</p>
    <div class="summary">
        <span>{{ $selectedPark->name }}</span>
        <span>{{ $draft['safari_mode'] }}</span>
        <span>{{ $draft['name'] }}</span>
    </div>
    @if ($errors->any())
        <div class="errors" role="alert">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('safari-booking-requests.store') }}" id="slot-form">
        @csrf
        <div class="day-list">
            @foreach ($days as $index => $day)
                <section class="day-card" data-date="{{ $day['date'] }}">
                    <h2>{{ \Carbon\CarbonImmutable::parse($day['date'])->format('l, M j, Y') }}</h2>
                    <input type="hidden" name="visits[{{ $index }}][date]" value="{{ $day['date'] }}">
                    <div class="shift-grid">
                        <div class="shift-card morning">
                            <label class="shift-title" for="morning-{{ $index }}"><span class="shift-dot"></span>Morning</label>
                            <select id="morning-{{ $index }}" name="visits[{{ $index }}][morning]">
                                <option value="">No morning visit</option>
                                @foreach ($day['zone_choices'] as $zoneChoice)
                                    <option value="{{ $zoneChoice['zone']->id }}" @disabled(! $zoneChoice['morning_available']) @selected((string) old("visits.$index.morning") === (string) $zoneChoice['zone']->id)>
                                        {{ $zoneChoice['zone']->name }}{{ $zoneChoice['morning_available'] ? '' : ' — Not available' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="shift-note">All selected zones are listed. Unavailable zones are disabled.</p>
                        </div>
                        <div class="shift-card evening">
                            <label class="shift-title" for="evening-{{ $index }}"><span class="shift-dot"></span>Evening</label>
                            <select id="evening-{{ $index }}" name="visits[{{ $index }}][evening]">
                                <option value="">No evening visit</option>
                                @foreach ($day['zone_choices'] as $zoneChoice)
                                    <option value="{{ $zoneChoice['zone']->id }}" @disabled(! $zoneChoice['evening_available']) @selected((string) old("visits.$index.evening") === (string) $zoneChoice['zone']->id)>
                                        {{ $zoneChoice['zone']->name }}{{ $zoneChoice['evening_available'] ? '' : ' — Not available' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="shift-note">All selected zones are listed. Unavailable zones are disabled.</p>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
        <p class="errors" id="visit-error" hidden></p>
        <button class="submit" type="submit">Check safari availability</button>
    </form>
    <a class="back" href="{{ route('home') }}">Back to change dates and zones</a>
</main>
<script>
    document.getElementById('slot-form').addEventListener('submit', (event) => {
        const days = [...document.querySelectorAll('.day-card')];
        const scheduledDates = new Set();
        const scheduledZones = new Set();
        let message = '';
        days.forEach((day) => {
            const morning = day.querySelector('select[name$="[morning]"]').value;
            const evening = day.querySelector('select[name$="[evening]"]').value;
            if (!morning && !evening) {
                message = 'Choose at least one zone for every selected date.';
            }
            if (morning || evening) scheduledDates.add(day.dataset.date);
            if (morning) scheduledZones.add(morning);
            if (evening) scheduledZones.add(evening);
        });
        const availableDates = new Set(days.map((day) => day.dataset.date));
        const selectedZoneIds = @json(array_map('strval', $draft['zone_ids']));
        if (!message && scheduledDates.size !== availableDates.size) message = 'Assign a visit to every selected date.';
        if (!message && selectedZoneIds.some((zoneId) => !scheduledZones.has(zoneId))) message = 'Assign every selected zone to a date and slot.';
        if (message) {
            event.preventDefault();
            const error = document.getElementById('visit-error');
            error.textContent = message;
            error.hidden = false;
            error.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
</body>
</html>
