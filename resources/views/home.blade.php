@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body text-center">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (isset($user))
                        <pre>Database</pre>
                        <h1>{{ $user->name  }}</h1>
                        <img class="img-rounded" src="{{ $user['avatar'] }}" alt="" width="250">
                        <p>{{ $user->location }}</p>
                    @endif
                    <hr>
                    @if (isset($response))
                        <pre>API Client</pre>
                        <h1>{{ $response['name'] }}</h1>
                        <img class="img-rounded" src="{{ $response['avatar_url'] }}" alt="" width="250">
                        <p>{{ $response['location'] }}</p>
                    @else
                        You're logged in
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
