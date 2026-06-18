@extends('layouts.app')
@section('titre', 'Rôles & permissions')
@section('rubrique', 'Administration')

@section('contenu')
@php
    // Regroupe les permissions par module (préfixe avant le point).
    $groupes = $permissions->groupBy(fn ($p) => explode('.', $p->name)[0]);
@endphp

@unless ($editable)
    <div class="mb-5 flex items-start gap-3 rounded-xl border border-mist bg-koanda-light px-4 py-3 text-sm text-slatetext">
        <span class="text-base leading-none">🔒</span>
        <p>Consultation seule — seul le rôle <strong class="text-forest">Super Admin</strong> peut modifier la matrice des permissions.</p>
    </div>
@endunless

<div class="space-y-5">
    @foreach ($roles as $role)
        @php
            $permsRole = $role->permissions->pluck('name')->all();
            [$labelRole, $couleur] = \App\Models\User::ROLES_META[$role->name] ?? [$role->name, 'slate'];
        @endphp
        <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
            @csrf @method('PUT')

            {{-- En-tête du rôle --}}
            <div class="flex items-center justify-between border-b border-mist bg-mineral px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 font-display text-sm font-bold text-forest ring-1 ring-inset ring-mist">
                        <span class="h-2 w-2 rounded-full bg-{{ $couleur }}-500"></span>{{ $labelRole }}
                    </span>
                    <span class="rounded-md bg-white px-2 py-0.5 text-xs font-medium text-slate-500 ring-1 ring-inset ring-mist">{{ count($permsRole) }} / {{ $permissions->count() }} permissions</span>
                </div>
                @if ($editable)
                    <button class="rounded-lg bg-koanda px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-koanda-dark">Enregistrer</button>
                @endif
            </div>

            {{-- Permissions par module --}}
            <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($groupes as $module => $perms)
                    <div class="rounded-xl border border-mist bg-mineral/50 p-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $module }}</p>
                        <div class="space-y-1">
                            @foreach ($perms as $perm)
                                @php $action = explode('.', $perm->name)[1] ?? $perm->name; @endphp
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm transition hover:bg-white has-[:checked]:bg-white has-[:checked]:font-medium has-[:checked]:text-koanda-dark {{ $editable ? '' : 'cursor-default' }}">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                           @checked(in_array($perm->name, $permsRole))
                                           @disabled(! $editable)
                                           class="rounded border-mist text-koanda focus:ring-koanda disabled:opacity-40">
                                    <span class="text-slatetext">{{ $action }}</span>
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
