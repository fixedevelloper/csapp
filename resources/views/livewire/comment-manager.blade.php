<div>
    <div class="content-page">
        <div class="content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Commentaires <small class="text-muted">({{ $pendingCount }} en attente)</small></h3>
        <div class="btn-group">
            <button class="btn btn-sm {{ $filter === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filter', 'pending')">En attente</button>
            <button class="btn btn-sm {{ $filter === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filter', 'approved')">Publiés</button>
            <button class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filter', 'all')">Tous</button>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <input type="search" class="form-control mb-3" placeholder="Rechercher un nom, un email, un texte…" wire:model.live.debounce.300ms="search">

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Auteur</th>
            <th>Commentaire</th>
            <th>Article</th>
            <th>Reçu le</th>
            <th>Statut</th>
            <th width="190">Actions</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($comments as $comment)
            <tr wire:key="comment-{{ $comment->id }}">
                <td>
                    {{ $comment->name }}<br>
                    <small class="text-muted">{{ $comment->email }}</small>
                </td>
                <td style="white-space: pre-line;">{{ $comment->comment }}</td>
                <td>{{ $comment->post ? Str::limit($comment->post->title, 40) : '—' }}</td>
                <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if ($comment->approved)
                        <span class="badge bg-success">Publié</span>
                    @else
                        <span class="badge bg-warning">En attente</span>
                    @endif
                </td>
                <td>
                    @if ($comment->approved)
                        <button class="btn btn-secondary btn-sm" wire:click="unapprove({{ $comment->id }})">Retirer</button>
                    @else
                        <button class="btn btn-success btn-sm" wire:click="approve({{ $comment->id }})">Approuver</button>
                    @endif
                    <button class="btn btn-danger btn-sm"
                            wire:confirm="Supprimer définitivement ce commentaire ?"
                            wire:click="delete({{ $comment->id }})">
                        Supprimer
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Aucun commentaire.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

{{ $comments->links() }}
        </div>
    </div>
</div>
