@extends('admin.layouts.app')

@section('title', 'Slot Booking')
@section('subtitle', 'Manage park-wide and zone-specific slot availability.')

@section('content')
<div class="mx-auto max-w-7xl space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-700">Park operations</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Slot Booking</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-500">Set dates available across a whole national park, then configure zones and any zone-specific exceptions.</p>
        </div>
        <div class="flex gap-3">
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">National parks</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $parks->count() }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Zones configured</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $zones->count() }}</p>
            </div>
        </div>
    </div>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="grid gap-6 md:grid-cols-[minmax(0,1fr)_minmax(16rem,0.8fr)] md:items-end">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Step 1</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Choose a national park</h2>
                <p class="mt-1 text-sm text-slate-500">The availability calendar applies to every slot zone in the selected park.</p>
            </div>
            @if ($parks->isEmpty())
                <a href="{{ route('admin.national-parks.create') }}" class="inline-flex justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Create a national park</a>
            @else
                <form method="GET" action="{{ route('admin.slots.index') }}">
                    <div class="mb-4 flex items-center gap-4">
                        <label for="months-select" class="text-sm font-medium text-slate-700">Months from today</label>
                        <select id="months-select" name="months" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 w-32" onchange="this.form.submit()">
                            <option value="12" {{ $months == 12 ? 'selected' : '' }}>12 months (1 year)</option>
                            <option value="6" {{ $months == 6 ? 'selected' : '' }}>6 months</option>
                            <option value="3" {{ $months == 3 ? 'selected' : '' }}>3 months</option>
                            <option value="1" {{ $months == 1 ? 'selected' : '' }}>1 month</option>
                        </select>
                    </div>
                    <input type="hidden" name="year" value="{{ $calendarYear }}">
                    <label for="park-select" class="mb-2 block text-sm font-semibold text-slate-700">National park</label>
                    <select id="park-select" name="park" onchange="this.form.submit()"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                        <option value="">Select a national park</option>
                        @foreach ($parks as $park)
                            <option value="{{ $park->id }}" @selected((string) $selectedParkId === (string) $park->id)>{{ $park->name }} — {{ $park->location }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </section>

    @if ($selectedPark)
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                 x-data="{
                     rangeStart: @js($calendarStartDate->toDateString()),
                     rangeEnd: @js($calendarEndDate->toDateString()),
                     availability: Object.fromEntries(Object.entries(@js($parkAvailability)).map(([date, status]) => [date, status ? '1' : '0'])),
                     selectedDates: [],
                     calendarMonths() {
                         const start = new Date(`${this.rangeStart}T00:00:00`);
                         const end = new Date(`${this.rangeEnd}T00:00:00`);
                         const months = [];
                         for (let cursor = new Date(start.getFullYear(), start.getMonth(), 1); cursor <= end; cursor.setMonth(cursor.getMonth() + 1)) {
                             months.push({
                                 key: `${cursor.getFullYear()}-${cursor.getMonth()}`,
                                 year: cursor.getFullYear(),
                                 month: cursor.getMonth(),
                                 label: new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(cursor),
                                 offset: new Date(cursor.getFullYear(), cursor.getMonth(), 1).getDay(),
                                 days: new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate()
                             });
                         }
                         return months;
                     },
                     dateFor(calendarMonth, day) {
                         return `${calendarMonth.year}-${String(calendarMonth.month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                     },
                     isInRange(date) {
                         return date >= this.rangeStart && date <= this.rangeEnd;
                     },
                     toggleDate(date) {
                         if (this.selectedDates.includes(date)) {
                             this.selectedDates = this.selectedDates.filter(selectedDate => selectedDate !== date);
                         } else {
                             this.selectedDates.push(date);
                         }
                     },
                     setSelectedStatus(status) {
                         this.selectedDates.forEach(date => this.availability[date] = status);
                         this.selectedDates = [];
                     },
                     clearSelectedStatus() {
                         this.selectedDates.forEach(date => delete this.availability[date]);
                         this.selectedDates = [];
                     }
                 }">
            <div class="border-b border-slate-100 bg-gradient-to-r from-emerald-50 via-white to-white px-6 py-6 sm:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Park-wide default</p>
                            <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $selectedPark->name }}</h2>
                            <p class="mt-1 max-w-2xl text-sm text-slate-500">Dates set here are the default availability for every zone in this park. You can override individual dates in any zone below.</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 sm:justify-center">
                        <span class="text-sm font-semibold text-slate-700">{{ $calendarStartDate->format('M j, Y') }} – {{ $calendarEndDate->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.slots.park-availability.update', $selectedPark) }}" class="space-y-6 p-5 sm:p-8">
                @csrf
                @method('PUT')
                <input type="hidden" name="year" value="{{ $calendarYear }}">
                <input type="hidden" name="months" value="{{ $months }}">
                <template x-for="(date, index) in Object.keys(availability).filter(date => isInRange(date))" :key="date">
                    <div>
                        <input type="hidden" :name="`availability[${index}][date]`" :value="date">
                        <input type="hidden" :name="`availability[${index}][is_available]`" :value="availability[date]">
                    </div>
                </template>

                <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800"><span x-text="selectedDates.length"></span> date(s) selected</p>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs font-medium text-slate-600">
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Available</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>Not available</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>Uses zone default</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="setSelectedStatus('1')" :disabled="selectedDates.length === 0"
                                class="rounded-xl bg-emerald-600 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">Set available</button>
                        <button type="button" @click="setSelectedStatus('0')" :disabled="selectedDates.length === 0"
                                class="rounded-xl bg-rose-600 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-40">Set not available</button>
                        <button type="button" @click="clearSelectedStatus()" :disabled="selectedDates.length === 0"
                                class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">Use zone default</button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="calendarMonth in calendarMonths()" :key="calendarMonth.key">
                        <article class="rounded-2xl border border-slate-200 p-3 sm:p-4">
                            <h3 class="mb-3 text-sm font-bold text-slate-900" x-text="calendarMonth.label"></h3>
                            <div class="grid grid-cols-7 gap-1 text-center">
                                <template x-for="weekday in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']" :key="`${calendarMonth.key}-${weekday}`">
                                    <span class="py-1 text-[0.65rem] font-semibold uppercase text-slate-400" x-text="weekday"></span>
                                </template>
                                <template x-for="blank in calendarMonth.offset" :key="`blank-${calendarMonth.key}-${blank}`">
                                    <span aria-hidden="true"></span>
                                </template>
                                <template x-for="day in calendarMonth.days" :key="`day-${calendarMonth.key}-${day}`">
                                    <button type="button"
                                            :aria-label="`${calendarMonth.label} ${day}: ${availability[dateFor(calendarMonth, day)] === '1' ? 'Available' : availability[dateFor(calendarMonth, day)] === '0' ? 'Not available' : 'Uses zone default'}`"
                                            :aria-pressed="selectedDates.includes(dateFor(calendarMonth, day))"
                                            :disabled="!isInRange(dateFor(calendarMonth, day))"
                                            @click="toggleDate(dateFor(calendarMonth, day))"
                                            :class="{
                                                'bg-emerald-600 text-white hover:bg-emerald-700': availability[dateFor(calendarMonth, day)] === '1',
                                                'bg-rose-600 text-white hover:bg-rose-700': availability[dateFor(calendarMonth, day)] === '0',
                                                'bg-slate-100 text-slate-700 hover:bg-slate-200': availability[dateFor(calendarMonth, day)] === undefined,
                                                'cursor-not-allowed opacity-25': !isInRange(dateFor(calendarMonth, day))
                                            }"
                                            class="relative mx-auto flex h-8 w-8 items-center justify-center rounded-lg text-xs font-semibold transition sm:h-9 sm:w-9"
                                            :style="selectedDates.includes(dateFor(calendarMonth, day)) ? 'outline: 2px solid #2563eb; outline-offset: 2px' : ''"
                                            x-text="day"></button>
                                </template>
                            </div>
                        </article>
                    </template>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-2xl text-xs leading-relaxed text-slate-500">Dates set here become the park-wide default for every zone. Choose “Use zone default” to remove a park-wide date status.</p>
                    <button type="submit" class="inline-flex justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-4 focus:ring-slate-200">Save park calendar</button>
                </div>
            </form>
        </section>
    @else
        <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="mt-4 font-bold text-slate-900">Select a national park to open its calendar</h2>
            <p class="mt-1 text-sm text-slate-500">You can select dates across all twelve months and apply one availability status to all of them.</p>
        </section>
    @endif

    @if ($selectedPark)
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5 sm:px-8">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Step 2</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Create zones for {{ $selectedPark->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">Park-wide dates apply to every zone by default. You can override individual dates for each zone.</p>
            </div>

            <form method="POST" action="{{ route('admin.slots.store') }}" class="space-y-6 p-6 sm:p-8"
                  x-data="{
                      zones: @js(old('zones', [['name' => '', 'default_available' => '1', 'dates' => []]])),
                      parkDates: @js($parkAvailability),
                      rangeStart: @js($calendarStartDate->toDateString()),
                      rangeEnd: @js($calendarEndDate->toDateString()),
                      activeMonthKey: @js($calendarStartDate->year.'-'.($calendarStartDate->month - 1)),
                      addZone() { this.zones.push({ name: '', default_available: '1', dates: [] }); },
                      addDate(zone) { zone.dates.push({ date: '', is_available: '1' }); },
                      hasZoneDate(zone, date) { return zone.dates.some(override => override.date === date); },
                      calendarMonths() {
                          const start = new Date(`${this.rangeStart}T00:00:00`);
                          const end = new Date(`${this.rangeEnd}T00:00:00`);
                          const months = [];
                          for (let cursor = new Date(start.getFullYear(), start.getMonth(), 1); cursor <= end; cursor.setMonth(cursor.getMonth() + 1)) {
                              months.push({
                                  key: `${cursor.getFullYear()}-${cursor.getMonth()}`,
                                  year: cursor.getFullYear(),
                                  month: cursor.getMonth(),
                                  label: new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(cursor),
                                  offset: new Date(cursor.getFullYear(), cursor.getMonth(), 1).getDay(),
                                  days: new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate()
                              });
                          }
                          return months;
                      },
                      dateFor(calendarMonth, day) {
                          return `${calendarMonth.year}-${String(calendarMonth.month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                      },
                      isInRange(date) { return date >= this.rangeStart && date <= this.rangeEnd; },
                      hasParkDate(date) { return this.parkDates[date] !== undefined; },
                      addParkDateOverride(zone, date) {
                          if (!this.hasZoneDate(zone, date)) {
                              const isAvailable = this.hasParkDate(date) ? this.parkDates[date] : zone.default_available === '1';
                              zone.dates.push({ date, is_available: isAvailable ? '1' : '0' });
                          }
                      },
                      removeDate(zone, index) { zone.dates.splice(index, 1); }
                  }">
                @csrf
                <input type="hidden" name="national_park_id" value="{{ $selectedPark->id }}">

                <template x-for="(zone, zoneIndex) in zones" :key="zoneIndex">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400" x-text="`Zone ${zoneIndex + 1}`"></p>
                                <h3 class="mt-1 font-bold text-slate-900">Zone details</h3>
                            </div>
                            <button type="button" x-show="zones.length > 1" @click="zones.splice(zoneIndex, 1)"
                                    class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Remove zone</button>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label :for="`zone-name-${zoneIndex}`" class="mb-2 block text-sm font-semibold text-slate-700">Zone name <span class="text-red-500">*</span></label>
                                <input :id="`zone-name-${zoneIndex}`" :name="`zones[${zoneIndex}][name]`" x-model="zone.name"
                                       type="text" maxlength="255" required placeholder="e.g. Dhikala Zone"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                            </div>
                            <div>
                                <label :for="`zone-default-${zoneIndex}`" class="mb-2 block text-sm font-semibold text-slate-700">Zone default when no park date is set <span class="text-red-500">*</span></label>
                                <select :id="`zone-default-${zoneIndex}`" :name="`zones[${zoneIndex}][default_available]`" x-model="zone.default_available" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                                    <option value="1">Available</option>
                                    <option value="0">Not available</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Zone-specific date overrides <span class="font-normal text-slate-400">(optional)</span></p>
                                    <p class="mt-1 text-xs text-slate-500">These dates take precedence over the park-wide calendar for this zone only.</p>
                                </div>
                                <button type="button" @click="addDate(zone)"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                    <span class="text-lg leading-none">+</span> Add date
                                </button>
                            </div>

                            <div class="mt-4 rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Park dates selected above</p>
                                <p class="mt-1 text-xs text-slate-500">Colors show park defaults. Select any date to add or edit a zone-specific override.</p>
                                <div class="mt-2 flex flex-wrap gap-3 text-[0.65rem] font-medium text-slate-600">
                                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-emerald-600"></span>Available</span>
                                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-rose-600"></span>Not available</span>
                                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-slate-200"></span>Uses zone fallback</span>
                                </div>
                                <label class="mt-3 block text-xs font-semibold text-slate-600">
                                    Show month
                                    <select x-model="activeMonthKey" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                        <template x-for="calendarMonth in calendarMonths()" :key="`month-option-${calendarMonth.key}`">
                                            <option :value="calendarMonth.key" x-text="calendarMonth.label"></option>
                                        </template>
                                    </select>
                                </label>
                                <div class="mt-3">
                                    <template x-for="calendarMonth in calendarMonths().filter(month => month.key === activeMonthKey)" :key="calendarMonth.key">
                                        <article class="w-full max-w-md rounded-xl border border-slate-200 p-3 sm:p-4">
                                            <h4 class="mb-2 text-xs font-bold text-slate-900" x-text="calendarMonth.label"></h4>
                                            <div class="grid grid-cols-7 gap-1 text-center">
                                                <template x-for="weekday in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']" :key="`${calendarMonth.key}-${weekday}`">
                                                    <span class="py-1 text-[0.65rem] font-semibold uppercase text-slate-400" x-text="weekday"></span>
                                                </template>
                                                <template x-for="blank in calendarMonth.offset" :key="`blank-${calendarMonth.key}-${blank}`">
                                                    <span aria-hidden="true"></span>
                                                </template>
                                                <template x-for="day in calendarMonth.days" :key="`day-${calendarMonth.key}-${day}`">
                                                    <button type="button" @click="addParkDateOverride(zone, dateFor(calendarMonth, day))"
                                                            :disabled="!isInRange(dateFor(calendarMonth, day))"
                                                            :aria-label="`${calendarMonth.label} ${day}: ${hasZoneDate(zone, dateFor(calendarMonth, day)) ? 'Zone override' : hasParkDate(dateFor(calendarMonth, day)) ? (parkDates[dateFor(calendarMonth, day)] ? 'Park default available' : 'Park default not available') : 'Uses zone fallback'}`"
                                                            :aria-pressed="hasZoneDate(zone, dateFor(calendarMonth, day))"
                                                            :class="{
                                                                'bg-emerald-600 text-white hover:bg-emerald-700': hasZoneDate(zone, dateFor(calendarMonth, day)) ? zone.dates.find(override => override.date === dateFor(calendarMonth, day)).is_available === '1' : hasParkDate(dateFor(calendarMonth, day)) && parkDates[dateFor(calendarMonth, day)],
                                                                'bg-rose-600 text-white hover:bg-rose-700': hasZoneDate(zone, dateFor(calendarMonth, day)) ? zone.dates.find(override => override.date === dateFor(calendarMonth, day)).is_available === '0' : hasParkDate(dateFor(calendarMonth, day)) && !parkDates[dateFor(calendarMonth, day)],
                                                                'bg-slate-100 text-slate-700 hover:bg-slate-200': !hasZoneDate(zone, dateFor(calendarMonth, day)) && !hasParkDate(dateFor(calendarMonth, day)),
                                                                'cursor-not-allowed opacity-25': !isInRange(dateFor(calendarMonth, day)),
                                                                'outline outline-2 outline-offset-1 outline-blue-600': hasZoneDate(zone, dateFor(calendarMonth, day))
                                                            }"
                                                            class="mx-auto flex h-8 w-8 items-center justify-center rounded-lg text-xs font-semibold transition sm:h-9 sm:w-9"
                                                            x-text="day"></button>
                                                </template>
                                            </div>
                                        </article>
                                    </template>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3">
                                <template x-for="(date, dateIndex) in zone.dates" :key="dateIndex">
                                    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                                        <div>
                                            <label :for="`zone-date-${zoneIndex}-${dateIndex}`" class="mb-1.5 block text-xs font-medium text-slate-600">Date</label>
                                            <input :id="`zone-date-${zoneIndex}-${dateIndex}`" :name="`zones[${zoneIndex}][dates][${dateIndex}][date]`" x-model="date.date"
                                                   type="date"
                                                   class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                        </div>
                                        <div>
                                            <label :for="`zone-date-status-${zoneIndex}-${dateIndex}`" class="mb-1.5 block text-xs font-medium text-slate-600">Status on this date</label>
                                            <select :id="`zone-date-status-${zoneIndex}-${dateIndex}`" :name="`zones[${zoneIndex}][dates][${dateIndex}][is_available]`" x-model="date.is_available"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                                <option value="1">Available</option>
                                                <option value="0">Not available</option>
                                            </select>
                                        </div>
                                        <button type="button" @click="removeDate(zone, dateIndex)" aria-label="Remove date"
                                                class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600">Remove</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" @click="addZone()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <span class="text-lg leading-none">+</span> Add another zone
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                        Save zones
                    </button>
                </div>
            </form>
        </section>
    @endif

    <section class="space-y-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Configured zones</h2>
            <p class="mt-1 text-sm text-slate-500">
                @if ($selectedPark)
                    Park-wide dates apply to each zone by default. Zone-specific date overrides take precedence for that zone.
                @else
                    Select a national park to view its configured zones.
                @endif
            </p>
        </div>

        @if ($selectedPark)
            <a href="{{ route('admin.slots.index', ['months' => $months]) }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">All national parks</a>

        @forelse ($zones as $zone)
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">{{ $zone->nationalPark->name }}</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $zone->name }}</h3>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $zone->default_available ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $zone->default_available ? 'bg-emerald-600' : 'bg-slate-500' }}"></span>
                        Zone fallback: {{ $zone->default_available ? 'Available' : 'Not available' }}
                    </span>
                </div>

                <div class="grid gap-6 p-5 sm:p-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
                    <form method="POST" action="{{ route('admin.slots.update', $zone) }}"
                          x-data="{
                              dates: @js($zone->slots->map(fn ($slot) => ['date' => $slot->slot_date->format('Y-m-d'), 'is_available' => $slot->is_available ? '1' : '0'])->values()),
                              parkDates: @js($parkAvailability),
                              rangeStart: @js($calendarStartDate->toDateString()),
                              rangeEnd: @js($calendarEndDate->toDateString()),
                              activeMonthKey: @js($calendarStartDate->year.'-'.($calendarStartDate->month - 1)),
                              defaultAvailable: @js($zone->default_available ? '1' : '0'),
                              hasOverride(date) { return this.dates.some(override => override.date === date); },
                              calendarMonths() {
                                  const start = new Date(`${this.rangeStart}T00:00:00`);
                                  const end = new Date(`${this.rangeEnd}T00:00:00`);
                                  const months = [];
                                  for (let cursor = new Date(start.getFullYear(), start.getMonth(), 1); cursor <= end; cursor.setMonth(cursor.getMonth() + 1)) {
                                      months.push({
                                          key: `${cursor.getFullYear()}-${cursor.getMonth()}`,
                                          year: cursor.getFullYear(),
                                          month: cursor.getMonth(),
                                          label: new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(cursor),
                                          offset: new Date(cursor.getFullYear(), cursor.getMonth(), 1).getDay(),
                                          days: new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate()
                                      });
                                  }
                                  return months;
                              },
                              dateFor(calendarMonth, day) {
                                  return `${calendarMonth.year}-${String(calendarMonth.month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                              },
                              isInRange(date) { return date >= this.rangeStart && date <= this.rangeEnd; },
                              hasParkDate(date) { return this.parkDates[date] !== undefined; },
                              addParkDateOverride(date) {
                                  if (!this.hasOverride(date)) {
                                      const isAvailable = this.hasParkDate(date) ? this.parkDates[date] : this.defaultAvailable === '1';
                                      this.dates.push({ date, is_available: isAvailable ? '1' : '0' });
                                  }
                              }
                          }"
                          class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="edit-zone-name-{{ $zone->id }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Zone name</label>
                            <input id="edit-zone-name-{{ $zone->id }}" type="text" name="name" required maxlength="255" value="{{ $zone->name }}"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label for="edit-zone-default-{{ $zone->id }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Zone fallback status</label>
                            <select id="edit-zone-default-{{ $zone->id }}" name="default_available" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                <option value="1" @selected($zone->default_available)>Available</option>
                                <option value="0" @selected(! $zone->default_available)>Not available</option>
                            </select>
                        </div>

                        <div class="space-y-3 border-t border-slate-100 pt-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Zone-specific date overrides</p>
                                    <p class="text-xs text-slate-500">These take precedence over the park calendar.</p>
                                </div>
                                <button type="button" @click="dates.push({ date: '', is_available: '1' })"
                                        class="rounded-lg px-2.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">+ Add date</button>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Park dates selected above</p>
                                <p class="mt-1 text-xs text-slate-500">Colors show park defaults. Select any date to add or edit a zone-specific override.</p>
                                <div class="mt-2 flex flex-wrap gap-3 text-[0.65rem] font-medium text-slate-600">
                                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-emerald-600"></span>Available</span>
                                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-rose-600"></span>Not available</span>
                                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-slate-200"></span>Uses zone fallback</span>
                                </div>
                                <label class="mt-3 block text-xs font-semibold text-slate-600">
                                    Show month
                                    <select x-model="activeMonthKey" class="mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                        <template x-for="calendarMonth in calendarMonths()" :key="`month-option-${calendarMonth.key}`">
                                            <option :value="calendarMonth.key" x-text="calendarMonth.label"></option>
                                        </template>
                                    </select>
                                </label>
                                <div class="mt-3">
                                    <template x-for="calendarMonth in calendarMonths().filter(month => month.key === activeMonthKey)" :key="calendarMonth.key">
                                        <article class="w-full max-w-md rounded-xl border border-slate-200 p-3 sm:p-4">
                                            <h4 class="mb-2 text-xs font-bold text-slate-900" x-text="calendarMonth.label"></h4>
                                            <div class="grid grid-cols-7 gap-1 text-center">
                                                <template x-for="weekday in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']" :key="`${calendarMonth.key}-${weekday}`">
                                                    <span class="py-1 text-[0.65rem] font-semibold uppercase text-slate-400" x-text="weekday"></span>
                                                </template>
                                                <template x-for="blank in calendarMonth.offset" :key="`blank-${calendarMonth.key}-${blank}`">
                                                    <span aria-hidden="true"></span>
                                                </template>
                                                <template x-for="day in calendarMonth.days" :key="`day-${calendarMonth.key}-${day}`">
                                                    <button type="button" @click="addParkDateOverride(dateFor(calendarMonth, day))"
                                                            :disabled="!isInRange(dateFor(calendarMonth, day))"
                                                            :aria-label="`${calendarMonth.label} ${day}: ${hasOverride(dateFor(calendarMonth, day)) ? 'Zone override' : hasParkDate(dateFor(calendarMonth, day)) ? (parkDates[dateFor(calendarMonth, day)] ? 'Park default available' : 'Park default not available') : 'Uses zone fallback'}`"
                                                            :aria-pressed="hasOverride(dateFor(calendarMonth, day))"
                                                            :class="{
                                                                'bg-emerald-600 text-white hover:bg-emerald-700': hasOverride(dateFor(calendarMonth, day)) ? dates.find(override => override.date === dateFor(calendarMonth, day)).is_available === '1' : hasParkDate(dateFor(calendarMonth, day)) && parkDates[dateFor(calendarMonth, day)],
                                                                'bg-rose-600 text-white hover:bg-rose-700': hasOverride(dateFor(calendarMonth, day)) ? dates.find(override => override.date === dateFor(calendarMonth, day)).is_available === '0' : hasParkDate(dateFor(calendarMonth, day)) && !parkDates[dateFor(calendarMonth, day)],
                                                                'bg-slate-100 text-slate-700 hover:bg-slate-200': !hasOverride(dateFor(calendarMonth, day)) && !hasParkDate(dateFor(calendarMonth, day)),
                                                                'cursor-not-allowed opacity-25': !isInRange(dateFor(calendarMonth, day)),
                                                                'outline outline-2 outline-offset-1 outline-blue-600': hasOverride(dateFor(calendarMonth, day))
                                                            }"
                                                            class="mx-auto flex h-8 w-8 items-center justify-center rounded-lg text-xs font-semibold transition sm:h-9 sm:w-9"
                                                            x-text="day"></button>
                                                </template>
                                            </div>
                                        </article>
                                    </template>
                                </div>
                            </div>
                            <p x-show="dates.length === 0" class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-500">
                                No zone-specific overrides. This zone follows the park calendar, then its fallback status.
                            </p>
                            <template x-for="(date, index) in dates" :key="index">
                                <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-2">
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <input type="date" :name="`dates[${index}][date]`" x-model="date.date" :aria-label="`Override date ${index + 1}`"
                                               class="min-w-0 rounded-lg border border-slate-300 px-2.5 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                        <select :name="`dates[${index}][is_available]`" x-model="date.is_available" :aria-label="`Availability for date ${index + 1}`"
                                                class="min-w-0 rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                                            <option value="1">Available</option>
                                            <option value="0">Not available</option>
                                        </select>
                                    </div>
                                    <button type="button" @click="dates.splice(index, 1)" :aria-label="`Remove date ${index + 1}`"
                                            class="rounded-lg px-2 text-xs font-medium text-slate-400 hover:bg-red-50 hover:text-red-600">Remove</button>
                                </div>
                            </template>
                        </div>

                        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Save zone</button>
                    </form>

                    <div class="lg:border-l lg:border-slate-100 lg:pl-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Zone-specific dates</h4>
                                <p class="mt-1 text-xs text-slate-500">These statuses override the park-wide calendar for this zone.</p>
                            </div>
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-600">{{ $zone->slots->count() }} override{{ $zone->slots->count() === 1 ? '' : 's' }}</span>
                        </div>

                        @if ($zone->slots->isEmpty())
                            <div class="mt-4 rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center">
                                <p class="text-sm font-medium text-slate-700">No zone-specific dates</p>
                                <p class="mt-1 text-xs text-slate-500">This zone uses the park calendar or its fallback status.</p>
                            </div>
                        @else
                            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                @foreach ($zone->slots as $slot)
                                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-3.5 py-3">
                                        <span class="text-sm font-medium text-slate-700">{{ $slot->slot_date->format('M j, Y') }}</span>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $slot->is_available ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $slot->is_available ? 'Available' : 'Not available' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.slots.destroy', $zone) }}" class="mt-5 border-t border-slate-100 pt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this zone and all its date overrides?')"
                                    class="text-xs font-semibold text-red-600 hover:text-red-700">Delete zone</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                <h3 class="font-bold text-slate-900">No zones configured for this park yet</h3>
                <p class="mt-1 text-sm text-slate-500">Create the first zone after setting this park's availability calendar.</p>
            </div>
        @endforelse
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($zones->groupBy('national_park_id') as $parkZones)
                    @php($park = $parkZones->first()->nationalPark)
                    <article class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $park->name }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $parkZones->count() }} configured zone{{ $parkZones->count() === 1 ? '' : 's' }}</p>
                        </div>
                        <a href="{{ route('admin.slots.index', ['park' => $park->id, 'months' => $months]) }}" class="shrink-0 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">View zones</a>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <h3 class="font-bold text-slate-900">No zones configured yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Choose a national park above to create its zones.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </section>
</div>
@endsection
