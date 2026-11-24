<?php

namespace Tests\Unit;

use App\Models\Contract;
use App\Services\LegalSystemService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LegalSystemServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.legal_system.url' => 'https://legal.example.test',
            'services.legal_system.token' => 'test-token',
            'services.legal_system.timeout' => 3,
            'services.legal_system.concerned_party_endpoint' => '/api/concerned-party/emails',
            'services.legal_system.fallback_email' => 'project.manager@example.com',
            'services.legal_system.test_recipient' => 'qa.fallback@example.com',
        ]);
    }

    private function contract(): Contract
    {
        return new Contract([
            'department' => 'cmd',
            'supplier_name' => 'Acme Trading',
            'name' => 'Supply of Medicines',
        ]);
    }

    public function test_it_fetches_concerned_party_emails_from_the_legal_system(): void
    {
        Http::fake([
            'legal.example.test/*' => Http::response([
                'data' => ['emails' => ['Concerned@Example.com', 'legal@example.com']],
            ]),
        ]);

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['concerned@example.com', 'legal@example.com'], $emails);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/concerned-party/emails')
                && $request['department'] === 'cmd'
                && $request['supplier_name'] === 'Acme Trading'
                && $request->hasHeader('Authorization', 'Bearer test-token');
        });
    }

    public function test_it_falls_back_when_the_legal_system_returns_an_error(): void
    {
        Http::fake([
            'legal.example.test/*' => Http::response('Service unavailable', 503),
        ]);

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['qa.fallback@example.com'], $emails);
    }

    public function test_it_falls_back_when_no_valid_email_is_returned(): void
    {
        Http::fake([
            'legal.example.test/*' => Http::response(['emails' => ['not-an-email', '']]),
        ]);

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['qa.fallback@example.com'], $emails);
    }

    public function test_it_falls_back_when_the_legal_system_throws(): void
    {
        Http::fake([
            'legal.example.test/*' => fn () => throw new \RuntimeException('Connection timed out'),
        ]);

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['qa.fallback@example.com'], $emails);
    }

    public function test_it_uses_the_fallback_when_the_legal_system_is_not_configured(): void
    {
        config([
            'services.legal_system.url' => null,
            'services.legal_system.token' => null,
        ]);

        Http::fake();

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['qa.fallback@example.com'], $emails);
        Http::assertNothingSent();
    }

    public function test_it_uses_the_project_manager_when_no_test_recipient_is_configured(): void
    {
        config([
            'services.legal_system.test_recipient' => null,
        ]);

        Http::fake([
            'legal.example.test/*' => Http::response('Service unavailable', 503),
        ]);

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['project.manager@example.com'], $emails);
    }

    public function test_the_default_test_recipient_is_the_configured_test_mailbox(): void
    {
        config([
            'services.legal_system.test_recipient' => 'habtamudoc70@gmail.com',
        ]);

        Http::fake([
            'legal.example.test/*' => Http::response('Service unavailable', 503),
        ]);

        $emails = (new LegalSystemService)->getConcernedPartyEmails($this->contract());

        $this->assertSame(['habtamudoc70@gmail.com'], $emails);
    }
}
