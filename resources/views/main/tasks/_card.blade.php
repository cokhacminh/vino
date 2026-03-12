@php
    $deadlineClass = '';
    if ($task->ngay_ket_thuc) {
        $dl = \Carbon\Carbon::parse($task->ngay_ket_thuc);
        $now = \Carbon\Carbon::now();
        if ($dl->isPast() && $task->trang_thai !== 'hoan_thanh') $deadlineClass = 'overdue';
        elseif ($dl->diffInDays($now) <= 2) $deadlineClass = 'soon';
    }
    $userIds = ',' . $task->taskUsers->pluck('user_id')->implode(',') . ',';
    $labelIds = ',' . $task->labels->pluck('id')->implode(',') . ',';
    $subtaskStats = $task->subtask_stats;
@endphp

<div class="task-card"
     data-id="{{ $task->id }}"
     data-title="{{ $task->tieu_de }}"
     data-priority="{{ $task->do_uu_tien }}"
     data-users="{{ $userIds }}"
     data-labels="{{ $labelIds }}"
     draggable="true"
     ondragstart="handleDragStart(event)"
     ondragend="handleDragEnd(event)"
     onclick="openDetailModal({{ $task->id }})">

    @if($task->labels->count())
    <div class="task-card-labels">
        @foreach($task->labels as $label)
            <span class="task-label-badge" style="background:{{ $label->mau_sac }}">{{ $label->ten }}</span>
        @endforeach
    </div>
    @endif

    <div class="task-card-title">{{ $task->tieu_de }}</div>

    <div class="task-card-meta">
        <div class="task-card-meta-left">
            <span class="card-priority {{ $task->do_uu_tien }}" title="{{ $task->do_uu_tien }}"></span>
            @if($task->ngay_ket_thuc)
                <span class="card-deadline {{ $deadlineClass }}">
                    📅 {{ \Carbon\Carbon::parse($task->ngay_ket_thuc)->format('d/m') }}
                </span>
            @endif
        </div>
        <div class="task-card-meta-right">
            <div class="card-stats">
                @if($subtaskStats['total'] > 0)
                    <span>☑ {{ $subtaskStats['done'] }}/{{ $subtaskStats['total'] }}</span>
                @endif
                @if($task->comments_count ?? $task->comments->count())
                    <span>💬 {{ $task->comments->count() }}</span>
                @endif
            </div>
            <div class="card-avatars">
                @foreach($task->taskUsers->take(3) as $tu)
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($tu->user->name ?? '?') }}&size=24&background=6d28d9&color=fff&bold=true"
                         class="card-avatar" title="{{ $tu->user->name ?? '' }}">
                @endforeach
                @if($task->taskUsers->count() > 3)
                    <span style="font-size:0.65rem;color:#64748b;margin-left:2px;">+{{ $task->taskUsers->count() - 3 }}</span>
                @endif
            </div>
        </div>
    </div>

    @if($task->tien_do_trung_binh > 0)
    <div class="card-progress">
        <div class="card-progress-fill" style="width:{{ $task->tien_do_trung_binh }}%"></div>
    </div>
    @endif
</div>
