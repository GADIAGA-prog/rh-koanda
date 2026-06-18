@extends('layouts.app')
@section('titre', 'Rôles & permissions')
@section('rubrique', 'Administration')

@section('contenu')
@php
    // Regroupe les permissions par module (préfixe avant le point).
    $groupes = $permissions->groupBy(fn ($p) => explode('.', $p->name)[0]);
@endphp

@unless ($editable)
    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Consultation seule — seul le rôle <strong>super-admin</strong> peut modifier la matrice.
    </div>
@endunless

<div class="space-y-5">
    @foreach ($roles as $role)
        @php $permsRole = $role->permissions->pluck('name')->all(); @endphp
        <form method="POST" action="{{ route('admin.roles.update', $role) }}"
              class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf @method('PUT')

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">{{ $role->name }}</h2>
                    <p class="text-xs text-slate-400">{{ count($permsRole) }} permission(s)</p>
                </div>
                @if ($editable)
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Enregistrer</button>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($groupes as $module => $perms)
                    <div class="rounded-lg border border-slate-100 p-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $module }}</p>
                        <div class="space-y-1.5">
                            @foreach ($perms as $perm)
                                <label class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                           @checked(in_array($perm->name, $permsRole))
                                           @disabled(! $editable)
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50">
                                    {{ $perm->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    @endforeach
</div>
@endsection
