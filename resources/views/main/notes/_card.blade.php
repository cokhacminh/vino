@php
    $reminder = $note->activeReminder;
@endphp

<div class="note-card"
     style="background: {{ $note->mau_sac }};"
     data-id="{{ $note->id }}"
     data-title="{{ $note->tieu_de }}"
     data-content="{{ $note->noi_dung ?? '' }}"
     data-color="{{ $note->mau_sac }}"
     data-pin="{{ $note->ghim ? '1' : '0' }}"
     data-hasreminder="{{ $reminder ? '1' : '0' }}"
     data-remindertime="{{ $reminder ? $reminder->thoi_gian : '' }}"
     data-reminderrepeat="{{ $reminder ? $reminder->lap_lai : '' }}"
     onclick="editNote({{ $note->id }}, event)">

    <button class="note-card-pin {{ $note->ghim ? 'pinned' : '' }}" onclick="togglePin({{ $note->id }}, event)" title="{{ $note->ghim ? 'Bỏ ghim' : 'Ghim' }}">
        {{ $note->ghim ? '📌' : '📍' }}
    </button>

    <div class="note-card-title">{{ $note->tieu_de }}</div>

    @if($note->noi_dung)
        <div class="note-card-content">{{ $note->noi_dung }}</div>
    @endif

    <div class="note-card-footer">
        <span>{{ $note->updated_at->format('d/m H:i') }}</span>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($reminder)
                <span class="reminder-tag">⏰ {{ $reminder->thoi_gian->format('d/m H:i') }}</span>
            @endif
            <button onclick="deleteNote({{ $note->id }}, event)" style="background:none;border:none;cursor:pointer;font-size:0.8rem;opacity:0.4;padding:2px;" title="Xóa">🗑</button>
        </div>
    </div>
</div>
