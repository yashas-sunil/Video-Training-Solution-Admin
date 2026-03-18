@extends('adminlte::page')

@section('title', 'Preview: ' . $test->test_name)

@push('css')
<style>
    .preview-header {
        /* background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); */
        background: white;
        border-radius: 12px;
        padding: 28px 32px;
        margin-bottom: 30px;
        color: #0f3460;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    }
    .preview-header h2 {
        font-size: 1.7rem;
        font-weight: 700;
        margin-bottom: 6px;
        letter-spacing: 0.5px;
    }
    .preview-header .meta-badges span {
        display: inline-block;
        background: rgba(5, 5, 5, 0.12);
        border: 1px solid rgba(32, 29, 29, 0.2);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.82rem;
        margin-right: 8px;
        margin-top: 8px;
        color: #0f3460;
    }
    .preview-header .meta-badges span i {
        margin-right: 5px;
        color: #0f3460;
    }
    .question-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        margin-bottom: 24px;
        overflow: hidden;
        border: 1px solid #e8ecf0;
        transition: box-shadow 0.2s;
    }
    .question-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
    }
    .question-header {
        background: #f7f9fc;
        border-bottom: 1px solid #e8ecf0;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .question-num {
        background: #0f3460;
        color: #fff;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .question-text {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        line-height: 1.5;
    }
    .options-list {
        padding: 16px 20px 20px 20px;
        list-style: none;
        margin: 0;
    }
    .options-list li {
        display: flex;
        align-items: flex-start;
        padding: 10px 14px;
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1.5px solid #e8ecf0;
        font-size: 0.95rem;
        color: #333;
        background: #fafbfd;
        transition: background 0.15s;
    }
    .options-list li.correct-answer {
        background: #e8f8f0;
        border-color: #27ae60;
        color: #1a6b3c;
        font-weight: 600;
    }
    .option-label {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e8ecf0;
        color: #555;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
        margin-right: 12px;
        margin-top: 1px;
    }
    .correct-answer .option-label {
        background: #27ae60;
        color: #fff;
    }
    .correct-badge {
        margin-left: auto;
        background: #27ae60;
        color: #fff;
        border-radius: 12px;
        padding: 2px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .no-questions {
        text-align: center;
        padding: 60px 20px;
        color: #888;
    }
    .no-questions i {
        font-size: 3.5rem;
        color: #ccc;
        margin-bottom: 16px;
        display: block;
    }
    .stats-row {
        display: flex;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .stat-box {
        background: #fff;
        border-radius: 10px;
        padding: 16px 24px;
        border: 1px solid #e8ecf0;
        flex: 1;
        min-width: 140px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .stat-box .stat-val {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0f3460;
    }
    .stat-box .stat-label {
        font-size: 0.8rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }
    .easy-val   { color: #27ae60 !important; }
    .medium-val { color: #f39c12 !important; }
    .hard-val   { color: #e74c3c !important; }
    .back-btn {
        background: #0f3460;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: background 0.2s;
    }
    .back-btn:hover {
        background: #16213e;
        color: #fff;
        text-decoration: none;
    }
    /* Pagination */
    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
        margin-bottom: 20px;
        padding: 14px 20px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e8ecf0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .page-info {
        font-size: 0.88rem;
        color: #666;
    }
    .page-info strong { color: #0f3460; }
    .pagination { margin: 0; }
    .pagination .page-item.active .page-link {
        background-color: #0f3460;
        border-color: #0f3460;
        color: #fff;
    }
    .pagination .page-link {
        color: #0f3460;
        border-radius: 6px;
        margin: 0 2px;
        padding: 6px 12px;
        font-size: 0.88rem;
        transition: all 0.15s;
    }
    .pagination .page-link:hover {
        background: #e8f0fe;
        color: #0f3460;
    }
</style>
@endpush

@section('content_header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="m-0 text-dark">Test Preview</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('admin-test') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Tests
            </a>
        </div>
    </div>
@stop

@section('content')

    {{-- Header Card --}}
    <div class="preview-header">
        <h2><i class="fas fa-clipboard-list mr-2" style="color: #0f3460;;"></i>{{ $test->test_name }}</h2>
        <div class="meta-badges">
            <span><i class="fas fa-book" style="color: #0f3460;;"></i> Course: {{ $course ? $course->title : 'N/A' }}</span>
            <span><i class="fas fa-question-circle"></i> Total Questions: {{ $test->total_ques_count }}</span>
            <span><i class="fas fa-circle" style="color:#27ae60;"></i> Easy: {{ $test->easy_count }}</span>
            <span><i class="fas fa-circle" style="color:#f39c12;"></i> Medium: {{ $test->medium_count }}</span>
            <span><i class="fas fa-circle" style="color:#e74c3c;"></i> Hard: {{ $test->hard_count }}</span>
            <!-- @if($test->status == 1)
                <span><i class="fas fa-check-circle" style="color:#a0ffb0;"></i> Active</span>
            @else
                <span><i class="fas fa-times-circle" style="color:#ffaaaa;"></i> Inactive</span>
            @endif -->
        </div>
    </div>

    <!-- {{-- Stats Row --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-val">{{ $questions->count() }}</div>
            <div class="stat-label">Questions Loaded</div>
        </div>
        <div class="stat-box">
            <div class="stat-val easy-val">{{ $test->easy_count }}</div>
            <div class="stat-label">Easy</div>
        </div>
        <div class="stat-box">
            <div class="stat-val medium-val">{{ $test->medium_count }}</div>
            <div class="stat-label">Medium</div>
        </div>
        <div class="stat-box">
            <div class="stat-val hard-val">{{ $test->hard_count }}</div>
            <div class="stat-label">Hard</div>
        </div>
    </div> -->

    {{-- Questions List --}}
    @if($questions->isEmpty())
        <div class="no-questions">
            <!-- <i class="fas fa-inbox"></i> -->
            <p style="font-size:1.1rem; font-weight:600; color:#555;">No questions found for this test.</p>
            <p style="color:#aaa;">Questions may not have been assigned yet.</p>
        </div>
    @else
        @php
            $labels  = ['A','B','C','D','E','F','G','H'];
            $offset  = ($questions->currentPage() - 1) * $questions->perPage();
        @endphp

        @foreach($questions->items() as $index => $question)
        <div class="question-card">
            <div class="question-header">
                <div class="question-num">{{ $offset + $index + 1 }}</div>
                <div class="question-text">{!! $question->question !!}</div>
            </div>
            <ul class="options-list">
                @if($question->options->isEmpty())
                    <li style="color:#aaa; border:none; background:transparent;">
                        <i class="fas fa-exclamation-circle mr-2"></i> No options found for this question.
                    </li>
                @else
                    @foreach($question->options as $optIndex => $option)
                    <li class="{{ $option->correctans == 1 ? 'correct-answer' : '' }}">
                        <span class="option-label">{{ $labels[$optIndex] ?? ($optIndex+1) }}</span>
                        <span>{!! $option->answer !!}</span>
                        @if($option->correctans == 1)
                            <span class="correct-badge"><i class="fas fa-check mr-1"></i>Correct</span>
                        @endif
                    </li>
                    @endforeach
                @endif
            </ul>
        </div>
        @endforeach

        {{-- Pagination Bar --}}
        @if($questions->lastPage() > 1)
        <div class="pagination-wrapper">
            <div class="page-info">
                Showing <strong>{{ $offset + 1 }}</strong> – <strong>{{ min($offset + $questions->perPage(), $questions->total()) }}</strong>
                of <strong>{{ $questions->total() }}</strong> questions
                &nbsp;|&nbsp; Page <strong>{{ $questions->currentPage() }}</strong> of <strong>{{ $questions->lastPage() }}</strong>
            </div>
            <div>
                {{ $questions->links() }}
            </div>
        </div>
        @endif
    @endif

@stop

@push('third_party_scripts')
@endpush
