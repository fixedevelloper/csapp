<div>
    <div class="content-page">
        <div class="content">
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-body">
            <div class="mb-3">
            <label>Titre</label>
            <input type="text" class="form-control" wire:model="title">
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Contenu</label>

         <textarea class="form-control" rows="4" name="content" wire:model="content"></textarea>
            @error('content') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
                <div class="mb-3">
                    <label>Meta Titre</label>
                    <input type="text" class="form-control" wire:model="meta_title">
                    @error('meta_title') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="mb-3">
                    <label>Meta- description</label>
                    <textarea class="form-control" rows="4" wire:model="meta_description"></textarea>
                    @error('meta_description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="mb-3">
                    <label>Mots cles</label>

                    <textarea class="form-control" rows="4" wire:model="meta_keywords"></textarea>
                    @error('meta_keywords') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
        <div class="mb-3">
            <label>Catégorie</label>
            <select class="form-control" wire:model="categories" multiple>
                @foreach ($availableCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
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
            @if ($image)
                <img src="{{ $image->temporaryUrl() }}" class="mt-2" width="150">
            @elseif($isEditing && $postId)
                @php $p = \App\Models\Post::find($postId); @endphp
                @if ($p && $p->getFirstMediaUrl('posts'))
                    <img src="{{ $p->getFirstMediaUrl('posts') }}" class="mt-2" width="150">
                @endif
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            {{ $isEditing ? 'Mettre à jour' : 'Créer' }}
        </button>
            </div></div>    </form>

</div>
    </div></div>

@push('scripts')
    <!-- Include Bubble Theme -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">

    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endpush
