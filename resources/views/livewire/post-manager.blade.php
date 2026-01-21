<div class="content-page">
    <div class="content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Gestion des Posts</h3>
            <button wire:click="create" class="btn btn-primary">
                <i class="fa fa-plus"></i> Ajouter
            </button>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Catégories</th>
                    <th>Tags</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>
                            @if ($post->getFirstMediaUrl('image'))
                                <img
                                    src="{{ $post->getFirstMediaUrl('image') }}"
                                    width="60"
                                    class="rounded"
                                    loading="lazy"
                                >
                            @endif
                        </td>

                        <td>{{ Str::limit($post->title, 50) }}</td>

                        <td>
                            @foreach ($post->categories as $category)
                                <span class="badge bg-info">{{ $category->name }}</span>
                            @endforeach
                        </td>

                        <td>
                            @foreach ($post->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                            @endforeach
                        </td>

                        <td>
                            <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'warning' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>

                        <td>
                            <button
                                wire:click="edit({{ $post->id }})"
                                class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button
                                wire:click="deleteConfirm({{ $post->id }})"
                                class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Aucun post trouvé.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $posts->links() }}
        </div>

    </div>
</div>
@push('scripts')
    <script>
        window.addEventListener('confirm-delete', event => {
            if (confirm('Voulez-vous vraiment supprimer ce post ?')) {
                Livewire.emit('confirmDelete', event.detail.id);
            }
        });
    </script>
@endpush
