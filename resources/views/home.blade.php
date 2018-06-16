@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (isset($response))
                        @foreach ($response['releases'] as $key => $rel)
                            {{--<pre>{{ var_dump($rel['basic_information']) }}</pre>--}}
                            <img src="{{$rel['basic_information']['cover_image']}}" width="250" height="250" alt="">
                            <p>{{$rel['basic_information']['artists'][0]['name']}} - {{$rel['basic_information']['title']}}</p>
                            <hr>
                        @endforeach
                    @else
                        You're logged in
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
