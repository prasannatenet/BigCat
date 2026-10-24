<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ranthambore Safari Booking</title>
    <style>
        :root {
            font-family: Arial, sans-serif;
            color: #171717;
            background: #f6ead8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: grid;
            place-items: center;
            background: radial-gradient(ellipse at 10% 10%, #fff6e8, transparent 45%), linear-gradient(135deg, #e7c89a, #f9eee0 52%, #d8b17e);
        }

        .booking {
            width: min(1760px, 100%);
            min-height: 570px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            background: white;
            border-radius: 22px;
            box-shadow: 0 18px 55px #432b1b26;
            overflow: hidden;
        }

        .park-panel {
            min-width: 0;
            padding: 30px 24px;
            border-right: 1px solid #e9e9e9;
            background: linear-gradient(155deg, #f8faf5, #eef3e9);
        }

        .calendar-panel {
            padding: 30px 34px;
            border-right: 1px solid #e9e9e9;
            display: flex;
            flex-direction: column;
        }

        .month-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 9%;
            border-bottom: 1px solid #ddd;
            padding-bottom: 16px;
        }

        .month-head h2 {
            font-size: 20px;
            margin: 0;
        }

        .month-head button {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            border: 1px solid #ddd;
            background: #f8f8f8;
            font-size: 25px;
            cursor: pointer;
        }

        .month-head button:last-child {
            background: #760600;
            border-color: #760600;
            color: white;
        }

        .weekdays,
        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
        }

        .weekdays {
            padding: 12px 0 8px;
            color: #333;
        }

        .days {
            gap: 4px;
        }

        .day {
            min-height: 48px;
            display: grid;
            place-items: center;
            border: 0;
            background: transparent;
            border-radius: 10px;
            font: inherit;
            color: #333;
        }

        button.day {
            cursor: pointer;
        }

        button.day:hover {
            background: #fff0e5;
        }

        button.day.selected {
            background: #ff7100;
            color: white;
            font-weight: 700;
        }

        button.day.unavailable {
            color: #b8b8b8;
            background: #f7f7f7;
            cursor: not-allowed;
        }

        .calendar-note {
            margin-top: auto;
            padding-top: 24px;
            color: #737373;
            font-size: 13px;
        }

        .form-panel {
            padding: 30px 34px 26px;
        }

        h1 {
            margin: 0;
            padding-bottom: 13px;
            border-bottom: 1px solid #ffeadc;
            color: #e85b00;
            text-align: center;
            font-size: 24px;
            font-weight: 500;
        }

        .intro {
            color: #555;
            font-size: 13px;
            line-height: 1.55;
            margin: 23px 0 17px;
        }

        .fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
            gap: 17px 18px;
        }

        .field {
            min-width: 0;
        }

        .field.full-width {
            grid-column: 1 / -1;
        }

        .phone-input-group {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 1.85fr);
            gap: 9px;
        }

        .field-hint {
            margin: 6px 0 0;
            color: #737373;
            font-size: 12px;
            line-height: 1.4;
        }

        .zone-picker {
            position: relative;
        }

        .zone-picker-button {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 0 13px;
            background: white;
            color: #333;
            text-align: left;
            font: inherit;
            font-size: 14px;
            cursor: pointer;
        }

        .zone-cards {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            z-index: 5;
            width: 100%;
            max-height: 260px;
            overflow-y: auto;
            display: grid;
            gap: 8px;
            padding: 10px;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            background: white;
            box-shadow: 0 12px 28px #0002;
        }

        .zone-cards[hidden] {
            display: none;
        }

        .zone-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 52px;
            padding: 10px 12px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            background: white;
            text-align: left;
            cursor: pointer;
        }

        .zone-card-copy {
            min-width: 0;
            flex: 1;
        }

        .zone-card-description {
            display: block;
            margin-top: 3px;
            color: #666;
            font-size: 12px;
            line-height: 1.35;
        }

        .zone-card-image {
            width: 58px;
            height: 48px;
            flex: none;
            border-radius: 6px;
            object-fit: cover;
        }

        .selected-park-card {
            position: relative;
            height: 100%;
            min-height: 100%;
            min-width: 0;
            overflow: hidden;
            border: 1px solid #dce4d8;
            border-radius: 18px;
            background: linear-gradient(155deg, #66846a, #1d4932);
            box-shadow: 0 12px 30px #183c2c20;
        }

        .selected-park-card img,
        .park-image-placeholder {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .park-image-placeholder {
            display: grid;
            place-items: center;
            padding: 20px;
            color: #e7f0e5;
            text-align: center;
            font-size: 13px;
            line-height: 1.5;
        }

        .park-image-placeholder[hidden],
        .selected-park-card img[hidden] {
            display: none;
        }

        .selected-park-caption {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            padding: 36px 15px 16px;
            background: linear-gradient(transparent, #10291fe8);
            color: white;
        }

        .selected-park-caption p {
            margin: 0 0 5px;
            color: #e4c794;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .selected-park-caption h3 {
            margin: 0;
            font-size: 17px;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .calendar-widget {
            display: flex;
            min-height: 100%;
            min-width: 0;
            flex-direction: column;
            padding: 16px;
            border: 1px solid #dce4d8;
            border-radius: 18px;
            background: #ffffffb8;
        }

        .zone-card:hover {
            border-color: #ff9b56;
            background: #fff8f3;
        }

        .zone-card.selected {
            border-color: #ff7100;
            background: #fff3e9;
        }

        .zone-card-check {
            color: #e85b00;
            font-weight: 700;
        }

        .selected-zones {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .selected-zone-chip {
            padding: 4px 8px;
            border-radius: 999px;
            background: #fff0e5;
            color: #9c3900;
            font-size: 12px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input,
        select {
            width: 100%;
            min-height: 44px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 0 13px;
            background: white;
            color: #333;
            font: inherit;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: 2px solid #ff710055;
            border-color: #ff7100;
        }

        .submit {
            margin-top: 18px;
            width: 100%;
            min-height: 46px;
            border: 0;
            border-radius: 5px;
            color: white;
            background: #ff6900;
            font-weight: 700;
            cursor: pointer;
        }

        .submit:hover {
            background: #e95e00;
        }

        .notice {
            margin: 12px 0;
            padding: 11px 13px;
            border-radius: 6px;
            font-size: 14px;
            background: #edfff4;
            color: #176b3a;
        }

        .notice.error {
            background: #fff3ef;
            color: #a42e10;
        }

        .errors {
            color: #a42e10;
            font-size: 13px;
            margin: 12px 0;
        }

        .result-overlay {
            position: fixed;
            inset: 0;
            z-index: 10;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #17171788;
        }

        .result-overlay[hidden] {
            display: none;
        }

        .result-dialog {
            width: min(420px, 100%);
            padding: 30px;
            border-radius: 16px;
            background: white;
            text-align: center;
            box-shadow: 0 24px 80px #0004;
        }

        .result-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 14px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #dcfce7;
            color: #15803d;
            font-size: 28px;
        }

        .result-dialog.error .result-icon {
            background: #fee2e2;
            color: #b91c1c;
        }

        .result-dialog h2 {
            margin: 0 0 9px;
            font-size: 21px;
        }

        .result-dialog p {
            margin: 0;
            color: #555;
            font-size: 14px;
            line-height: 1.55;
        }

        .result-close {
            margin-top: 22px;
            min-width: 120px;
            padding: 11px 18px;
            border: 0;
            border-radius: 7px;
            background: #ff6900;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 760px) {
            body {
                padding: 12px;
                place-items: start center;
            }

            .booking {
                grid-template-columns: 1fr;
            }

            .calendar-panel {
                border-right: 0;
                border-bottom: 1px solid #e9e9e9;
                padding: 22px;
                min-height: 420px;
            }

            .form-panel {
                padding: 24px 22px;
            }
        }

        @media (max-width: 440px) {
            .fields {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            .field.full-width {
                grid-column: auto;
            }

            .month-head {
                padding-left: 1%;
                padding-right: 1%;
            }
        }

        :root {
            color: #18251f;
            background: #f1eee4;
        }

        body {
            padding: 32px 20px;
            background: radial-gradient(ellipse at 8% 12%, #fff8e9 0, transparent 38%), radial-gradient(ellipse at 92% 85%, #d6e3d1 0, transparent 34%), linear-gradient(135deg, #ede7d8, #f8f6ef 55%, #e4eadf);
        }

        .booking {
            width: min(1760px, 100%);
            min-height: 640px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            border: 1px solid #e6e8df;
            border-radius: 28px;
            box-shadow: 0 28px 80px #253a2b20;
        }

        .park-panel {
            padding: 38px 28px;
            border-right-color: #e5e9e0;
        }

        .calendar-panel {
            padding: 38px 28px;
            border-right-color: #e5e9e0;
            background: linear-gradient(155deg, #f8faf5, #eef3e9);
        }

        .month-head {
            padding: 0 3% 18px;
            border-bottom-color: #dce4d8;
        }

        .month-head h2 {
            color: #183c2c;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .month-head button {
            width: 46px;
            height: 46px;
            border-color: #dce4d8;
            border-radius: 14px;
            background: white;
            color: #244332;
            box-shadow: 0 3px 10px #183c2c0c;
            transition: transform .18s, background .18s;
        }

        .month-head button:hover {
            transform: translateY(-2px);
            background: #edf4e9;
        }

        .month-head button:last-child {
            border-color: #24543c;
            background: #24543c;
            color: white;
        }

        .month-head button:last-child:hover {
            background: #173e2b;
        }

        .weekdays {
            padding: 18px 0 10px;
            color: #718174;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
        }

        .days {
            gap: 6px;
        }

        .day {
            min-height: 48px;
            border-radius: 13px;
            color: #34443a;
            font-size: 14px;
        }

        button.day {
            transition: background .15s, color .15s, transform .15s;
        }

        button.day:hover {
            transform: translateY(-1px);
            background: #e2ecd9;
        }

        button.day.selected {
            background: #e77a28;
            color: white;
            box-shadow: 0 5px 12px #e77a2840;
        }

        button.day.unavailable {
            background: #e7ebe5;
            color: #aab3aa;
        }

        .calendar-note {
            margin-top: auto;
            padding: 14px 15px;
            border: 1px solid #dce7d7;
            border-radius: 12px;
            background: #ffffffb8;
            color: #627264;
            font-size: 13px;
            line-height: 1.5;
        }

        .form-panel {
            padding: 38px 42px 34px;
        }

        .form-eyebrow {
            margin: 0 0 9px;
            color: #6d866f;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .15em;
            text-align: left;
            text-transform: uppercase;
        }

        h1 {
            padding: 0;
            border: 0;
            color: #183c2c;
            text-align: left;
            font-size: clamp(27px, 3vw, 34px);
            font-weight: 750;
            letter-spacing: -.04em;
            line-height: 1.12;
        }

        .intro {
            margin: 12px 0 21px;
            color: #69736b;
            font-size: 14px;
            line-height: 1.65;
        }

        .selected-park-card img {
            transition: opacity .2s ease;
        }

        .fields {
            gap: 16px 14px;
        }

        label {
            margin-bottom: 7px;
            color: #34463a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .015em;
        }

        input,
        select,
        .zone-picker-button {
            min-height: 48px;
            border: 1px solid #dce3da;
            border-radius: 11px;
            padding: 0 13px;
            background: #fff;
            color: #26352b;
            font-size: 13px;
            box-shadow: 0 2px 5px #1b352408;
            transition: border-color .18s, box-shadow .18s;
        }

        input:focus,
        select:focus,
        .zone-picker-button:focus-visible {
            outline: 0;
            border-color: #6b9874;
            box-shadow: 0 0 0 4px #80a98925;
        }

        select:disabled {
            cursor: not-allowed;
            background: #f2f5ef;
            color: #536255;
            opacity: 1;
        }

        .field-hint {
            margin-top: 6px;
            color: #879187;
            font-size: 11px;
        }

        .selection-summary {
            min-height: 48px;
            display: flex;
            align-items: center;
            padding: 0 13px;
            border: 1px solid #dce3da;
            border-radius: 11px;
            background: #f4f7f1;
            color: #31583d;
            font-size: 13px;
            font-weight: 700;
        }

        .back-link {
            display: inline-block;
            margin-top: 14px;
            color: #46634d;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .date-zone-assignments {
            display: grid;
            gap: 9px;
            margin-top: 12px;
        }

        .zone-dropdown-button {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 12px;
            border: 1px solid #dce3da;
            border-radius: 10px;
            background: white;
            color: #35483a;
            text-align: left;
            font: inherit;
            font-size: 12px;
            cursor: pointer;
        }

        .zone-dropdown-button:hover,
        .zone-dropdown-button[aria-expanded="true"] {
            border-color: #78a47f;
            background: #f7faf5;
        }

        .zone-dropdown-button .chevron {
            color: #6d866f;
            font-size: 16px;
        }

        .date-zone-options {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            right: 0;
            z-index: 5;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 7px;
            max-height: 240px;
            overflow-y: auto;
            padding: 9px;
            border: 1px solid #e1e7df;
            border-radius: 12px;
            background: white;
            box-shadow: 0 16px 38px #183c2c1c;
        }

        .date-zone-options[hidden] {
            display: none;
        }

        .date-zone-option {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
            margin: 0;
            padding: 8px 10px;
            border: 1px solid #e4eae1;
            border-radius: 9px;
            background: white;
            color: #35483a;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .date-zone-option:has(input:checked) {
            border-color: #78a47f;
            background: #edf5eb;
            color: #285637;
        }

        .date-zone-option input[type="checkbox"] {
            width: 16px;
            min-width: 16px;
            height: 16px;
            min-height: 16px;
            margin: 0;
            accent-color: #35734b;
        }

        .zone-picker-button {
            font-size: 13px;
        }

        .zone-cards {
            top: calc(100% + 8px);
            max-height: 300px;
            gap: 7px;
            padding: 9px;
            border-color: #e1e7df;
            border-radius: 14px;
            box-shadow: 0 16px 38px #183c2c1c;
        }

        .zone-card {
            min-height: 58px;
            border-color: #e7ebe5;
            border-radius: 10px;
        }

        .zone-card:hover {
            border-color: #a8c2a9;
            background: #f5f8f2;
        }

        .zone-card.selected {
            border-color: #5c8a66;
            background: #edf5eb;
        }

        .zone-card-check {
            color: #35734b;
            font-size: 12px;
        }

        .selected-zones {
            gap: 7px;
            margin-top: 9px;
        }

        .selected-zone-chip {
            padding: 5px 10px;
            border: 1px solid #d8e7d6;
            background: #f0f6ed;
            color: #315c3b;
            font-weight: 600;
        }

        .submit {
            min-height: 52px;
            margin-top: 22px;
            border-radius: 12px;
            background: linear-gradient(100deg, #e97827, #d95c18);
            box-shadow: 0 9px 20px #df6b2430;
            font-size: 14px;
            letter-spacing: .01em;
            transition: transform .18s, box-shadow .18s;
        }

        .submit:hover {
            transform: translateY(-1px);
            background: linear-gradient(100deg, #d9651c, #c94f13);
            box-shadow: 0 12px 24px #df6b2440;
        }

        .errors {
            margin: 10px 0;
            padding: 10px 12px;
            border: 1px solid #f2d2c7;
            border-radius: 10px;
            background: #fff5f1;
        }

        .result-dialog {
            border: 1px solid #e7ebe5;
        }

        .result-close {
            border-radius: 9px;
            background: #24543c;
        }

        @media (max-width: 1100px) {
            .booking {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .park-panel {
                grid-column: 1 / -1;
                min-height: 300px;
                border-right: 0;
                border-bottom: 1px solid #e5e9e0;
            }

            .selected-park-card {
                min-height: 220px;
            }

            .calendar-panel,
            .form-panel {
                padding: 30px 26px;
            }

            .calendar-widget {
                min-height: 450px;
                padding: 10px;
            }
        }

        @media (max-width: 760px) {
            body {
                padding: 16px 12px;
            }

            .booking {
                grid-template-columns: 1fr;
                min-height: 0;
                border-radius: 22px;
            }

            .park-panel {
                min-height: 240px;
                padding: 22px;
            }

            .selected-park-card {
                min-height: 190px;
            }

            .calendar-panel {
                min-height: 470px;
                border-right: 0;
                border-bottom: 1px solid #e9e9e9;
            }

            .form-panel {
                padding: 30px 24px;
            }
        }

        @media (max-width: 440px) {

            .calendar-panel,
            .form-panel {
                padding: 24px 18px;
            }

            .day {
                min-height: 42px;
            }

            .date-zone-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <main class="booking">
        <section class="park-panel" aria-label="Selected national park">
            <div class="selected-park-card" aria-live="polite">
                <img id="selected-park-image" alt="" hidden>
                <div id="park-image-placeholder" class="park-image-placeholder">Select a national park to see its photo.
                </div>
                <div class="selected-park-caption">
                    <p>Selected national park</p>
                    <h3 id="selected-park-name">{{ $selectedPark?->name ?? 'Choose a park' }}</h3>
                </div>
            </div>
        </section>
        <section class="calendar-panel" aria-label="Choose safari date">
            <div class="calendar-widget">
                <div class="month-head">
                    <button type="button" id="previous-month" aria-label="Previous month">‹</button>
                    <h2 id="month-label"></h2>
                    <button type="button" id="next-month" aria-label="Next month">›</button>
                </div>
                <div class="weekdays" aria-hidden="true">
                    <span>SUN</span><span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span><span>SAT</span>
                </div>
                <div class="days" id="calendar-days"></div>
                <p class="calendar-note" id="calendar-note">Select the date you want to visit.</p>
            </div>
        </section>
        <section class="form-panel">
            <p class="form-eyebrow">Step 1 of 2 · Choose your safari</p>
            <h1>Plan your safari</h1>
            <p class="intro">Choose your national park, dates, and zones, then add your details. On the next page,
                choose Morning, Evening, or Any for each visit.</p>
            @if ($errors->any())
            <div class="errors" role="alert">{{ $errors->first() }}</div> @endif
            <form method="POST" action="{{ route('safari-booking.details') }}" id="booking-form">
                @csrf
                <div id="selected-dates-inputs"></div>
                <div class="fields">
                    <div class="field">
                        <label for="park-select">National park</label>
                        @if ($parks->count() === 1)
                            <input type="hidden" id="park" value="{{ $parks->first()->id }}">
                            <input type="hidden" name="national_park_id" value="{{ $parks->first()->id }}">
                            <select id="park-select" disabled>
                                <option>{{ $parks->first()->name }}</option>
                            </select>
                        @else
                            <select id="park" name="national_park_id" required>
                                <option value="">Choose a national park</option>
                                @foreach ($parks as $park)
                                    <option value="{{ $park->id }}" @selected((string) old('national_park_id', $selectedPark?->id) === (string) $park->id)>{{ $park->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="field">
                        <label for="safari_mode">Safari mode</label>
                        <select id="safari_mode" name="safari_mode" required>
                            <option value="">Choose safari mode</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle }}" @selected(old('safari_mode') === $vehicle)>{{ $vehicle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field full-width zone-picker">
                        <label for="zone-dropdown-button">Choose zones</label>
                        <p class="field-hint">Choose the zones you may want to visit. You can match them to dates on the
                            next page.</p>
                        <button class="zone-dropdown-button" id="zone-dropdown-button" type="button"
                            aria-expanded="false">
                            <span id="zone-dropdown-label">Choose one or more zones</span><span class="chevron">⌄</span>
                        </button>
                        <div class="date-zone-options" id="zone-options" hidden></div>
                        <p class="errors" id="zone-selection-error" hidden>Choose at least one zone.</p>
                    </div>
                    <div class="field full-width">
                        <label for="name">Your name</label>
                        <input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>
                    </div>
                    <div class="field">
                        <label for="phone">Phone number</label>
                        <div class="phone-input-group">
                            <select id="phone_country_iso" name="phone_country_iso" aria-label="Country calling code"
                                required>
                                <option value="">Select country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country['iso2'] }}" @selected(old('phone_country_iso', 'IN') === $country['iso2'])>{{ $country['iso2'] }} {{ $country['calling_code'] }} -
                                        {{ $country['flag'] }} {{ $country['name'] }}</option>
                                @endforeach
                            </select>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                                autocomplete="tel-national" placeholder="Phone number" required>
                        </div>
                        {{-- <p class="field-hint">Select your country to include its calling code with your contact
                            details.</p> --}}
                    </div>
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                            required>
                    </div>
                    <div class="field full-width">
                        <label for="coupon_name">Coupon</label>
                        <input id="coupon_name" name="coupon_name" value="{{ old('coupon_name') }}" autocomplete="off"
                            placeholder="Enter a coupon name or code">
                        <p class="field-hint">Have a coupon? Enter it here with your booking request.</p>
                    </div>
                </div>
                <button class="submit" type="submit">Continue to choose safari slots</button>
            </form>
        </section>
    </main>
    <script>
        const zonesByPark = @json($zonesByPark);
        const parkImagesById = @json($parkImagesById);
        const parkNamesById = @json($parkNamesById);
        const parkInput = document.getElementById('park');
        const selectedParkImage = document.getElementById('selected-park-image');
        const parkImagePlaceholder = document.getElementById('park-image-placeholder');
        const selectedParkName = document.getElementById('selected-park-name');
        const zoneSelectionError = document.getElementById('zone-selection-error');
        const selectedDatesInputs = document.getElementById('selected-dates-inputs');
        const zoneOptions = document.getElementById('zone-options');
        const zoneDropdown = document.getElementById('zone-dropdown-button');
        const zoneDropdownLabel = document.getElementById('zone-dropdown-label');
        const oldZoneIds = @json(old('zone_ids', []));
        const today = new Date(); today.setHours(0, 0, 0, 0);
        let visibleMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        let selectedDates = @json(old('safari_dates', []));
        selectedDates = selectedDates.map(String);
        let selectedZoneIds = oldZoneIds.map(String);
        let currentParkId = parkInput.value;

        function selectedParkId() { return parkInput.value; }
        function updateParkGallery() {
            const parkId = selectedParkId();
            const imageUrl = (parkImagesById[parkId] || [])[0];
            const parkName = parkNamesById[parkId] || 'Choose a park';
            selectedParkName.textContent = parkName;
            selectedParkImage.hidden = !imageUrl;
            parkImagePlaceholder.hidden = Boolean(imageUrl);
            parkImagePlaceholder.textContent = parkId
                ? `No photo available for ${parkName}.`
                : 'Select a national park to see its photo.';

            if (imageUrl) {
                selectedParkImage.src = imageUrl;
                selectedParkImage.alt = parkName;
            } else {
                selectedParkImage.removeAttribute('src');
                selectedParkImage.alt = '';
            }
        }
        function updateZones() {
            const zones = zonesByPark[selectedParkId()] || [];
            if (currentParkId !== selectedParkId()) {
                selectedZoneIds = [];
                currentParkId = selectedParkId();
            }
            zoneOptions.replaceChildren();
            zones.forEach((zone) => {
                const zoneId = String(zone.id);
                const zoneLabel = document.createElement('label');
                zoneLabel.className = 'date-zone-option';
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'zone_ids[]';
                checkbox.value = zoneId;
                checkbox.checked = selectedZoneIds.includes(zoneId);
                const zoneName = document.createElement('span');
                zoneName.textContent = zone.name;
                zoneLabel.append(checkbox, zoneName);
                zoneOptions.append(zoneLabel);
                checkbox.addEventListener('change', () => {
                    const selected = new Set(selectedZoneIds);
                    if (checkbox.checked) selected.add(zoneId);
                    else selected.delete(zoneId);
                    selectedZoneIds = [...selected];
                    updateZoneLabel(zones);
                    zoneSelectionError.hidden = selectedZoneIds.length > 0;
                });
            });
            updateZoneLabel(zones);
            zoneSelectionError.hidden = selectedZoneIds.length > 0;
            syncSelectedDates();
        }
        function updateZoneLabel(zones) {
            const selectedNames = zones.filter((zone) => selectedZoneIds.includes(String(zone.id))).map((zone) => zone.name);
            zoneDropdownLabel.textContent = selectedNames.length ? selectedNames.join(', ') : 'Choose one or more zones';
        }
        function syncSelectedDates() {
            selectedDatesInputs.replaceChildren();
            selectedDates.forEach((date) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'safari_dates[]';
                input.value = date;
                selectedDatesInputs.append(input);
            });
            document.getElementById('calendar-note').textContent = selectedDates.length
                ? `${selectedDates.length} date${selectedDates.length === 1 ? '' : 's'} selected. Select a date again to remove it.`
                : 'Choose one or more dates.';
        }
        function drawCalendar() {
            const calendar = document.getElementById('calendar-days');
            const year = visibleMonth.getFullYear(), month = visibleMonth.getMonth();
            document.getElementById('month-label').textContent = visibleMonth.toLocaleDateString('en', { month: 'long', year: 'numeric' });
            calendar.replaceChildren();
            const startOffset = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            for (let i = 0; i < startOffset; i++) calendar.append(document.createElement('span'));
            for (let day = 1; day <= daysInMonth; day++) {
                const key = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const isPastDate = new Date(year, month, day) < today;
                const button = document.createElement('button');
                button.type = 'button'; button.className = 'day'; button.textContent = day;
                button.setAttribute('aria-label', `${key}${isPastDate ? ', past date' : ''}`);
                if (isPastDate) { button.disabled = true; button.classList.add('unavailable'); }
                const isSelected = selectedDates.includes(key);
                if (isSelected) button.classList.add('selected');
                button.setAttribute('aria-pressed', String(isSelected));
                button.addEventListener('click', () => {
                    selectedDates = isSelected
                        ? selectedDates.filter((selectedDate) => selectedDate !== key)
                        : [...selectedDates, key];
                    updateZones();
                    drawCalendar();
                });
                calendar.append(button);
            }
        }
        document.getElementById('previous-month').addEventListener('click', () => { visibleMonth.setMonth(visibleMonth.getMonth() - 1); drawCalendar(); });
        document.getElementById('next-month').addEventListener('click', () => { visibleMonth.setMonth(visibleMonth.getMonth() + 1); drawCalendar(); });
        zoneDropdown.addEventListener('click', () => {
            zoneOptions.hidden = !zoneOptions.hidden;
            zoneDropdown.setAttribute('aria-expanded', String(!zoneOptions.hidden));
        });
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.zone-picker')) {
                zoneOptions.hidden = true;
                zoneDropdown.setAttribute('aria-expanded', 'false');
            }
        });
        parkInput.addEventListener('change', () => {
            updateZones();
            updateParkGallery();
        });
        document.getElementById('booking-form').addEventListener('submit', (event) => {
            const missingDates = selectedDates.length === 0;
            const missingZones = selectedZoneIds.length === 0;
            zoneSelectionError.hidden = !missingZones;
            if (missingZones || missingDates || !document.getElementById('park').value) {
                event.preventDefault();
                if (missingZones) {
                    zoneSelectionError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                if (missingDates) {
                    document.getElementById('calendar-note').textContent = 'Select at least one date on the calendar.';
                    document.querySelector('.calendar-panel').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                if (!document.getElementById('park').value) {
                    document.getElementById('park').focus();
                }
            }
        });
        updateZones();
        updateParkGallery();
        syncSelectedDates();
        drawCalendar();

    </script>
</body>

</html>