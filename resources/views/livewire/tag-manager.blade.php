<div>
    <div class="content-page">
        <span id="item_id" hidden></span>
        <div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h3>Gestion des Tags</h3>
        <button class="btn btn-primary" wire:click="create">Ajouter un tag</button>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Slug</th>
            <th>Créé le</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($tags as $tag)
            <tr>
                <td>{{ $tag->name }}</td>
                <td>{{ $tag->slug }}</td>
                <td>{{ $tag->created_at->format('d/m/Y') }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" wire:click="edit({{ $tag->id }})">
                        Modifier
                    </button>

                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Supprimer ce tag ?')"
                            wire:click="delete({{ $tag->id }})">
                        Supprimer
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

{{ $tags->links() }}
        </div>
<!-- MODAL -->
    <div wire:ignore.self class="modal fade" id="tagModal" tabindex="-1">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEditing ? 'Modifier le tag' : 'Créer un tag' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label>Nom</label>
                        <input type="text" class="form-control" wire:model="name">

                        @error('name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditing ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.addEventListener('openModal', () => {
        new bootstrap.Modal(document.getElementById('tagModal')).show();
    });

    window.addEventListener('closeModal', () => {
        bootstrap.Modal.getInstance(document.getElementById('tagModal')).hide();
    });
</script>
