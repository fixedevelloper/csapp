<div>
    <div class="content-page">
        <span id="item_id" hidden></span>
        <div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h3>Gestion des Catégories</h3>
        <button class="btn btn-primary" wire:click="create">Ajouter une catégorie</button>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Slug</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($categories as $cat)
            <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->slug }}</td>
                <td>{{ $cat->created_at->format('d/m/Y') }}</td>
                <td>
                    <button wire:click="edit({{ $cat->id }})" class="btn btn-warning btn-sm">Modifier</button>

                    <button wire:click="delete({{ $cat->id }})"
                            onclick="return confirm('Supprimer cette catégorie ?')"
                            class="btn btn-danger btn-sm">
                        Supprimer
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

{{ $categories->links() }}
        </div>
<!-- MODAL -->
    <div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEditing ? 'Modifier la catégorie' : 'Créer une catégorie' }}
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
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    });

    window.addEventListener('closeModal', () => {
        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
    });
</script>
