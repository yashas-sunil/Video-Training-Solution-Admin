<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Quiz Attempts</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f7f7f7;
    }

    /* Logo bar */
    .top-logo-bar {
      text-align: left;
      padding: 10px 20px;
      background: white;
      border-bottom: 1px solid #ddd;
    }

    .top-logo-bar img {
      height: 40px;
    }

    /* Navbar */
    .navbar {
      background-color: #007bff;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }

    .navbar .logo img {
      height: 55px;
      width: auto;
    }

    .navbar .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    /* Page content */
    .page-content {
      padding: 20px;
    }

    /* Attempts grid */
    .attempts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    .attempt-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      background: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      width: auto;
      height: 130px;
      box-sizing: border-box;
      overflow: hidden;
    }

    .attempt-card p {
      margin: 5px 0;
      font-size: 14px;
    }

    .attempt-card a {
      display: inline-block;
      margin-top: 8px;
      padding: 4px 8px;
      background: #BFDEFF;
      color: #007BFF;
      border-radius: 4px;
      text-decoration: none;
      font-size: 12px;
    }

    /*.attempt-card a:hover {
      background: #0056b3;
    }*/

    /* Toolbar */
    .toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .quiz-title {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      margin: 0;
      font-size: 25px;
      font-weight: bold;
    }

    .toolbar {
      position: relative;
      /* zaroori hai absolute centering ke liye */
    }

    .toolbar-actions {
      display: flex;
      gap: 10px;
    }

    /* Filter button (just image, no style) */
    .btn-filter {
      background: none;
      border: none;
      padding: 0;
      cursor: pointer;
    }

    .btn-filter img {
      width: 20px;
      height: 20px;
      vertical-align: middle;
    }

    /* Back button styled */
    .btn-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 6px 14px;
      border: none;
      font-size: 14px;
      font-weight: 500;
      border-radius: 6px;
      background: #007bff;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    /*.btn-link:hover {
    background: #007bff;
    color: #fff;
  })*/

    /* Sidebar filter */
    .filter-sidebar {
      position: fixed;
      top: 0;
      right: -400px;
      width: 300px;
      height: 100%;
      background: #fff;
      border-left: 1px solid #ddd;
      box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
      transition: right 0.3s ease;
      padding: 20px;
      overflow-y: auto;
      z-index: 1000;
    }

    .filter-sidebar.open {
      right: 0;
    }

    .filter-sidebar h3 {
      margin-top: 0;
    }

    .filter-section {
      margin-bottom: 20px;
    }

    .filter-section label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .filter-section select,
    .filter-section input {
      width: 100%;
      padding: 6px;
      margin-bottom: 10px;
    }

    .close-btn {
      background: none;
      border: none;
      font-size: 20px;
      float: right;
      cursor: pointer;
    }

    .apply-btn {
      display: block;
      width: 100%;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .apply-btn:hover {
      background: #0056b3;
    }

    .clear-btn {
      display: block;
      margin-top: 10px;
      width: 100%;
      padding: 8px;
      background: none;
      border: none;
      color: #007bff;
      cursor: pointer;
    }

    /* Score slider overlay */
    .score-range-container {
      position: relative;
      height: 40px;
    }

    .score-range-container input[type=range] {
      position: absolute;
      width: 100%;
      pointer-events: none;
      background: none;
    }

    .score-range-container input[type=range]::-webkit-slider-thumb {
      pointer-events: all;
      position: relative;
      z-index: 2;
    }

    .score-range-container input[type=range]::-moz-range-thumb {
      pointer-events: all;
      position: relative;
      z-index: 2;
    }

    /* Overlay background */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.45);
      /* 45% opacity black */
      display: none;
      z-index: 900;
      /* below sidebar */
    }

    .overlay.show {
      display: block;
    }

    @media (max-width: 992px) {
      .attempts-grid {
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      }
    }
  </style>
</head>

<body>

  <!-- Logo -->
  <div class="navbar">
    <!-- Left: Company Logo -->
    <div class="logo">
      {{-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo"> --}}
      <img src="{{ asset('images/logo2.png') }}" alt="Company Logo">

    </div>

    <!-- Right: User Info -->
    <div class="user-info">
      <div>
        <div style="font-size: 0.85rem;">Welcome back !</div>
        <div style="font-size: 24px;font-weight: bold;">{{ auth()->user()->name }}</div>
      </div>
      <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        style="display: flex; gap: 5px; color:white; text-decoration: none; margin-left: 20px;">Logout<img
          src="{{ asset('images/logout.png') }}" alt="Logout"></a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </div>
  </div>

  <!-- Page Content -->
  <div class="page-content">

    <div class="toolbar">
      <h2>Quiz Attempts</h2>
      <div class="toolbar-actions">

        <!-- Filter Button -->
        <button class="btn-filter" onclick="openFilter()">
          <img src="{{ asset('images/filter.png') }}" alt="Filter" />
        </button>

        <!-- Back Button -->
        <button class="btn-link" onclick="window.history.back()">
          ⬅ Back
        </button>

      </div>
      <h2 class="quiz-title">{{ $quizName }}</h2>
    </div>


    <!-- Attempts -->
    <div class="attempts-grid" id="attemptsGrid">
      @forelse($attempts as $attempt)
      <div class="attempt-card" data-score="{{ $attempt['score_percent'] }}"
        data-chapter="{{ $attempt['chapter_name'] }}" data-attempt="{{ $attempt['attempt_number'] }}"
        data-date="{{ \Carbon\Carbon::parse($attempt['attempt_time'])->format('Y-m-d') }}"
        data-time="{{ \Carbon\Carbon::parse($attempt['attempt_time'])->format('H:i') }}">
        <p><strong>Chapter:</strong> {{ $attempt['chapter_name'] }}</p>
        <p>
          <strong>Attempt:</strong> {{ $attempt['attempt_number'] }}
          <span style="font-size:12px; color:#777;">({{ $attempt['attempt_time'] }})</span>
        </p>
        <p><strong>Score:</strong> {{ $attempt['score_percent'] }}%</p>

        <a href="{{ url('/course-attempts/'.$quizName.'/view/'.$attempt['attempt_number']) }}">
          View Questions
        </a>
      </div>
      @empty
      <p>No attempts found.</p>
      @endforelse
    </div>
  </div>

  <!-- Overlay -->
  <div id="overlay" class="overlay" onclick="closeFilter()"></div>

  <!-- Sidebar Filter -->
  <div id="filterSidebar" class="filter-sidebar">
    <button class="close-btn" onclick="closeFilter()">✖</button>
    <h3>Filter</h3>

    <!-- Chapter Dropdown -->
    <div class="filter-section">
      <label for="chapter">Filter by Chapter:</label>
      <select id="chapter">
        <option value="">All</option>
        @foreach($attempts->pluck('chapter_name')->unique() as $chapter)
        <option value="{{ $chapter }}">{{ $chapter }}</option>
        @endforeach
      </select>
    </div>

    <!-- Attempt Dropdown -->
    <div class="filter-section">
      <label for="attemptNo">Filter by Attempt:</label>
      <select id="attemptNo">
        <option value="">All</option>
        @foreach($attempts->pluck('attempt_number')->unique() as $atNo)
        <option value="{{ $atNo }}">Attempt {{ $atNo }}</option>
        @endforeach
      </select>
    </div>

    <!-- Date -->
    <div class="filter-section">
      <label for="date">Filter by Date:</label>
      <input type="date" id="date">
    </div>

    <!-- Score Double Range -->
    <div class="filter-section">
      <label>Filter by Score Range:
        <span id="minScoreVal">0</span>% - <span id="maxScoreVal">100</span>%
      </label>
      <div class="score-range-container">
        <input type="range" id="minScore" min="0" max="100" value="0" step="1" oninput="updateScoreRange()">
        <input type="range" id="maxScore" min="0" max="100" value="100" step="1" oninput="updateScoreRange()">
      </div>
    </div>

    <button class="apply-btn" onclick="applyFilters()">Apply</button>
    <button class="clear-btn" onclick="clearFilters()">Clear</button>
  </div>

  <script>
    function openFilter() {
      document.getElementById("filterSidebar").classList.add("open");
      document.getElementById("overlay").classList.add("show");
    }
    function closeFilter() {
      document.getElementById("filterSidebar").classList.remove("open");
      document.getElementById("overlay").classList.remove("show");
    }

    function updateScoreRange() {
      let minScore = parseInt(document.getElementById("minScore").value);
      let maxScore = parseInt(document.getElementById("maxScore").value);

      if (minScore > maxScore) {
        [minScore, maxScore] = [maxScore, minScore];
        document.getElementById("minScore").value = minScore;
        document.getElementById("maxScore").value = maxScore;
      }

      document.getElementById("minScoreVal").innerText = minScore;
      document.getElementById("maxScoreVal").innerText = maxScore;
    }

    function applyFilters() {
      const chapter = document.getElementById("chapter").value;
      const attempt = document.getElementById("attemptNo").value;
      const date = document.getElementById("date").value;
      const minScore = parseInt(document.getElementById("minScore").value);
      const maxScore = parseInt(document.getElementById("maxScore").value);

      const cards = document.querySelectorAll(".attempt-card");
      cards.forEach(card => {
        let show = true;

        if (chapter && card.getAttribute("data-chapter") !== chapter) show = false;
        if (attempt && card.getAttribute("data-attempt") !== attempt) show = false;
        if (date && card.getAttribute("data-date") !== date) show = false;

        let score = parseInt(card.getAttribute("data-score"));
        if (score < minScore || score > maxScore) show = false;

        card.style.display = show ? "block" : "none";
      });

      closeFilter();
    }

    function clearFilters() {
      document.getElementById("chapter").value = "";
      document.getElementById("attemptNo").value = "";
      document.getElementById("date").value = "";
      document.getElementById("minScore").value = 0;
      document.getElementById("maxScore").value = 100;
      document.getElementById("minScoreVal").innerText = "0";
      document.getElementById("maxScoreVal").innerText = "100";

      document.querySelectorAll(".attempt-card").forEach(card => {
        card.style.display = "block";
      });
    }
  </script>

</body>

</html>