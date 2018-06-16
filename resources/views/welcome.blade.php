@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{!! $name !!}</div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-danger">
                            {{ session('message') }}
                        </div>
                        <a class="btn btn-default" href="/discogs">Login with Discogs</a>
                    @else
                        <form class="form-inline" action="/" method="POST">
                            {{ csrf_field() }}
                            <input class="form-control col" type="text" name="search" id="search">
                            <input class="btn btn-primary" type="submit" value="Go">
                        </form>
                    @endif

                    @if(isset($response))
                        @foreach($response['results'] as $res)
                            <li style="list-style: none;">{{ $res['title'] }}</li>
                            <img src="{{ $res['cover_image'] }}" alt="" />
                        @endforeach
                    @endif

                    {{--<div class="links">
                        <a href="https://laravel.com/docs">Documentation</a>
                        <a href="https://laracasts.com">Laracasts</a>
                        <a href="https://laravel-news.com">News</a>
                        <a href="https://forge.laravel.com">Forge</a>
                        <a href="https://github.com/laravel/laravel">GitHub</a>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
