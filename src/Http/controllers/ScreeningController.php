<?php

namespace 1234nin4321\Seat\EspionageDetector\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Seat\Web\Http\Controllers\Controller;
use 1234nin4321\Seat\EspionageDetector\Models\ScreeningResult;
use 1234nin4321\Seat\EspionageDetector\Models\SuspiciousEntity;
use 1234nin4321\Seat\EspionageDetector\Jobs\ProcessScreening;
use Seat\Eveapi\Models\Character\CharacterInfo;

class ScreeningController extends Controller
{
    public function index()
    {
        $entities = SuspiciousEntity::all()->groupBy('entity_type');
        
        return view('espionage-detector::index', [
            'characters' => $entities->get('character', collect()),
            'corporations' => $entities->get('corporation', collect()),
            'alliances' => $entities->get('alliance', collect()),
            'recentScans' => ScreeningResult::with('character')
                ->latest()
                ->take(10)
                ->get()
                ->unique('character_id')
        ]);
    }

    public function saveEntities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'characters' => 'nullable|string',
            'corporations' => 'nullable|string',
            'alliances' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        SuspiciousEntity::truncate();

        $this->processEntityInput($request->characters, 'character');
        $this->processEntityInput($request->corporations, 'corporation');
        $this->processEntityInput($request->alliances, 'alliance');

        return redirect()->back()->with('success', 'Suspicious entities updated!');
    }

    private function processEntityInput(?string $input, string $type)
    {
        if (empty($input)) return;

        collect(explode("\n", $input))
            ->map(fn($id) => trim($id))
            ->filter(fn($id) => is_numeric($id) && $id > 0)
            ->each(fn($id) => SuspiciousEntity::create([
                'entity_id' => (int)$id,
                'entity_type' => $type
            ]));
    }

    public function runCheck(Request $request)
    {
        $request->validate([
            'character_id' => 'required|integer|exists:character_infos,character_id'
        ]);

        ProcessScreening::dispatch($request->character_id);

        return redirect()->back()->with(
            'success', 
            "Screening initiated for character ID: {$request->character_id}"
        );
    }

    public function results(CharacterInfo $character)
    {
        $results = ScreeningResult::where('character_id', $character->character_id)
            ->with('entity')
            ->paginate(50);

        return view('espionage-detector::partials.results', [
            'results' => $results,
            'character' => $character
        ]);
    }
}