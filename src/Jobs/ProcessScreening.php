<?php

namespace 1234nin4321\Seat\EspionageDetector\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\{
    Mail\CharacterMail,
    Contracts\CharacterContract,
    Contacts\CharacterContact,
    Wallet\CharacterWalletJournal
};
use 1234nin4321\Seat\EspionageDetector\Models\{
    ScreeningResult,
    SuspiciousEntity
};

class ProcessScreening implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    public function __construct(
        private int $characterId
    ) {}

    public function handle()
    {
        try {
            $suspicious = SuspiciousEntity::all()->groupBy('entity_type');
            
            $this->processWalletJournals($suspicious);
            $this->processMails($suspicious);
            $this->processContracts($suspicious);
            $this->processContacts($suspicious);

        } catch (\Exception $e) {
            Log::error("Screening failed for {$this->characterId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function processWalletJournals($suspicious)
    {
        CharacterWalletJournal::where('character_id', $this->characterId)
            ->cursor()
            ->each(function ($journal) use ($suspicious) {
                $this->checkEntity(
                    $journal->second_party_id,
                    $suspicious,
                    'wallet',
                    $journal->date,
                    "Transaction: {$journal->ref_type} (Amount: {$journal->amount})"
                );
            });
    }

    private function processMails($suspicious)
    {
        CharacterMail::where('character_id', $this->characterId)
            ->cursor()
            ->each(function ($mail) use ($suspicious) {
                $this->checkEntity(
                    $mail->from,
                    $suspicious,
                    'mail',
                    $mail->timestamp,
                    "Mail Subject: {$mail->subject}"
                );
            });
    }

    private function processContracts($suspicious)
    {
        CharacterContract::where('issuer_id', $this->characterId)
            ->cursor()
            ->each(function ($contract) use ($suspicious) {
                $this->checkEntity(
                    $contract->assignee_id,
                    $suspicious,
                    'contract',
                    $contract->date_issued,
                    "Contract: {$contract->title} (Type: {$contract->type})"
                );
            });
    }

    private function processContacts($suspicious)
    {
        CharacterContact::where('character_id', $this->characterId)
            ->cursor()
            ->each(function ($contact) use ($suspicious) {
                $this->checkEntity(
                    $contact->contact_id,
                    $suspicious,
                    'contact',
                    now(),
                    "Contact Type: {$contact->contact_type} (Standing: {$contact->standing})"
                );
            });
    }

    private function checkEntity($entityId, $suspicious, $entryType, $date, $context)
    {
        foreach (['character', 'corporation', 'alliance'] as $type) {
            if ($suspicious->get($type, collect())->contains('entity_id', $entityId)) {
                ScreeningResult::updateOrCreate([
                    'character_id' => $this->characterId,
                    'entity_id' => $entityId,
                    'entry_type' => $entryType,
                    'entry_date' => $date
                ], [
                    'entity_type' => $type,
                    'context' => $context
                ]);
                break;
            }
        }
    }
}