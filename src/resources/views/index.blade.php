@extends('web::layout')

@section('title', 'Espionage Detector')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Espionage Detector</h3>
        <div class="card-tools">
            <a href="{{ route('seat-utilities.settings') }}" class="btn btn-sm btn-light">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h4 class="card-title">Watchlist</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('espionage-detector.save-entities') }}">
                            @csrf
                            <div class="form-group">
                                <label>Character IDs</label>
                                <textarea class="form-control" name="characters" rows="3" 
                                    placeholder="One ID per line">{{ old('characters', $characters->pluck('entity_id')->join("\n")) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Corporation IDs</label>
                                <textarea class="form-control" name="corporations" rows="3"
                                    placeholder="One ID per line">{{ old('corporations', $corporations->pluck('entity_id')->join("\n")) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Alliance IDs</label>
                                <textarea class="form-control" name="alliances" rows="3"
                                    placeholder="One ID per line">{{ old('alliances', $alliances->pluck('entity_id')->join("\n")) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-save"></i> Save Watchlist
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h4 class="card-title">Character Screening</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('espionage-detector.run-check') }}">
                            @csrf
                            <div class="form-group">
                                <label>Select Character</label>
                                <select name="character_id" class="form-control" required>
                                    @foreach(CharacterInfo::all() as $character)
                                        <option value="{{ $character->character_id }}">
                                            {{ $character->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-search"></i> Run Screening
                            </button>
                        </form>

                        <hr>

                        <h5>Recent Scans</h5>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Character</th>
                                        <th>Last Scan</th>
                                        <th>Matches</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentScans as $result)
                                    <tr>
                                        <td>{{ $result->character->name }}</td>
                                        <td>{{ $result->created_at->diffForHumans() }}</td>
                                        <td>{{ ScreeningResult::where('character_id', $result->character_id)->count() }}</td>
                                        <td>
                                            <a href="{{ route('espionage-detector.results', $result->character) }}"
                                               class="btn btn-xs btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No scans yet</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('javascript')
<script>
$(document).ready(function() {
    $('select[name="character_id"]').select2({
        placeholder: "Search character...",
        minimumInputLength: 3,
        ajax: {
            url: '{{ route('fastlookup.characters') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { query: params.term };
            },
            processResults: function (data) {
                return { results: data };
            }
        }
    });
});
</script>
@endpush