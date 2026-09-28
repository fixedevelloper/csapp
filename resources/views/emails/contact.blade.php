{{-- Email texte brut : pas d'échappement HTML, sinon les apostrophes s'affichent en &#039; --}}
Nouveau message depuis le formulaire de contact

Nom : {!! $contact->name !!}
Email : {!! $contact->email !!}
Téléphone : {!! $contact->phone !!}
Sujet : {!! $contact->subject !!}

{!! $contact->message !!}
