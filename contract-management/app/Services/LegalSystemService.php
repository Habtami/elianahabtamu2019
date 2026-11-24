<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LegalSystemService
{
    /**
     * Resolve the email addresses of the concerned party ("ለሚመረከተው")
     * for a contract, falling back to the configured project manager email.
     */
    public function getConcernedPartyEmails(Contract $contract): array
    {
        if (app()->environment('local')) {
            $testRecipients = $this->testRecipientEmails();

            if ($testRecipients !== []) {
                Log::info('Local environment detected; using the test recipient for the performance guarantee alert.', [
                    'contract_id' => $contract->id,
                ]);

                return $testRecipients;
            }
        }

        $baseUrl = rtrim((string) config('services.legal_system.url'), '/');
        $token = (string) config('services.legal_system.token');

        if ($baseUrl === '' || $token === '') {
            Log::warning('Legal system is not configured; using fallback recipients for the performance guarantee alert.', [
                'contract_id' => $contract->id,
            ]);

            return $this->fallbackEmails();
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout((int) config('services.legal_system.timeout', 5))
                ->get($baseUrl.config('services.legal_system.concerned_party_endpoint'), [
                    'department' => $contract->department,
                    'supplier_name' => $contract->supplier_name,
                    'contract_name' => $contract->name,
                    'contract_id' => $contract->id,
                ]);

            if ($response->failed()) {
                Log::error('Legal system returned an error while resolving concerned party emails.', [
                    'contract_id' => $contract->id,
                    'status' => $response->status(),
                ]);

                return $this->fallbackEmails();
            }

            $emails = $this->extractEmails($response->json());

            if ($emails !== []) {
                return $emails;
            }

            Log::warning('Legal system returned no concerned party email; using fallback recipients.', [
                'contract_id' => $contract->id,
            ]);

            return $this->fallbackEmails();
        } catch (\Throwable $e) {
            Log::error('Legal system request failed; using fallback recipients for the performance guarantee alert.', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage(),
            ]);

            return $this->fallbackEmails();
        }
    }

    protected function extractEmails(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        $candidates = [];

        if (isset($payload['emails'])) {
            $candidates = array_merge($candidates, (array) $payload['emails']);
        }

        if (isset($payload['email'])) {
            $candidates[] = $payload['email'];
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            $data = $payload['data'];

            if (isset($data['emails'])) {
                $candidates = array_merge($candidates, (array) $data['emails']);
            } elseif (isset($data['email'])) {
                $candidates[] = $data['email'];
            } elseif (array_is_list($data)) {
                $candidates = array_merge($candidates, $data);
            }
        }

        if (array_is_list($payload)) {
            $candidates = array_merge($candidates, $payload);
        }

        $emails = [];

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                $candidate = $candidate['email'] ?? null;
            }

            if (! is_string($candidate)) {
                continue;
            }

            $candidate = trim($candidate);

            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                $emails[] = mb_strtolower($candidate);
            }
        }

        return array_values(array_unique($emails));
    }

    /**
     * Fall back to the test recipient (local/unreachable), then the configured
     * project manager email, then active admins.
     */
    public function fallbackEmails(): array
    {
        $testEmails = $this->testRecipientEmails();

        if ($testEmails !== []) {
            Log::warning('Using the configured test recipient for the performance guarantee alert.', [
                'recipients' => $testEmails,
            ]);

            return $testEmails;
        }

        $emails = $this->normalizeEmails((string) config('services.legal_system.fallback_email'));

        if ($emails !== []) {
            return $emails;
        }

        $adminEmails = User::query()
            ->where('status', true)
            ->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])
            ->pluck('email')
            ->filter(fn ($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->map(fn ($email) => mb_strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

        if ($adminEmails === []) {
            Log::error('No fallback recipient is configured for performance guarantee alerts.');
        }

        return $adminEmails;
    }

    /**
     * The dedicated test recipient used in local environments and whenever
     * the legal system cannot supply a concerned party email.
     */
    public function testRecipientEmails(): array
    {
        return $this->normalizeEmails((string) config('services.legal_system.test_recipient'));
    }

    protected function normalizeEmails(string $configured): array
    {
        $emails = [];

        foreach (preg_split('/[,;]+/', $configured) ?: [] as $candidate) {
            $candidate = trim($candidate);

            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                $emails[] = mb_strtolower($candidate);
            }
        }

        return array_values(array_unique($emails));
    }
}
