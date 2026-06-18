@props(['role' => null])
@php
    // Chip sobre (charte : couleurs vives réservées aux infos clés). Le rôle est
    // signalé par une petite pastille colorée, le chip restant neutre.
    [$label, $couleur] = \App\Models\User::ROLES_META[$role] ?? [$role ?: '—', 'slate'];
@endphp
@if ($role)
    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-0.5 text-xs font-medium text-slatetext ring-1 ring-inset ring-mist">
        <span class="h-1.5 w-1.5 rounded-full bg-{{ $couleur }}-500"></span>{{ $label }}
    </span>
@else
    <span class="text-xs text-slate-400">—</span>
@endif
