<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            Results for {{ $character->name }}
            <small class="text-muted">(ID: {{ $character->character_id }})</small>
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Entity</th>
                        <th>Type</th>
                        <th>Entry Type</th>
                        <th>Date</th>
                        <th>Context</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ $result->entity_id }}</span>
                            {{ $this->getEntityName($result->entity_id, $result->entity_type) }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $this->getEntityBadgeClass($result->entity_type) }}">
                                {{ ucfirst($result->entity_type) }}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-{{ $this->getEntryIcon($result->entry_type) }}"></i>
                            {{ ucfirst($result->entry_type) }}
                        </td>
                        <td>
                            {{ $result->entry_date->format('Y-m-d H:i') }}
                            <br>
                            <small>{{ $result->entry_date->diffForHumans() }}</small>
                        </td>
                        <td class="text-wrap" style="max-width: 300px;">
                            {!! $result->context !!}
                        </td>
                        <td>
                            <a href="https://evewho.com/{{ $result->entity_type }}/{{ $result->entity_id }}" 
                               target="_blank"
                               class="btn btn-sm btn-outline-info"
                               title="EVE Who Lookup">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                            <h4>No Suspicious Activity Found</h4>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($results->hasPages())
    <div class="card-footer">
        {{ $results->links() }}
    </div>
    @endif
</div>