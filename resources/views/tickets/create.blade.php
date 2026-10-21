@extends('layouts.app')
@section('title', 'Ask for help')
@section('content')
<h1>Ask for help</h1>
<form method="POST" action="{{ route('tickets.store') }}">
@csrf
<p>
<label for="name">Your name</label><br>
<input id="name" name="name" value="{{ old('name') }}">
@error('name') <br><small class="error">{{ $message }}</small> @enderror
</p>
<p>
<label for="topic">Topic</label><br>
<select id="topic" name="topic">
<option value="">— choose —</option>
@foreach ($topics as $topic)
<option value="{{ $topic }}" @selected(old('topic') === $topic)>{{

$topic }}</option>
@endforeach
</select>
@error('topic') <br><small class="error">{{ $message }}</small> @enderror
</p>
<p>
<label for="description">What is the problem?</label><br>
<textarea id="description" name="description" rows="3">{{

old('description') }}</textarea>

@error('description') <br><small class="error">{{ $message }}</small>

@enderror
</p>
<button type="submit">Join the queue</button>
</form>
@endsection