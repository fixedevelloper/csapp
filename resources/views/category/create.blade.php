@extends('layout')

@section('content')
    <div class="content-page">
        <div class="content">
        <!-- Start Content-->
            <div class="container-fluid">
                <div class="row mt-3">

                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" action="{{route('category.create')}}">
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label for="name" class="form-label">Libelle</label>
                                            <input class="form-control" value="{{$product->libelle}}" name="libelle" type="text" id="name" required="" placeholder="Enter your name">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="name" class="form-label">Prix</label>
                                            <input class="form-control" value="{{$product->price}}" min="0" name="price" type="text" id="name" required="" placeholder="Enter your name">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="name" class="form-label">Prix de vente</label>
                                            <input value="{{$product->price_sell}}" class="form-control" min="0" name="price_sell" type="text" id="name" required="" placeholder="Enter your name">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="name" class="form-label">Quantite</label>
                                            <input value="{{$product->quantite}}" class="form-control" min="0" name="quantite" type="number" id="name" required="" placeholder="Enter your name">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="name" class="form-label">Categorie</label>
                                            <select name="product_type_id" id="inputState" class="form-select">
                                                <option>Choisir la categorie</option>
                                                @foreach($categories as $item)
                                                    @if($product['product_type_id']==$item->id)
                                                        <option selected value="{{$item->id}}">{{$item->libelle}}</option>
                                                    @else
                                                        <option {{ $product['product_type_id'] == $item->id ? 'selected' : '' }} value="{{$item->id}}">{{$item->libelle}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="name" class="form-label">Fournisseur</label>
                                            <select name="fournisseur_id" id="inputState" class="form-select">
                                                <option value="">Choisir le fournisseur</option>

                                                @foreach($fournisseurs as $item)
                                                    @if($product['fournisseur_id']==$item->id)
                                                        <option selected value="{{$item->id}}">{{$item->name}}</option>
                                                    @else
                                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select> </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="name" class="form-label">Image</label>
                                            <div class="text-center mt-1 mb-1">
                                                <img style="height: 100px;border: 1px solid; border-radius: 10px;" id="viewer"
                                                     src="{{asset('storage/product').'/'.$product['image']}}" alt="agent image"/>
                                            </div>
                                            <input class="form-control"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" name="image" type="file" id="name">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea  id="description" rows="4" class="form-control" name="description" placeholder="produit description">
                                            {{$product->description}}
                                </textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-center">
                                        <a class="btn btn-warning" type="button" href="{{route('product.index')}}"><i class="mdi mdi-arrow-left"></i> annuler </a>
                                        <button class="btn btn-success" type="submit"> Modifier </button>
                                    </div>
                                </form>
                            </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

