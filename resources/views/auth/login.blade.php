@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold text-blue-600 mb-6">Connexion</h1>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-gray-700">Email</label>
            <input id="email" type="email" name="email" required autofocus
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>
        <div>
            <label for="password" class="block text-gray-700">Mot de passe</label>
            <input id="password" type="password" name="password" required
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200">
        </div>
        <button type="submit"
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Se connecter</button>
    </form>
</div>
@endsection
