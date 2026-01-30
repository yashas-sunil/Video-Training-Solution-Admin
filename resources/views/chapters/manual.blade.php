<!DOCTYPE html>
<html lang="en">


<head>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
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
        max-width: 100%;
    }

    .header {
        display: flex;
        justify-content: end;
        align-items: center;
        margin-bottom: 20px;
    }

    .header h1 {
        margin: 0;
        font-size: 22px;
    }

    .btn {
        height: fit-content;
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

    .btn:hover {
        background: #ffffff;
        color: #700002;
    }

    .btn.secondary {
        background: #fff;
        color: #700002;
    }

    .overview {
        margin-bottom: 40px;
    }

    .grid {
        display: flex;
        gap: 60px;
        flex-direction: column;
        padding-left: 60px;
        padding-right: 60px;
    }

    .card {
        display: flex;
        background: #fff;
        border-radius: 10px;
        padding: 14px;
        border: 1px solid #700002;
        justify-content: space-between;
        align-items: center;
    }

    /* overview layout */
    .overview-item {
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-bottom: 26px;
    }

    .overview-item .label {
        min-width: 180px;
        font-weight: bold;
        font-size: 16px;
        color: #700002;
    }

    .label {
        padding-left: 60px;
        padding-right: 60px;
    }

    .lessons-wrapper {
        display: flex;
        gap: 20px;
    }

    .lessons-list {
        width: 210px;
        padding: 10px;
        background: #700002;
        height: -webkit-fill-available;
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        position: fixed;
        height: -webkit-fill-available;
    }

    .lessons-list button {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 6px;
        padding: 8px;
        border-radius: 6px;
        background: #ffffff40;
        border: none;
        color: #ffffff;
        cursor: pointer;
        font-weight: 600;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .lessons-list button:hover {
        background: #ffffff;
        color: #700002;
    }

    .lesson-tab:hover .tab-icon {
        filter:
            brightness(0) saturate(100%) invert(9%) sepia(100%) saturate(9800%) hue-rotate(352deg) brightness(47%) contrast(112%);
    }

    .lesson-contents {
        flex: 1;
        min-width: 0;
    }

    /* 🔥 IMPORTANT: lesson kabhi hide nahi hoga */
    .lesson-section {
        display: none;
        margin-bottom: 60px;
        margin-left: 230px;
    }

    .continue-wrap {
        margin-top: 30px;
        text-align: center;
    }

    .course-layout {
        display: flex;
        height: 80vh;
    }

    .scroll-pane {
        overflow-y: auto;
        min-block-size: fit-content;
    }

    .lessons-list {
        flex-shrink: 0;
    }

    .chapter-hero {
        background-size: cover;
        background-position: center;
        padding: 50px 20px;
        position: relative;
    }

    .chapter-hero::after {
        content: "";
        position: absolute;
        inset: 0;
    }

    .chapter-hero h1 {
        position: relative;
        z-index: 2;
        color: white;
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .lesson-tab {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .lesson-tab input {
        cursor: pointer;
    }

    .lesson-tab.active {
        font-weight: bold;
    }

    .lesson-tab input[type="checkbox"] {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 10px;
        display: grid;
        place-content: center;
        cursor: pointer;
        background: #858282;
    }

    .lesson-tab input[type="checkbox"]:checked {
        background-color: #ff746777;
    }

    .lesson-tab input[type="checkbox"]:checked::before {
        content: "✓";
        color: white;
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
    }

    .progress-container {
        position: relative;
        width: 100%;
        height: 6px;
        background: #b5b5b5;
        border-radius: 6px;
        margin-top: 20px;
        box-shadow: 5px 4px 7px 2px #00000040 inset;
    }

    .progress-bar {
        height: 100%;
        width: 0%;
        background-color: #ffffff;
        border-radius: 6px;
        transition: width 0.3s ease;
    }

    .progress-text {
        position: absolute;
        right: 10px;
        top: -22px;
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    .tab-label {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tab-icon {
        width: 30px;
        height: auto;
        object-fit: contain;
    }

    .icon_chapter {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .card {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .inline-viewer {
        width: 100%;
    }

    .inline-viewer iframe,
    .inline-viewer img {
        width: 100%;
        height: 70vh;
        border: none;
        border-radius: 8px;
        object-fit: contain;
    }

.inline-viewer video {
    width: 100%;
    max-width: 900px;
    height: auto;
    max-height: 70vh;
    display: block;
    margin: 0 auto;
    border-radius: 8px;
    background: black;
}
.inline-viewer video::-webkit-media-controls-panel {
    background-image: none !important;
}

.file-name,
.viewer-title,
.video-name {
    display: none !important;
}

/* ✅ sidebar button pe click always work (checkbox click steal na kare) */
.lessons-list button input {
    pointer-events: none;
}
</style>


</head>

<body>

 <div class="page">
<div class="course-layout">

{{-- 🔹 LEFT SIDEBAR --}}
<div class="sidebar">
    <div class="chapter-hero" style="display:flex;background:#700002;gap:30px;flex-direction:column;">
        <h1>{{ $chapter->name }}</h1>

        <div class="progress-container">
            <div class="progress-bar"></div>
            <span class="progress-text">0%</span>
        </div>
    </div>

    <div class="lessons-list scroll-pane">
        <button class="lesson-tab active" data-index="overview">
            <span class="tab-label">
                <img src="{{ asset('images/overview-icon.png') }}" class="tab-icon">
                Overview
            </span>
            <input type="checkbox" checked disabled>
        </button>

        @foreach ($lessons as $i => $lesson)
        <button class="lesson-tab" data-index="{{ $i }}">
            <span class="tab-label">
                <img src="{{ asset('images/lesson-icon.png') }}" class="tab-icon">
                {{ $lesson['lesson_name'] }}
            </span>
            <input type="checkbox" data-check="{{ $i }}">
        </button>
        @endforeach
    </div>
</div>


{{-- 🔹 RIGHT CONTENT --}}
<div class="lesson-contents scroll-pane">

{{-- 🔹 OVERVIEW --}}
<div class="lesson-section" id="lesson-overview" data-index="overview">
<div class="overview">

@foreach ($overview as $label => $items)
<div class="overview-item">
<div class="grid">

@foreach ($items as $content)
@php
$ext = strtolower(pathinfo($content['url'], PATHINFO_EXTENSION));
@endphp

<div class="card">

<div class="media-title">{{ $content['original_name'] }}</div>

@if(in_array($ext,['mp4','webm','ogg']))
    {{-- 🔹 VIDEO --}}
    <div class="inline-viewer show">
        <video class="lesson-video" controls playsinline preload="none" controlsList="nodownload noremoteplayback">
            <source src="{{ $content['url'] }}" type="video/mp4">
        </video>
    </div>
@else
    {{-- 🔹 IMAGE / PDF --}}
    <button class="btn open-inline" data-url="{{ $content['url'] }}">Open</button>
    <div class="inline-viewer"></div>
@endif

</div>
@endforeach

</div>
</div>
@endforeach

<div class="continue-wrap">
    <button class="btn unlock-once" data-next="0">Start Lesson →</button>
</div>

</div>
</div>


{{-- 🔹 LESSONS --}}
@foreach ($lessons as $i => $lesson)
<div class="lesson-section" id="lesson-{{ $i }}" data-index="{{ $i }}">

<h2 style="background:#fff;padding:60px;color:#700002;font-size:40px;">
    {{ $lesson['lesson_name'] }}
</h2>

@foreach ($lesson['contents'] as $type => $items)
<h3 style="padding:0 60px;color:#700002;">{{ $type }}</h3>

<div class="grid">

@foreach ($items as $content)
@php
$ext = strtolower(pathinfo($content['url'], PATHINFO_EXTENSION));
@endphp

<div class="card">

<div class="media-title">{{ $content['original_name'] }}</div>

@if(in_array($ext,['mp4','webm','ogg']))
    {{-- 🔹 VIDEO --}}
    <div class="inline-viewer show">
        <video class="lesson-video" controls playsinline preload="none" controlsList="nodownload noremoteplayback">
            <source src="{{ $content['url'] }}" type="video/mp4">
        </video>
    </div>
@else
    {{-- 🔹 IMAGE / PDF --}}
    <button class="btn open-inline" data-url="{{ $content['url'] }}">Open</button>
    <div class="inline-viewer"></div>
@endif

</div>
@endforeach

</div>
@endforeach

@if(isset($lessons[$i+1]))
<div class="continue-wrap">
    <button class="btn unlock-once" data-next="{{ $i+1 }}">Continue →</button>
</div>
@endif

</div>
@endforeach

</div>
</div>
</div>

<script>
const tabs = document.querySelectorAll(".lesson-tab");
const checks = document.querySelectorAll("[data-check]");
const continueBtns = document.querySelectorAll(".unlock-once");

let unlockedIndex = -1;

function setActiveTab(targetIndex){
    tabs.forEach(t => t.classList.remove("active"));
    const btn = document.querySelector(`.lesson-tab[data-index="${targetIndex}"]`);
    if(btn) btn.classList.add("active");
}

function scrollToSection(targetIndex){
    let sectionId =
        targetIndex === "overview"
            ? "lesson-overview"
            : "lesson-" + targetIndex;

    const section = document.getElementById(sectionId);
    if(!section) return;

    const y =
        section.getBoundingClientRect().top +
        window.pageYOffset -
        20;

    window.scrollTo({
        top: y,
        behavior: "smooth"
    });

    setActiveTab(targetIndex);
}

function render(){
    const sections = document.querySelectorAll(".lesson-section");
    sections.forEach(sec=>{
        sec.style.display="none";
        const btn=sec.querySelector(".unlock-once");
        if(btn) btn.style.display="none";
    });

    const overview=document.getElementById("lesson-overview");
    overview.style.display="block";

    if(unlockedIndex===-1){
        const startBtn=overview.querySelector(".unlock-once");
        if(startBtn) startBtn.style.display="inline-flex";
    }

    for(let i=0;i<=unlockedIndex;i++){
        const el=document.getElementById("lesson-"+i);
        if(el) el.style.display="block";
    }

    if(unlockedIndex>=0){
        const last=document.getElementById("lesson-"+unlockedIndex);
        if(last){
            const btn=last.querySelector(".unlock-once");
            if(btn) btn.style.display="inline-flex";
        }
    }

    checks.forEach(chk=>{
        const i=parseInt(chk.dataset.check);
        chk.checked=i<=unlockedIndex;
    });

    updateProgress();
}

function updateProgress(){
    const total=tabs.length-1;
    const percent=total>0
        ? Math.round((unlockedIndex+1)/total*100)
        : 0;

    document.querySelector(".progress-bar").style.width=percent+"%";
    document.querySelector(".progress-text").textContent=percent+"%";
}

continueBtns.forEach(btn=>{
    btn.addEventListener("click",(e)=>{
        e.preventDefault();

        const next=parseInt(btn.dataset.next);
        unlockedIndex=next;

        render();

        setTimeout(()=>{
            scrollToSection(next);
        },150);
    });
});

tabs.forEach(tab=>{
    tab.addEventListener("click",(e)=>{
        e.preventDefault();

        const idx=tab.dataset.index;

        if(idx==="overview"){
            render();
            setTimeout(()=>{
                scrollToSection("overview");
            },100);
            return;
        }

        const lessonIndex=parseInt(idx);
        if(lessonIndex>unlockedIndex) return;

        render();
        setTimeout(()=>{
            scrollToSection(idx);
        },100);
    });
});

document.querySelectorAll(".open-inline").forEach(btn=>{
    btn.addEventListener("click",function(){
        const url=this.dataset.url;
        const viewer=this.nextElementSibling;
        const ext=url.split('.').pop().toLowerCase();

        if(viewer.classList.contains("show")){
            viewer.classList.remove("show");
            viewer.innerHTML="";
            return;
        }

        if(ext==="pdf"){
            viewer.innerHTML =
                `<iframe src="${url}#toolbar=0"
                style="width:100%; height:70vh; border:none;"></iframe>`;
        }else{
            viewer.innerHTML = `<img src="${url}">`;
        }

        viewer.classList.add("show");
        viewer.scrollIntoView({behavior:"smooth",block:"center"});
    });
});

document.querySelectorAll(".lesson-video").forEach(video=>{
    video.autoplay=false;
    video.addEventListener("play",()=>{
        document.querySelectorAll("video").forEach(v=>{
            if(v!==video) v.pause();
        });
    });
});

render();
setActiveTab("overview");
</script>
</body>

</html>
