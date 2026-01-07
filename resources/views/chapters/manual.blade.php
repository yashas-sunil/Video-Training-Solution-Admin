<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $chapter->name }} - Manual Content</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
            color: #222;
        }
        .page {
            max-width: 1024px;
            margin: 30px auto;
            padding: 0 16px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #700002;
            background: #700002;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .btn.secondary {
            background: #fff;
            color: #700002;
        }
        .type-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        .type-tab {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #700002;
            background: #fff;
            color: #700002;
            font-size: 13px;
            cursor: pointer;
        }
        .type-tab.active {
            background: #700002;
            color: #fff;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }
        .card {
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }
        .card h3 {
            margin: 0 0 6px 0;
            font-size: 16px;
        }
        .file-name {
            font-size: 14px;
            color: #444;
            margin-bottom: 8px;
            word-break: break-word;
        }
        .small {
            font-size: 12px;
            color: #777;
        }
        .type-section {
            display: none;
        }
        .type-section.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div>
            <h1>{{ $chapter->name }}</h1>
            <div class="small">Manual uploads for this chapter</div>
        </div>
        <div>
            <button class="btn secondary" onclick="window.history.back()">← Back</button>
        </div>
    </div>

    {{-- Type selection (7 content types) --}}
    <div class="type-tabs">
        @foreach ($types as $idx => $typeLabel)
            @php
                $slug = \Illuminate\Support\Str::slug($typeLabel, '_');
            @endphp
            <button
                type="button"
                class="type-tab{{ $idx === 0 ? ' active' : '' }}"
                data-type="{{ $slug }}"
            >
                {{ $typeLabel }}
            </button>
        @endforeach
    </div>

    {{-- Files inside selected type --}}
    @foreach ($groupedContents as $label => $items)
        @php
            $slug = \Illuminate\Support\Str::slug($label, '_');
        @endphp
        <div class="type-section{{ $loop->first ? ' active' : '' }}" data-type="{{ $slug }}">
            <div class="grid">
                @foreach ($items as $content)
                    <div class="card">
                        <h3>{{ $content['original_name'] }}</h3>
                        <a class="btn" href="{{ $content['url'] }}" target="_blank" rel="noopener">
                            View
                        </a>
                        @if ($content['size'])
                            <div class="small" style="margin-top:6px;">
                                Size: {{ round($content['size'] / 1024, 1) }} KB
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<script>
    const typeTabs = document.querySelectorAll('.type-tab');
    const sections = document.querySelectorAll('.type-section');

    typeTabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const type = tab.getAttribute('data-type');

            typeTabs.forEach((t) => t.classList.remove('active'));
            tab.classList.add('active');

            sections.forEach((sec) => {
                if (sec.getAttribute('data-type') === type) {
                    sec.classList.add('active');
                } else {
                    sec.classList.remove('active');
                }
            });
        });
    });
</script>
</body>
</html>

