<div>
    <div class="content-page">
        <div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h3>Newsletter <small class="text-muted">({{ $total }} inscrits)</small></h3>
        <a class="btn btn-primary" href="{{ route('newsletter.export') }}">Exporter en CSV</a>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <input type="search" class="form-control mb-3" placeholder="Rechercher un email…" wire:model.live.debounce.300ms="search">

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Email</th>
            <th>Inscrit le</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($subscribers as $subscriber)
            <tr>
                <td>{{ $subscriber->email }}</td>
                <td>{{ $subscriber->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <button class="btn btn-danger btn-sm"
                            wire:confirm="Supprimer {{ $subscriber->email }} de la newsletter ?"
                            wire:click="delete({{ $subscriber->id }})">
                        Supprimer
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-muted">Aucun inscrit.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

{{ $subscribers->links() }}
        </div>
    </div>
</div>
