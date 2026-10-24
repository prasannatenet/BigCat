<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailSettingsController extends Controller
{
    public function index(): View
    {
        $mailSettings = MailSetting::query()->first();

        return view('admin.email-settings.index', compact('mailSettings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['required', 'in:ssl,tls,none'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:2000'],
            'from_email' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'test_recipient_email' => ['required_if:send_test,1', 'nullable', 'email', 'max:255'],
            'send_test' => ['sometimes', 'in:1'],
        ]);

        $mailSettings = MailSetting::query()->firstOrNew(['id' => 1]);
        $mailSettings->fill([
            'host' => $validated['host'],
            'port' => $validated['port'],
            'encryption' => $validated['encryption'],
            'username' => $validated['username'] ?? null,
            'from_email' => $validated['from_email'],
            'from_name' => $validated['from_name'],
            'test_recipient_email' => $validated['test_recipient_email'] ?? null,
        ]);
        if (! empty($validated['password'])) {
            $mailSettings->password = $validated['password'];
        }
        $mailSettings->save();

        if ($request->boolean('send_test')) {
            $this->configureMailer($mailSettings);

            try {
                Mail::mailer('smtp')->raw(
                    'This is a test email from your BigCatt admin SMTP configuration.',
                    function (Message $message) use ($mailSettings): void {
                        $message->to($mailSettings->test_recipient_email)
                            ->subject('SMTP configuration test')
                            ->from($mailSettings->from_email, $mailSettings->from_name);
                    }
                );
            } catch (Throwable) {
                return back()->withInput($request->except('password'))
                    ->withErrors(['smtp' => 'Settings were saved, but the test email could not be sent. Check the SMTP details and try again.']);
            }

            return redirect()->route('admin.email-settings.index')
                ->with('status', 'SMTP settings saved and the test email was sent.');
        }

        return redirect()->route('admin.email-settings.index')
            ->with('status', 'SMTP settings saved successfully.');
    }

    private function configureMailer(MailSetting $mailSettings): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'scheme' => $mailSettings->encryption === 'ssl' ? 'smtps' : 'smtp',
                'auto_tls' => $mailSettings->encryption === 'tls',
                'host' => $mailSettings->host,
                'port' => $mailSettings->port,
                'username' => $mailSettings->username,
                'password' => $mailSettings->password,
                'timeout' => 15,
                'local_domain' => parse_url((string) config('app.url'), PHP_URL_HOST),
            ],
            'mail.from' => [
                'address' => $mailSettings->from_email,
                'name' => $mailSettings->from_name,
            ],
        ]);
        Mail::purge('smtp');
    }
}
