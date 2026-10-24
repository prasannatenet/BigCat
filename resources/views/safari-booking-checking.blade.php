<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="5;url={{ route('safari-booking.confirmation', ['confirmationToken' => $bookingRequest->confirmation_token]) }}">
    <title>Checking safari availability</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 20px; background: #f6ead8; font-family: Arial, sans-serif; color: #171717; }
        .overlay { position: fixed; inset: 0; display: grid; place-items: center; padding: 20px; background: #17171788; }
        .dialog { width: min(430px, 100%); padding: 34px; border-radius: 18px; background: white; text-align: center; box-shadow: 0 24px 80px #0004; }
        .spinner { width: 50px; height: 50px; margin: 0 auto 18px; border: 5px solid #ffeadc; border-top-color: #ff6900; border-radius: 50%; animation: spin 0.8s linear infinite; }
        h1 { margin: 0 0 10px; font-size: 22px; }
        p { margin: 0; color: #555; font-size: 14px; line-height: 1.6; }
        .countdown { margin-top: 17px; color: #e85b00; font-weight: 700; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) { .spinner { animation-duration: 2s; } }
    </style>
</head>
<body>
    <main class="overlay">
        <section class="dialog" role="status" aria-live="polite">
            <div class="spinner" aria-hidden="true"></div>
            <h1>Checking slot availability</h1>
            <p>Your details are saved. We’re preparing the availability results for your selected dates and zones.</p>
            <p class="countdown" id="countdown">Redirecting in 5 seconds…</p>
        </section>
    </main>
    <script>
        const confirmationUrl = @json(route('safari-booking.confirmation', ['confirmationToken' => $bookingRequest->confirmation_token]));
        let secondsRemaining = 5;

        const countdownInterval = window.setInterval(() => {
            secondsRemaining -= 1;
            document.getElementById('countdown').textContent = `Redirecting in ${secondsRemaining} second${secondsRemaining === 1 ? '' : 's'}…`;
        }, 1000);

        window.setTimeout(() => {
            window.clearInterval(countdownInterval);
            window.location.replace(confirmationUrl);
        }, 5000);
    </script>
</body>
</html>
