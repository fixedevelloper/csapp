<div>
    <div class="content-page">
        <span id="item_id" hidden></span>
        <div class="content">
    <div class="card">
        <div class="card-header">
            <h3>Posts</h3>
        </div>

        <div class="card-body">

            {{-- TABLE --}}
            <table class="table">
                <thead>
                <tr>
                    <th wire:click="sortBy('id')" style="cursor:pointer">
                        ID
                        @if($order_column == 'id')
                            <span>{{ $order_direction == 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>

                    <th style="width: 15%">Image</th>

                    <th wire:click="sortBy('title')" style="cursor:pointer">
                        Titre
                        @if($order_column == 'title')
                            <span>{{ $order_direction == 'asc' ? '▲' : '▼' }}</span>
                        @endif
                    </th>

                    <th>Catégorie</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @foreach($items as $key=>$agent)
                    <tr>
                        <td>{{ $items->firstItem() + $key }}</td>

                        <td>
                            <img width="60" height="60" class="rounded"
                                 src="{{ asset('storage/post/'.$agent->image) }}">
                        </td>

                        <td>{{ $agent->title }}</td>

                        <td>
                            @foreach($agent->categories as $cat)
                                <span class="badge bg-primary">{{ $cat->name }}</span>
                            @endforeach
                        </td>

                        <td>
                            <button class="btn btn-danger btn-sm"
                                    wire:click="$emit('deletePost', {{ $agent->id }})">
                                <i class="mdi mdi-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $items->links() }}
            </div>

        </div>
    </div>
        </div></div>
</div>



