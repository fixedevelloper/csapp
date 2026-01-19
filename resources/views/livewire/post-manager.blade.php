<div>
    <div class="content-page">
        <div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h3>Gestion des Posts</h3>
        <button wire:click="create" class="btn btn-primary">Ajouter</button>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Image</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Tags</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($posts as $post)
            <tr>
                <td>
                    @if ($post->getFirstMediaUrl('image'))
                        <img src="{{ $post->getFirstMediaUrl('image') }}" width="60">
                    @endif
                </td>
                <td>{{ $post->title }}</td>
                <td> @foreach ($post->categories as $category)
                        <span class="badge bg-info">{{ $category->name }}</span>
                    @endforeach</td>
                <td>
                    @foreach ($post->tags as $tag)
                        <span class="badge bg-info">{{ $tag->name }}</span>
                    @endforeach
                </td>
                <td>{{ $post->status }}</td>
                <td>
                    <button wire:click="edit({{ $post->id }})" class="btn btn-sm btn-warning">Edit</button>
                    <button wire:click="delete({{ $post->id }})" class="btn btn-sm btn-danger"
                            onclick="return confirm('Supprimer ?')">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
        </div></div>
{{ $posts->links() }}

<!-- Modal -->
    <div wire:ignore.self class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEditing ? 'Modifier le post' : 'Créer un post' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Titre</label>
                            <input type="text" class="form-control" wire:model="title">
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Contenu</label>
                            <textarea class="form-control" rows="4" wire:model="content"></textarea>
                            @error('content') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Catégorie</label>
                            <select class="form-control" wire:model="categories" multiple>
                                <option value="">-- Choisir --</option>
                                @foreach ($availableCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Tags</label>
                            <select class="form-control" multiple wire:model="tags">
                                @foreach ($availableTags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" wire:model="image" class="form-control">
                            @error('image') <small class="text-danger">{{ $message }}</small> @enderror

                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="mt-2" width="150">
                            @elseif ($isEditing && $postId)
                                @php $p = \App\Models\Post::find($postId); @endphp
                                @if ($p && $p->getFirstMediaUrl('image'))
                                    <img src="{{ $p->getFirstMediaUrl('image') }}" class="mt-2" width="150">
                                @endif
                            @endif
                        </div>

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
        new bootstrap.Modal(document.getElementById('postModal')).show();
    });
    window.addEventListener('closeModal', () => {
        bootstrap.Modal.getInstance(document.getElementById('postModal')).hide();
    });
</script>

