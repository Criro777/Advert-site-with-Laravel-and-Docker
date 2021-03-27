@extends('layouts.app')

@section('content')
    @include('cabinet.profile._nav')

    <form method="POST" action="{{ route('cabinet.profile.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="col-form-label">First Name</label>
            <input id="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $user->name) }}" required>
            @if ($errors->has('name'))
                <span class="invalid-feedback"><strong>{{ $errors->first('name') }}</strong></span>
            @endif
        </div>

        <div class="form-group">
            <label for="surname" class="col-form-label">Last Name</label>
            <input id="surname" type="text" class="form-control{{ $errors->has('surname') ? ' is-invalid' : '' }}" name="surname" value="{{ old('surname', $user->surname) }}" required>
            @if ($errors->has('surname'))
                <span class="invalid-feedback"><strong>{{ $errors->first('surname') }}</strong></span>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
@endsection
