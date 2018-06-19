@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{!! $name !!}</div>

                <div class="panel-body text-center">
                    <form class="form-inline" action="{{ route('find') }}" method="POST">
                        {{ csrf_field() }}
                        <input class="form-control" type="text" name="search" id="search">
                        <input class="btn btn-default" type="submit" value="Go">
                    </form>
                    <hr>
                    @if(isset($response))
                        @foreach($response['results'] as $res)
                            <img src="{{ $res['cover_image'] }}" width="100%" alt="" />
                            <h1>{{ $res['title'] }}</h1>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
