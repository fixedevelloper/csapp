@extends('layout')

@section('content')
    <div class="content-page">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box page-title-box-alt">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Accueil</a></li>
                                    <li class="breadcrumb-item active">Mon compte</li>
                                </ol>
                            </div>
                            <h4 class="page-title">Mon compte</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="bg-picture">
                                    <div class="d-flex align-items-top">
                                        <img src="{{ $user->avatar_url }}"
                                             class="flex-shrink-0 rounded-circle avatar-xl img-thumbnail float-start me-3"
                                             alt="profile-image">
                                    </div>
                                    <form method="POST" action="{{route('changeimage')}}" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        <div class="mt-3 d-grid text-center">
                                            <input required name="photo" type="file" placeholder="Changer image">
                                        </div>
                                        <div class="mt-3 d-grid text-center">
                                            <button class="btn btn-success" type="submit"> Changer image </button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills navtab-bg nav-justified">
                                    <li class="nav-item">
                                        <a href="#profile1" data-bs-toggle="tab" aria-expanded="true"
                                           class="nav-link active">
                                            Profile
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#messages1" data-bs-toggle="tab" aria-expanded="false"
                                           class="nav-link">
                                            Changer mot de passe
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane show active" id="profile1">
                                        <form method="POST" action="{{route('profil')}}">
                                            {{csrf_field()}}
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label">Nom</label>
                                                    <input value="{{ old('name', $user->name) }}" class="form-control" name="name"
                                                           type="text" id="name" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="phone" class="form-label">Téléphone</label>
                                                    <input value="{{ old('phone', $user->phone) }}" class="form-control" name="phone"
                                                           type="tel" id="phone">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="emailaddress" class="form-label">Adresse email</label>
                                                    <input value="{{ old('email', $user->email) }}" class="form-control" name="email"
                                                           type="email" id="emailaddress" required>
                                                </div>
                                            </div>
                                            <div class="mb-3 d-grid text-center">
                                                <button class="btn btn-success" type="submit"> Modifier</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="messages1">
                                        <form method="POST" action="{{route('changepassword')}}">
                                            {{csrf_field()}}
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="oldpassword" class="form-label">Ancien mot de
                                                        passe</label>
                                                    <input class="form-control" name="oldpassword" type="password"
                                                           id="oldpassword" required="" placeholder="">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Nouveau mot de
                                                        passe</label>
                                                    <input class="form-control" name="password" type="password"
                                                           required minlength="8" id="password" autocomplete="new-password">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3 offset-md-6">
                                                    <label for="password_confirmation" class="form-label">Confirmer le
                                                        nouveau mot de passe</label>
                                                    <input class="form-control" name="password_confirmation" type="password"
                                                           required minlength="8" id="password_confirmation" autocomplete="new-password">
                                                </div>
                                            </div>
                                            <div class="mb-3 d-grid text-center">
                                                <button class="btn btn-outline-dark" type="submit"> Changer le mot de
                                                    passe
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection

